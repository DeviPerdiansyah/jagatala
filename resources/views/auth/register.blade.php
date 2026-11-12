@extends('layouts.guest')

@section('title', 'Daftar Nasabah Baru - Bank Sampah')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <!-- LOGO RESMI BANK SAMPAH JAGATALA -->
        <div class="p-2 bg-white rounded-2xl border border-emerald-100 shadow-md shadow-emerald-500/5 w-16 h-16 mx-auto mb-3 flex items-center justify-center overflow-hidden">
            <img src="{{ asset('images/logo-jagatala.png') }}" alt="Logo Bank Sampah Jagatala" class="w-full h-full object-contain">
        </div>

        <!-- NAMA BRAND & JUDUL FORM -->
        <span class="text-xs font-black text-emerald-600 uppercase tracking-widest block mb-1">
            Bank Sampah Jagatala
        </span>
        <h1 class="text-2xl font-bold text-slate-900">Formulir Pendaftaran Nasabah</h1>
        <p class="text-xs text-slate-500 mt-1">Lengkapi data domisili Anda untuk verifikasi akun</p>
    </div>

    <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
        @csrf

        <!-- NAMA LENGKAP -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- EMAIL & NO HANDPHONE -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@gmail.com"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">No. WhatsApp/HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08123456789"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- TANGGAL LAHIR -->
        <div class="space-y-1">
            <label for="birth_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tanggal Lahir</label>
            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            @error('birth_date')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- NOMOR REKENING / E-WALLET -->
        <div class="space-y-1">
            <label for="account_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Rekening / E-Wallet</label>
            <input type="text" id="account_number" name="account_number" value="{{ old('account_number') }}" placeholder="Contoh: BCA - 1234567890 / DANA - 0812345678" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            @error('account_number')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- ALAMAT LENGKAP, KOTA/KABUPATEN, DAN RT/RW -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alamat Lengkap</label>
                <input type="text" name="address" value="{{ old('address') }}" required placeholder="Jl. Mawar No. 12"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kota / Kabupaten</label>
                <input type="text" name="city" value="{{ old('city') }}" required placeholder="Contoh: Bandung"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('city') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">RT / RW</label>
                <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" required placeholder="RT 02 / RW 05"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('rt_rw') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- PASSWORD & KONFIRMASI PASSWORD -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
            Kirim Formulir Pendaftaran
        </button>
    </form>

    <p class="text-center text-xs text-slate-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:underline">Login di sini</a>
    </p>
</div>
@endsection