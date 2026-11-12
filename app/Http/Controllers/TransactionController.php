<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Menampilkan riwayat transaksi (Untuk Pengelola & Nasabah)
    public function index(Request $request)
    {
        // Tambahkan 'details.category' pada relasi with
        $query = Transaction::with(['user', 'category', 'details.category']);

        // Jika Login sebagai Nasabah, tampilkan data nasabah itu saja
        if (auth()->user()->role === 'nasabah') {
            $query->where('user_id', auth()->id());
        }

        // Filter Nama Nasabah (Khusus Pengelola)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Tanggal Spesifik
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter Bulan
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter Tahun
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter Kategori Sampah
        if ($request->filled('category_id')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Hitung Ringkasan Total Akumulasi (Sesuai Filter)
        $totalSummary = [
            'weight' => (clone $query)->sum('weight'),
            'amount' => (clone $query)->sum('total_amount'),
        ];

        // Urutkan dari transaksi terbaru & batasi 20 data per halaman
        $transactions = $query->latest()->paginate(20);

        // Ambil semua daftar kategori sampah untuk dropdown filter
        $categories = Category::orderBy('name', 'asc')->get();

        return view('transactions.index', compact('transactions', 'totalSummary', 'categories'));
    }

    // Halaman Form Input Setoran (Khusus Pengelola)
    public function create()
    {
        $nasabahs = User::where('role', 'nasabah')->get();
        $categories = Category::all();

        return view('transactions.create', compact('nasabahs', 'categories'));
    }

    // Proses Simpan Setoran & Auto Update Saldo
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.weight' => 'required|numeric|min:0.01',
        ]);

        $createdTransaction = null;

        DB::transaction(function () use ($request, &$createdTransaction) {
            $grandTotal = 0;
            $totalWeight = 0;

            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'category_id' => $request->items[0]['category_id'], // fallback kategori utama
                'weight' => 0,
                'price_per_kg' => 0,
                'total_amount' => 0,
            ]);

            // 2. Simpan Setiap Detail Sampah
            foreach ($request->items as $item) {
                $category = Category::findOrFail($item['category_id']);
                $subtotal = $item['weight'] * $category->price_per_kg;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'category_id' => $category->id,
                    'weight' => $item['weight'], // Berisi jumlah kg atau jumlah pcs
                    'unit' => $category->unit ?? 'kg', // Menyimpan satuan ('kg' atau 'pcs')
                    'price_per_kg' => $category->price_per_kg,
                    'subtotal' => $subtotal,
                ]);

                $grandTotal += $subtotal;
                // Selalu akumulasikan jumlah item/berat ke total header tanpa filter satuan
                $totalWeight += $item['weight'];
            }

            // 3. Update Total Header
            $transaction->update([
                'weight' => $totalWeight,
                'total_amount' => $grandTotal,
            ]);

            // 4. Tambah Saldo Nasabah
            $nasabah = User::findOrFail($request->user_id);
            $nasabah->increment('balance', $grandTotal);

            // Load relasi untuk keperluan struk
            $transaction->load(['user', 'details.category']);
            $createdTransaction = $transaction;
        });

        // Format data payload untuk dikirim ke view struk modal
        $firstDetail = $createdTransaction->details->first();
        $unitText = ($firstDetail && strtolower($firstDetail->unit ?? $firstDetail->category->unit ?? 'kg') === 'pcs') ? 'Pcs' : 'Kg';

        $detailsPayload = $createdTransaction->details->map(function ($d) {
            $u = strtolower($d->unit ?? $d->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
            return [
                'name' => $d->category->name ?? 'Sampah',
                'weight' => number_format($d->weight, 1, ',', '.'),
                'unit' => $u,
                'price' => 'Rp ' . number_format($d->price_per_kg, 0, ',', '.'),
                'subtotal' => 'Rp ' . number_format($d->subtotal, 0, ',', '.')
            ];
        });

        return redirect()->route('transactions.index')->with([
            'success' => 'Setoran multi-item berhasil dicatat & saldo nasabah telah bertambah!',
            'show_receipt' => [
                'id' => 'TRX-' . str_pad($createdTransaction->id, 5, '0', STR_PAD_LEFT),
                'date' => $createdTransaction->created_at->format('d M Y, H:i') . ' WIB',
                'nasabah' => $createdTransaction->user->name ?? '-',
                'details' => $detailsPayload,
                'weight' => number_format($createdTransaction->weight, 1, ',', '.') . ' ' . $unitText,
                'total' => '+ Rp ' . number_format($createdTransaction->total_amount, 0, ',', '.')
            ]
        ]);
    }

    // 1. Tampilan Cetak PDF / Print Laporan
    public function printReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with(['user', 'category']);

        // Filter berdasarkan tanggal jika diisi
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Jika nasabah, hanya cetak miliknya sendiri
        if (auth()->user()->role === 'nasabah') {
            $query->where('user_id', auth()->user()->id);
        }

        $transactions = $query->latest()->get();
        $totalWeight = $transactions->sum('weight');
        $totalAmount = $transactions->sum('total_amount');

        return view('transactions.report_pdf', compact('transactions', 'startDate', 'endDate', 'totalWeight', 'totalAmount'));
    }

    // 2. Export ke File Excel (.csv)
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with(['user', 'category']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if (auth()->user()->role === 'nasabah') {
            $query->where('user_id', auth()->user()->id);
        }

        $transactions = $query->latest()->get();
        $fileName = 'Laporan_Transaksi_Jagatala_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['No', 'Tanggal', 'Nama Nasabah', 'Jenis Sampah', 'Kategori', 'Berat (Kg)', 'Harga/Kg (Rp)', 'Total (Rp)']);

            $no = 1;
            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $no++,
                    $trx->created_at->format('d-m-Y H:i'),
                    $trx->user->name ?? '-',
                    $trx->category->name ?? '-',
                    $trx->category->type ?? 'Anorganik',
                    $trx->weight,
                    $trx->price_per_kg,
                    $trx->total_amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}