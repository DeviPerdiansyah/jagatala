<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Helpers\BusinessDayHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    // Limit Max Kas Fisik Tunai Per Hari (Rp 2.000.000)
    protected $dailyCashLimit = 2000000;

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'pengelola') {
            // 1. Data yang masih pending (perlu persetujuan)
            $pendingWithdrawals = Withdrawal::with('user')
                ->where('status', 'pending')
                ->latest()
                ->get();

            // 2. Data riwayat yang sudah selesai (disetujui / ditolak) + Pagination
            $completedWithdrawals = Withdrawal::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->paginate(10);

            // 3. Rekap proyeksi harian
            $dailyProjections = Withdrawal::with('user')
                ->where('status', 'pending')
                ->orderBy('pickup_date', 'asc')
                ->get()
                ->groupBy(function($item) {
                    return $item->pickup_date ? $item->pickup_date->format('Y-m-d') : $item->created_at->format('Y-m-d');
                });

            return view('withdrawals.index', compact('pendingWithdrawals', 'completedWithdrawals', 'dailyProjections'));
        } else {
            // Untuk Nasabah
            $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(10);
            return view('withdrawals.index', compact('withdrawals'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:cash,transfer',
            'payment_detail' => 'required_if:payment_method,transfer|nullable|string|max:255',
        ]);

        $user = auth()->user();

        // Restriksi 1x Penarikan per 7 Hari
        $lastWithdrawal = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->first();

        if ($lastWithdrawal) {
            $nextAllowedDate = $lastWithdrawal->created_at->addDays(7)->format('d M Y, H:i');
            return back()->with('error', "Anda hanya dapat melakukan penarikan 1 kali dalam seminggu. Anda bisa mengajukan kembali pada: {$nextAllowedDate}");
        }

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Saldo Anda tidak mencukupi untuk penarikan ini.');
        }

        // Kalkulasi Hari Kerja (Skip Weekend)
        $pickupDate = BusinessDayHelper::getNextBusinessDay(3);

        // FITUR LIMIT KAS HARIAN: Cek kuota kas tunai hari H
        if ($request->payment_method === 'cash') {
            $currentTotalOnDate = Withdrawal::where('payment_method', 'cash')
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('pickup_date', $pickupDate)
                ->sum('amount');

            // Jika melebihi limit, undur ke hari kerja berikutnya
            if (($currentTotalOnDate + $request->amount) > $this->dailyCashLimit) {
                $pickupDate->addDay();
                if ($pickupDate->isWeekend()) {
                    $pickupDate = BusinessDayHelper::getNextBusinessDay(1);
                }
            }
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_detail' => $request->payment_method === 'transfer' ? $request->payment_detail : null,
            'status' => 'pending',
            'pickup_date' => $pickupDate,
        ]);

        return back()->with('success', 'Pengajuan berhasil dibuat! Estimasi pencairan dana pada tanggal ' . $pickupDate->format('d M Y'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Status penarikan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $withdrawal) {
            if ($request->action === 'approve') {
                if ($withdrawal->user->balance < $withdrawal->amount) {
                    throw new \Exception('Saldo nasabah tidak mencukupi.');
                }

                $withdrawal->user->decrement('balance', $withdrawal->amount);
                $withdrawal->update(['status' => 'approved']);

            } else {
                $withdrawal->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }
        });

        if ($request->action === 'approve') {
            return back()
                ->with('success', 'Penarikan telah berhasil disetujui.')
                ->with('status_popup', 'approved');
        } else {
            return back()
                ->with('success', 'Penarikan telah ditolak.')
                ->with('status_popup', 'rejected')
                ->with('rejection_reason', $request->rejection_reason);
        }
    }

    // Eksekusi Tanda Tangan Digital & Serah Terima Uang Fisik
    public function completeFulfillment(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Pencairan dana harus disetujui terlebih dahulu.');
        }

        $withdrawal->update([
            'completed_at' => now(),
            'signature' => $request->signature,
        ]);

        return back()->with('success', 'Serah terima uang fisik berhasil diselesaikan dan tanda tangan tersimpan!');
    }

    // Cetak Lembar Rekap Kas Fisik Harian
    public function printDailyCashProjection(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $items = Withdrawal::with('user')
            ->where('payment_method', 'cash')
            ->where('status', 'pending')
            ->whereDate('pickup_date', $date)
            ->get();

        return view('withdrawals.print_projection', compact('items', 'date'));
    }

    public function printReceipt(Withdrawal $withdrawal)
    {
        $user = auth()->user();
        $withdrawal->load('user');

        if ($user->role !== 'pengelola' && $withdrawal->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Kuitansi hanya tersedia untuk penarikan yang telah disetujui.');
        }

        return view('withdrawals.receipt', compact('withdrawal'));
    }
}