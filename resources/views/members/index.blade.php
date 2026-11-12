@extends('layouts.app')

@section('title', 'Kelola & Verifikasi Nasabah - Bank Sampah Jagatala')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- HEADER & TITLE EXECUTIVE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Kelola & Verifikasi Nasabah
                </h1>
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full">
                    Directory
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola verifikasi pendaftaran, status akun, dan data kontak nasabah secara real-time.
            </p>
        </div>

        <!-- Total Member Counter Badge -->
        <div class="flex items-center gap-2.5 bg-white border border-slate-200/80 px-4 py-2 rounded-2xl shadow-sm w-fit">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold text-sm">
                👥
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Terdaftar</div>
                <div class="text-sm font-black text-slate-800">{{ $members->total() }} Nasabah</div>
            </div>
        </div>
    </div>

    <!-- FILTER FORM -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        <form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            
            <!-- Filter Cari -->
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Nasabah</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / WA / Kota..." 
                        class="w-full pl-8 pr-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    <span class="absolute left-2.5 top-2.5 text-xs text-slate-400">🔍</span>
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Status Akun</label>
                <select name="status" class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ Aktif / Disetujui</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>💤 Non-Aktif (Inaktif)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>

            <!-- Filter Bulan -->
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Bulan Registrasi</label>
                <select name="month" class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Tahun Registrasi</label>
                <select name="year" class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                    <option value="">-- Semua Tahun --</option>
                    @foreach(range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl transition shadow-md shadow-emerald-600/20 active:scale-95">
                    Filter Data
                </button>
                <a href="{{ route('members.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition border border-slate-200/60" title="Reset Filter">
                    🔄
                </a>
            </div>

        </form>
    </div>

    <!-- TABEL DATA NASABAH -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="max-h-[460px] overflow-y-auto relative scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-slate-50/95 backdrop-blur-md z-10 border-b border-slate-200/80 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="py-4 px-6">Tanggal Registrasi</th>
                        <th class="py-4 px-6">Profil Nasabah</th>
                        <th class="py-4 px-6">Alamat Domisili</th>
                        <th class="py-4 px-6">Saldo Tabungan</th>
                        <th class="py-4 px-6 text-center">Status Verifikasi</th>
                        <th class="py-4 px-6 text-center">Aksi Pengelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50/80 transition group">
                            <!-- Tgl Daftar -->
                            <td class="py-4 px-6 text-slate-500 whitespace-nowrap">
                                <div class="font-bold text-slate-700">{{ $member->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $member->created_at->format('H:i') }} WIB</div>
                            </td>

                            <!-- Profil & WA -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-black text-xs flex items-center justify-center shadow-md shadow-emerald-600/10">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 group-hover:text-emerald-700 transition">{{ $member->name }}</div>
                                        @if($member->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 text-[10.5px] text-emerald-600 font-bold hover:underline mt-0.5 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100/60">
                                                💬 WA: {{ $member->phone }}
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">No WA (-)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Alamat Domisili -->
                            <td class="py-4 px-6 text-slate-600 max-w-xs">
                                <div class="font-bold text-slate-800">{{ $member->address ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                    @if($member->city)
                                        <span class="font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100/80">
                                            {{ $member->city }}
                                        </span>
                                    @endif
                                    @if($member->rt_rw)
                                        <span class="text-slate-400">({{ $member->rt_rw }})</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Saldo -->
                            <td class="py-4 px-6 font-black text-slate-900 whitespace-nowrap">
                                <span class="text-emerald-600">Rp</span> {{ number_format($member->balance, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($member->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif($member->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @elseif($member->status === 'inactive')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-300 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Non-Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Pengelola -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($member->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Setujui -->
                                        <form action="{{ route('members.update_status', $member->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" 
                                                onclick="confirmAction(event, 'Setujui pendaftaran {{ $member->name }}?', 'Ya, Setujui!')" 
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black rounded-xl transition shadow-md shadow-emerald-600/20 active:scale-95 flex items-center gap-1">
                                                <span>✓</span> Setujui
                                            </button>
                                        </form>

                                        <!-- Tolak -->
                                        <form action="{{ route('members.update_status', $member->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" 
                                                onclick="confirmAction(event, 'Tolak pendaftaran {{ $member->name }}?', 'Ya, Tolak!')" 
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-black rounded-xl transition active:scale-95 flex items-center gap-1">
                                                <span>✕</span> Tolak
                                            </button>
                                        </form>
                                    </div>

                                @elseif($member->status === 'rejected')
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Aktivasi Ulang -->
                                        <form action="{{ route('members.reactivate', $member->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" 
                                                onclick="confirmAction(event, 'Aktifkan kembali pendaftaran {{ $member->name }}?', 'Ya, Aktifkan!')" 
                                                class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[10.5px] font-black rounded-xl transition flex items-center gap-1" title="Aktivasi Ulang Akun">
                                                🔄 Aktifkan
                                            </button>
                                        </form>

                                        <!-- Tombol Hapus (Memakai SweetAlert2 confirmAction) -->
                                        <form action="{{ route('members.destroy', $member->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                onclick="confirmAction(event, 'Apakah Anda yakin ingin menghapus data pendaftaran {{ $member->name }} secara permanen?', 'Ya, Hapus!')" 
                                                class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[10.5px] font-black rounded-xl transition flex items-center gap-1" title="Hapus Permanen">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>

                                @elseif($member->status === 'inactive')
                                    <!-- Tombol Aktivasi Ulang dari Non-Aktif -->
                                    <form action="{{ route('members.reactivate', $member->id) }}" method="POST" class="inline-block">
                                        @csrf @method('PATCH')
                                        <button type="submit" 
                                            onclick="confirmAction(event, 'Aktifkan kembali akun {{ $member->name }}?', 'Ya, Aktifkan!')" 
                                            class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-black rounded-xl transition flex items-center gap-1">
                                            ⚡ Aktifkan Kembali
                                        </button>
                                    </form>

                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[10.5px] font-bold rounded-xl border border-slate-200/60 inline-block">
                                        ✓ Terverifikasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">🔍</div>
                                Tidak ada data nasabah yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $members->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection