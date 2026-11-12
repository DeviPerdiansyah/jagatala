@extends('layouts.app')

@section('title', auth()->user()->role === 'pengelola' ? 'Dashboard Admin - Bank Sampah Jagatala' : 'Dashboard Nasabah - Bank Sampah Jagatala')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- BANNER PERINGATAN MAINTENANCE HARGA (REAL-TIME) -->
    <div id="dashboardMaintenanceBanner" class="hidden bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 text-white p-4 rounded-2xl shadow-lg border border-amber-400 flex items-center justify-between animate-pulse">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl shrink-0">
                ⚠️
            </div>
            <div>
                <h4 class="font-black text-sm uppercase tracking-wider">Pembaruan Harga Sedang Berlangsung</h4>
                <p class="text-xs text-amber-100 mt-0.5">Pengelola sedang menyesuaikan patokan harga sampah. Layanan transaksi ditangguhkan sementara.</p>
            </div>
        </div>
        <span class="bg-amber-950/40 text-white text-[10px] font-extrabold px-3 py-1.5 rounded-xl uppercase tracking-wider border border-amber-300/30">Mode Kunci</span>
    </div>

    <!-- HEADER DASHBOARD EXECUTIVE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ auth()->user()->role === 'pengelola' ? 'Executive Dashboard' : 'Dashboard Nasabah' }}
                </h1>
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider {{ auth()->user()->role === 'pengelola' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-200 text-slate-700' }} rounded-full">
                    {{ auth()->user()->role === 'pengelola' ? 'Pengelola Core' : 'Nasabah VIP' }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Selamat datang kembali, <span class="font-bold text-slate-800">{{ auth()->user()->name }}</span>! Ringkasan operasional real-time hari ini.
            </p>
        </div>
        
        <!-- Live Sync Indicator Badge -->
        <div class="flex items-center gap-2.5 bg-white border border-emerald-200/80 px-4 py-2 rounded-2xl shadow-sm w-fit">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wider">Live System Active</span>
        </div>
    </div>

    @if(auth()->user()->role === 'pengelola')
    <!-- ========================================== -->
    <!--           TAMPILAN KHUSUS PENGELOLA        -->
    <!-- ========================================== -->

    <!-- PANEL AKSI Cepat PENGELOLA -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('transactions.create') }}" class="group relative overflow-hidden bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-between border border-emerald-500/30">
            <div class="z-10">
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-100">Pencatatan</div>
                <div class="text-sm font-black mt-0.5 group-hover:translate-x-1 transition duration-150">➕ Input Setoran Baru</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-lg z-10 transition">📥</div>
        </a>

        <a href="{{ route('withdrawals.index') }}" class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-between border border-amber-400/30">
            <div class="z-10">
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-amber-100">Keuangan</div>
                <div class="text-sm font-black mt-0.5 group-hover:translate-x-1 transition duration-150">💸 Konfirmasi Penarikan</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-lg z-10 transition">⌛</div>
        </a>

        <a href="{{ route('categories.index') }}" class="group relative overflow-hidden bg-gradient-to-r from-slate-800 to-slate-900 text-white p-4 rounded-2xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-between border border-slate-700/50">
            <div class="z-10">
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-300">Master Data</div>
                <div class="text-sm font-black mt-0.5 group-hover:translate-x-1 transition duration-150">🏷️ Kelola Harga Sampah</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-lg z-10 transition">⚙️</div>
        </a>
    </div>

    <!-- KARTU STATISTIK UTAMA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Nasabah -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between z-10">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Nasabah</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition">👥</div>
            </div>
            <div class="mt-4 z-10">
                <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-nasabah">0</div>
                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">
                    <span>✓ Active Members</span>
                </span>
            </div>
        </div>

        <!-- Total Sampah -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between z-10">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Sampah Terkumpul</span>
                <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition">⚖️</div>
            </div>
            <div class="mt-4 z-10">
                <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-sampah">0</div>
                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-extrabold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-md">
                    <span>♻️ Terdaur Ulang</span>
                </span>
            </div>
        </div>

        <!-- Kas Nasabah -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between z-10">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Kas Tabungan</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition">💰</div>
            </div>
            <div class="mt-4 z-10">
                <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-kas">Rp 0</div>
                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-extrabold text-sky-700 bg-sky-50 border border-sky-100 px-2 py-0.5 rounded-md">
                    <span>💎 Liabilitas Nasabah</span>
                </span>
            </div>
        </div>

        <!-- PENARIKAN PENDING -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-300 transition duration-200 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between z-10">
                <span class="text-[11px] font-extrabold text-amber-600/90 uppercase tracking-wider">Perlu Disiapkan</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition">⌛</div>
            </div>
            <div class="mt-3 z-10">
                <div class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight" id="stat-pending-amount">Rp 0</div>
                
                <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2">
                    <span class="text-[10px] font-extrabold text-slate-500">
                        <strong class="font-black text-amber-700" id="stat-pending">0</strong> Pengajuan Pending
                    </span>
                    <a href="{{ route('withdrawals.index') }}" class="text-[10px] font-extrabold text-amber-700 hover:text-amber-900 underline flex items-center gap-0.5">
                        Proses →
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- ANALYTICS CHARTS SECTION WITH PERIOD SWITCHER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Trend (Area/Bar) -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <h3 class="text-base font-extrabold text-slate-900">Trend Setoran Sampah & Total Uang</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        <span class="font-bold text-emerald-600">Batang Hijau</span> = Volume Sampah (Kg/Pcs) &nbsp;|&nbsp; 
                        <span class="font-bold text-sky-600">Garis Biru</span> = Nilai Rupiah (Rp)
                    </p>
                </div>

                <!-- FILTER PERIODE WAKTU (7 HARI / 30 HARI) -->
                <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-slate-200/60 w-fit">
                    <button id="btn-period-7" onclick="changePeriod(7)" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 bg-white text-emerald-700 shadow-sm">
                        7 Hari
                    </button>
                    <button id="btn-period-30" onclick="changePeriod(30)" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 text-slate-500 hover:text-slate-800">
                        30 Hari
                    </button>
                </div>
            </div>
            <div id="wahAreaChart" class="w-full"></div>
        </div>

        <!-- Donut Chart (Komposisi Kategori) -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    <h3 class="text-base font-extrabold text-slate-900">Komposisi Sampah</h3>
                </div>
                <p class="text-xs text-slate-400" id="donut-period-subtitle">Persentase kategori sampah 7 hari terakhir</p>
            </div>
            
            <div id="wahDonutChart" class="my-auto py-3"></div>

            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <div class="text-[10px] text-slate-400 font-extrabold uppercase">Terbanyak</div>
                    <div class="text-xs font-black text-emerald-600 mt-0.5 truncate" id="top-category-text">Memuat...</div>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <div class="text-[10px] text-slate-400 font-extrabold uppercase">Total Kategori</div>
                    <div class="text-xs font-black text-slate-800 mt-0.5" id="total-categories-text">Memuat...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD EVALUASI & ANALISIS DINAMIS -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-700/80 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between gap-4 border-b border-slate-700/60 pb-4 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-xl font-bold">
                    💡
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white" id="eval-title-text">Evaluasi & Analisis Operasional (7 Hari Terakhir)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Laporan dinamis otomatis berdasarkan tren transaksi warga</p>
                </div>
            </div>
            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-extrabold px-3 py-1 rounded-xl uppercase tracking-wider hidden sm:inline-block">
                Auto-Generated Insight
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ringkasan Transaksi -->
            <div class="bg-slate-800/60 border border-slate-700/50 p-4 sm:p-5 rounded-2xl">
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">
                    <span>📊</span>
                    <span>Ringkasan Transaksi & Tren</span>
                </div>
                <div class="text-xs text-slate-300 leading-relaxed" id="eval-summary-text">
                    Membuat analisa data...
                </div>
            </div>

            <!-- Rekomendasi Tindakan -->
            <div class="bg-slate-800/60 border border-slate-700/50 p-4 sm:p-5 rounded-2xl">
                <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider mb-2">
                    <span>🎯</span>
                    <span>Rekomendasi Peningkatan Kesadaran Warga</span>
                </div>
                <div class="text-xs text-slate-300 leading-relaxed space-y-1" id="eval-recommendation-text">
                    Menyusun rekomendasi strategi pengelola...
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- ========================================== -->
    <!--            TAMPILAN KHUSUS NASABAH         -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        <div class="lg:col-span-1 bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-600 rounded-3xl p-6 text-white shadow-xl shadow-emerald-900/20 relative overflow-hidden flex flex-col justify-between min-h-[220px]">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-32 h-32 bg-emerald-400/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="flex justify-between items-center z-10">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center font-black text-sm">J</div>
                    <span class="font-extrabold tracking-wider text-xs uppercase text-emerald-200">Jagatala Pay</span>
                </div>
                <span class="text-[10px] bg-emerald-500/30 border border-emerald-300/30 px-2.5 py-1 rounded-full font-bold uppercase tracking-widest text-emerald-100 backdrop-blur-md">
                    Nasabah VIP
                </span>
            </div>

            <div class="my-4 z-10">
                <p class="text-[11px] font-medium text-emerald-200/80 uppercase tracking-wider">Total Saldo Tabungan</p>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white mt-1" id="nasabah-balance">
                    Rp 0
                </h2>
            </div>

            <div class="pt-3 border-t border-white/15 flex items-center gap-2 z-10">
                <a href="{{ route('withdrawals.index') }}" 
                   class="flex-1 py-2 px-3 bg-white text-emerald-900 hover:bg-emerald-50 text-xs font-extrabold rounded-xl text-center shadow-md transition duration-150 active:scale-95">
                    💸 Tarik Saldo
                </a>
                <a href="{{ route('transactions.index') }}" 
                   class="flex-1 py-2 px-3 bg-white/15 hover:bg-white/25 text-white text-xs font-extrabold rounded-xl text-center backdrop-blur-md transition duration-150 border border-white/20">
                    📜 Riwayat
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Sampah Disetor</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-2" id="nasabah-sampah">
                            0
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center text-xl shadow-sm">
                        ⚖️
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-[11px] font-semibold text-emerald-600">
                    <span>🌱 Kontribusi nyata menjaga kebersihan bumi</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Setoran Saya</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-2" id="nasabah-transaksi">
                            0
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
                        📦
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-[11px] font-semibold text-slate-500">
                    <span>Frekuensi penyetoran sampah yang berhasil</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SECTION TABEL TRANSAKSI & KATALOG HARGA -->
    <div class="grid grid-cols-1 {{ auth()->user()->role === 'nasabah' ? 'lg:grid-cols-3' : '' }} gap-6">
        
        <div class="{{ auth()->user()->role === 'nasabah' ? 'lg:col-span-2' : '' }} bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Aktivitas Transaksi Terbaru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ auth()->user()->role === 'pengelola' ? '5 penyetoran sampah paling akhir dari seluruh nasabah' : '5 riwayat setoran sampah terbaru Anda' }}
                        </p>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs rounded-xl transition">
                        Lihat Semua →
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="py-3.5 px-6">Waktu</th>
                                @if(auth()->user()->role === 'pengelola')
                                    <th class="py-3.5 px-6">Nasabah</th>
                                @endif
                                <th class="py-3.5 px-6">Jenis Sampah</th>
                                <th class="py-3.5 px-6">Jumlah / Berat</th>
                                <th class="py-3.5 px-6">Nilai Total</th>
                            </tr>
                        </thead>
                        <tbody id="realtime-transactions-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'pengelola' ? '5' : '4' }}" class="py-8 text-center text-xs text-slate-400">
                                    Memuat data transaksi...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'nasabah')
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm">🏷️</span>
                            <h2 class="text-base font-extrabold text-slate-900">Katalog Harga Sampah</h2>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Patokan harga resmi terkini dari pengelola</p>
                    </div>
                </div>

                <div class="max-h-[280px] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="py-3 px-5">Jenis Sampah</th>
                                <th class="py-3 px-5 text-right">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody id="realtime-pricelist-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            <tr>
                                <td colspan="2" class="py-8 text-center text-xs text-slate-400">
                                    Memuat patokan harga...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="p-3.5 bg-slate-50/80 border-t border-slate-100 text-[10.5px] text-slate-400 text-center font-medium">
                ⚡ *Harga disinkronkan secara otomatis oleh Admin
            </div>
        </div>
        @endif

    </div>

