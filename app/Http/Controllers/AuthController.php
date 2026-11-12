<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // CEK STATUS AKUN (Khusus Nasabah)
            if ($user->role === 'nasabah' && $user->status !== 'active') {
                Auth::logout();
                
                if ($user->status === 'pending') {
                    return back()->with('error', 'Akun Anda sedang dalam proses verifikasi domisili oleh pengelola. Harap tunggu.');
                } else {
                    return back()->with('error', 'Pendaftaran Anda ditolak oleh pengelola karena tidak memenuhi kriteria domisili.');
                }
            }

            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'required|string|max:15',
            'birth_date'     => 'required|date',
            'account_number' => 'required|string|max:50',
            'address'        => 'required|string',
            'city'           => 'required|string|max:100', // <--- Validasi Kota/Kabupaten
            'rt_rw'          => 'required|string|max:20',
            'password'       => 'required|min:8|confirmed',
        ]);

        // Buat Nasabah Baru dengan status 'pending'
        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'birth_date'     => $request->birth_date,
            'account_number' => $request->account_number,
            'address'        => $request->address,
            'city'           => $request->city,           // <--- Simpan Kota/Kabupaten
            'rt_rw'          => $request->rt_rw,
            'password'       => Hash::make($request->password),
            'role'           => 'nasabah',
            'status'         => 'pending', // Wajib di-approve pengelola
            'balance'        => 0,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang diverifikasi oleh Pengelola. Silakan tunggu konfirmasi.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}