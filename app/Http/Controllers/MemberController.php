<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Menampilkan daftar semua nasabah
    public function index(Request $request)
    {
        $query = User::where('role', 'nasabah');

        // 1. Filter Cari Nama / Phone / Alamat / Kota / RT-RW
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhere('rt_rw', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Status Verifikasi (pending, active, rejected, inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Filter Bulan Pendaftaran
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // 4. Filter Tahun Pendaftaran
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $members = $query->latest()->paginate(5);

        return view('members.index', compact('members'));
    }

    // Proses Setujui / Tolak Pendaftaran Domisili Nasabah
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,rejected,inactive',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        $statusMessage = $request->status === 'active' 
            ? 'Nasabah berhasil disetujui! Akun sekarang sudah bisa digunakan untuk login.' 
            : ($request->status === 'inactive' ? 'Akun nasabah berhasil dinonaktifkan.' : 'Pendaftaran nasabah telah ditolak.');

        return back()->with('success', $statusMessage);
    }

    // FITUR BARU: Aktivasi Ulang Akun Nasabah (Ditolak / Non-Aktif)
    public function reactivate(User $user)
    {
        $user->update([
            'status' => 'active',
        ]);

        return back()->with('success', "Akun nasabah {$user->name} berhasil diaktifkan kembali.");
    }

    // FITUR BARU: Hapus Permanent Akun Nasabah yang Ditolak
    public function destroy(User $user)
    {
        if ($user->status !== 'rejected') {
            return back()->with('error', 'Hanya pendaftaran nasabah yang berstatus DITOLAK yang dapat dihapus.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Data pendaftaran nasabah {$userName} berhasil dihapus dari sistem.");
    }
}