</div>

<!-- SCRIPT APEXCHARTS & REAL-TIME FETCH DATA -->
<script>
    let chartArea = null;
    let chartDonut = null;
    let selectedDays = 7; // Default 7 hari
    let fetchInterval = null;
    const userRole = "{{ auth()->user()->role }}";

    document.addEventListener("DOMContentLoaded", function () {
        if (userRole === 'pengelola') {
            const optionsArea = {
                series: [{
                    name: 'Jumlah / Berat Sampah',
                    type: 'column',
                    data: []
                }, {
                    name: 'Nilai Uang (Rp)',
                    type: 'line',
                    data: []
                }],
                chart: { height: 320, type: 'line', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                colors: ['#10b981', '#0284c7'],
                stroke: { width: [0, 3.5], curve: 'smooth' },
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 6 } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: [],
                    axisBorder: { show: false }, axisTicks: { show: false },
                    labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 } }
                },
                yaxis: [{
                    title: { text: "Jumlah / Berat", style: { color: '#10b981', fontSize: '11px', fontWeight: 700 } },
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' }, formatter: (val) => val }
                }, {
                    opposite: true,
                    title: { text: "Nilai Uang (Rp)", style: { color: '#0284c7', fontSize: '11px', fontWeight: 700 } },
                    labels: { 
                        style: { colors: '#94a3b8', fontSize: '11px' },
                        formatter: function (val) {
                            if (val >= 1000000) return "Rp " + (val / 1000000).toFixed(1) + "jt";
                            if (val >= 1000) return "Rp " + (val / 1000).toFixed(0) + "rb";
                            if (val > 0) return "Rp " + val;
                            return "Rp 0";
                        }
                    }
                }],
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', fontWeight: 700 }
            };

            chartArea = new ApexCharts(document.querySelector("#wahAreaChart"), optionsArea);
            chartArea.render();

            const optionsDonut = {
                series: [1], labels: ['Memuat...'],
                chart: { height: 260, type: 'donut', fontFamily: 'Inter, sans-serif' },
                colors: ['#059669', '#10b981', '#34d399', '#38bdf8', '#818cf8', '#f59e0b'],
                plotOptions: { pie: { donut: { size: '74%', labels: { show: true, total: { show: true, label: 'Kategori', fontSize: '11px', color: '#94a3b8' } } } } },
                dataLabels: { enabled: false }, legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }, stroke: { show: false }
            };

            chartDonut = new ApexCharts(document.querySelector("#wahDonutChart"), optionsDonut);
            chartDonut.render();
        }

        fetchDashboardStats(selectedDays);
        fetchInterval = setInterval(() => fetchDashboardStats(selectedDays), 5000);
    });

    function changePeriod(days) {
        selectedDays = days;

        // Visual State Filter Button
        const btn7 = document.getElementById('btn-period-7');
        const btn30 = document.getElementById('btn-period-30');

        if (days === 30) {
            btn30.className = "px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 bg-white text-emerald-700 shadow-sm";
            btn7.className = "px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 text-slate-500 hover:text-slate-800";
        } else {
            btn7.className = "px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 bg-white text-emerald-700 shadow-sm";
            btn30.className = "px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-200 text-slate-500 hover:text-slate-800";
        }

        // Update Subtitle & Label
        const donutSubtitle = document.getElementById('donut-period-subtitle');
        if(donutSubtitle) donutSubtitle.innerText = `Persentase kategori sampah ${days} hari terakhir`;

        const evalTitle = document.getElementById('eval-title-text');
        if(evalTitle) evalTitle.innerText = `Evaluasi & Analisis Operasional (${days} Hari Terakhir)`;

        fetchDashboardStats(selectedDays);
    }

    async function fetchDashboardStats(days = 7) {
        try {
            const response = await fetch(`{{ route('dashboard.stats') }}?days=${days}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            const banner = document.getElementById('dashboardMaintenanceBanner');
            if (banner) {
                if (data.is_maintenance) banner.classList.remove('hidden');
                else banner.classList.add('hidden');
            }

            if (data.role === 'pengelola') {
                if(document.getElementById('stat-nasabah')) document.getElementById('stat-nasabah').innerText = data.stats.total_nasabah;
                if(document.getElementById('stat-sampah')) document.getElementById('stat-sampah').innerText = data.stats.total_sampah;
                if(document.getElementById('stat-kas')) document.getElementById('stat-kas').innerText = data.stats.total_kas_nasabah;
                if(document.getElementById('stat-pending')) document.getElementById('stat-pending').innerText = data.stats.pending_withdrawals;
                if(document.getElementById('stat-pending-amount')) document.getElementById('stat-pending-amount').innerText = data.stats.pending_withdrawals_amount;

                if (chartArea && data.chart_trend) {
                    chartArea.updateOptions({ 
                        xaxis: { categories: data.chart_trend.categories },
                        plotOptions: { bar: { columnWidth: days === 30 ? '60%' : '45%' } }
                    });
                    chartArea.updateSeries([
                        { name: 'Jumlah / Berat Sampah', data: data.chart_trend.weights },
                        { name: 'Nilai Uang (Rp)', data: data.chart_trend.amounts }
                    ]);
                }

                if (chartDonut && data.chart_donut) {
                    chartDonut.updateOptions({ labels: data.chart_donut.labels });
                    chartDonut.updateSeries(data.chart_donut.series);
                    if(document.getElementById('top-category-text')) document.getElementById('top-category-text').innerText = data.chart_donut.top_category;
                    if(document.getElementById('total-categories-text')) document.getElementById('total-categories-text').innerText = data.chart_donut.total_categories + ' Jenis';
                }

                // Render Evaluasi Otomatis
                if (data.evaluation) {
                    if (document.getElementById('eval-summary-text')) {
                        document.getElementById('eval-summary-text').innerHTML = data.evaluation.summary;
                    }
                    if (document.getElementById('eval-recommendation-text')) {
                        document.getElementById('eval-recommendation-text').innerHTML = data.evaluation.recommendation;
                    }
                }

                renderTransactionsTable(data.latest_transactions, true);

            } else if (data.role === 'nasabah') {
                if(document.getElementById('nasabah-balance')) document.getElementById('nasabah-balance').innerText = data.stats.balance;
                if(document.getElementById('nasabah-sampah')) document.getElementById('nasabah-sampah').innerText = data.stats.total_sampah;
                if(document.getElementById('nasabah-transaksi')) document.getElementById('nasabah-transaksi').innerText = data.stats.total_transaksi;

                renderTransactionsTable(data.latest_transactions, false);
                renderPriceListTable(data.price_list);
            }
        } catch (error) {
            console.error('Gagal memuat data realtime:', error);
        }
    }

    function renderTransactionsTable(transactions, isPengelola) {
        let rowsHtml = '';
        const colSpan = isPengelola ? 5 : 4;

        if (!transactions || transactions.length === 0) {
            rowsHtml = `<tr><td colspan="${colSpan}" class="py-8 text-center text-xs text-slate-400">Belum ada riwayat transaksi.</td></tr>`;
        } else {
            transactions.forEach(trx => {
                const initial = trx.nasabah ? trx.nasabah.charAt(0).toUpperCase() : 'N';
                const unitLabel = trx.unit ? trx.unit : 'Kg';
                rowsHtml += `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-6 text-slate-500 whitespace-nowrap">${trx.date}</td>
                        ${isPengelola ? `
                            <td class="py-3.5 px-6 font-bold text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold flex items-center justify-center">
                                        ${initial}
                                    </div>
                                    <span>${trx.nasabah}</span>
                                </div>
                            </td>` : ''}
                        <td class="py-3.5 px-6 text-slate-600">${trx.category}</td>
                        <td class="py-3.5 px-6 font-bold text-slate-800 whitespace-nowrap">${trx.weight} ${unitLabel}</td>
                        <td class="py-3.5 px-6 font-black text-emerald-600 whitespace-nowrap">${trx.total}</td>
                    </tr>
                `;
            });
        }
        document.getElementById('realtime-transactions-body').innerHTML = rowsHtml;
    }

    function renderPriceListTable(priceList) {
        const priceBody = document.getElementById('realtime-pricelist-body');
        if (!priceBody) return;

        let rowsHtml = '';
        if (!priceList || priceList.length === 0) {
            rowsHtml = `<tr><td colspan="2" class="py-8 text-center text-xs text-slate-400">Belum ada data harga sampah.</td></tr>`;
        } else {
            priceList.forEach(item => {
                rowsHtml += `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-5 font-bold text-slate-800">${item.name}</td>
                        <td class="py-3 px-5 text-right font-black text-emerald-600 whitespace-nowrap">${item.price}</td>
                    </tr>
                `;
            });
        }
        priceBody.innerHTML = rowsHtml;
    }
</script>
@endsection