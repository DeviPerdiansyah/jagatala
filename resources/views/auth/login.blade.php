@extends('layouts.app')

@section('title', 'Login - Bank Sampah Jagatala')

@section('content')
<div class="max-w-md mx-auto my-10 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Masuk Akun</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola atau pantau tabungan sampah Anda</p>
    </div>

    <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
            @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Password</label>
            <input type="password" name="password" required 
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
        </div>

        <button type="submit" 
            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-sm shadow-sm transition">
            Masuk Sekarang
        </button>
    </form>

    <div class="mt-6 text-center text-xs text-slate-600">
        Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-600 font-semibold hover:underline">Daftar di sini</a>
    </div>
</div>
@endsection