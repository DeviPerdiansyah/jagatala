@extends('layouts.app')

@section('title', (auth()->user()->role === 'pengelola' ? 'Pencairan Saldo' : 'Tarik Saldo') . ' - Bank Sampah Jagatala')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- HEADER UTAMA -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-50 rounded-full blur-2xl pointer-events-none"></div>
        <div class="z-10">
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ auth()->user()->role === 'pengelola' ? 'Pencairan Saldo Nasabah' : 'Tarik Saldo Tabungan' }}
                </h1>
                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-full shadow-xs">
                    {{ auth()->user()->role === 'pengelola' ? 'Pengelola' : 'Layanan Mandiri' }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                @if(auth()->user()->role === 'pengelola')
                    Verifikasi dan eksekusi pengajuan pencairan saldo tabungan sampah dari nasabah.
                @else
                    Halo <span class="font-bold text-slate-800">{{ auth()->user()->name }}</span>! Ajukan pencairan tabungan digital Anda dengan cepat dan praktis.
                @endif
            </p>
        </div>
    </div>

    <!-- FLASH NOTIFICATION STANDAR -->
    @if(session('success') && !session('status_popup'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-900 text-xs font-bold flex items-center gap-3 shadow-xs">
            <span class="text-base">✨</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-900 text-xs font-bold flex items-center gap-3 shadow-xs">
            <span class="text-base">⚠️</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- FITUR KHUSUS PENGELOLA: RINGKASAN PROYEKSI PENCAIRAN SALDO -->
    @if(auth()->user()->role === 'pengelola')
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>📊</span> Proyeksi Pencairan Dana Harian
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar rekap kebutuhan dana tunai & kewajiban transfer per hari kerja mendatang.</p>
            </div>
            <div class="flex items-center bg-slate-100 p-1 rounded-2xl border border-slate-200/80 self-start sm:self-auto">
                <button type="button" onclick="filterProjection('all')" id="btnFilterAll" class="px-3 py-1 text-[11px] font-black rounded-xl bg-white text-slate-900 shadow-2xs transition">Semua</button>
                <button type="button" onclick="filterProjection('cash')" id="btnFilterCash" class="px-3 py-1 text-[11px] font-black rounded-xl text-slate-500 hover:text-slate-900 transition">💵 Tunai</button>
                <button type="button" onclick="filterProjection('transfer')" id="btnFilterTransfer" class="px-3 py-1 text-[11px] font-black rounded-xl text-slate-500 hover:text-slate-900 transition">🏦 Transfer</button>
            </div>
        </div>

        @if(isset($dailyProjections) && $dailyProjections->isEmpty())
            <div class="p-6 text-center text-xs text-slate-400 font-bold bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                Belum ada antrean pencairan dana yang perlu disiapkan.
            </div>
        @elseif(isset($dailyProjections))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dailyProjections as $date => $items)
                    @php
                        $cashItems = $items->where('payment_method', 'cash');
                        $transferItems = $items->where('payment_method', 'transfer');
                        $totalCash = $cashItems->sum('amount');
                        $totalTransfer = $transferItems->sum('amount');
                        $dateObj = \Carbon\Carbon::parse($date)->locale('id');
                        $formattedDate = $dateObj->translatedFormat('d F Y');
                        $dayName = strtoupper($dateObj->translatedFormat('l'));
                    @endphp
                    
                    <div class="p-4 bg-slate-50/80 hover:bg-slate-100/50 transition border border-slate-200/80 rounded-2xl space-y-3 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-2 mb-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-emerald-600 tracking-wider block">{{ $dayName }}</span>
                                    <span class="text-xs font-extrabold text-slate-900">{{ $formattedDate }}</span>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-200 text-slate-800 text-[10px] font-black rounded-lg">
                                    {{ $items->count() }} Permintaan
                                </span>
                            </div>

                            <div class="space-y-2 max-h-44 overflow-y-auto pr-1 scrollbar-thin">
                                @foreach($items as $item)
                                    <div class="p-2.5 bg-white rounded-xl border border-slate-200/60 shadow-2xs space-y-1 projection-item" data-method="{{ $item->payment_method }}">
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="font-bold text-slate-800 truncate max-w-[120px] capitalize">
                                                {{ $item->user->name ?? 'Nasabah' }}
                                            </span>
                                            <span class="font-black text-slate-900">
                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center text-[10px]">
                                            @if($item->payment_method === 'cash')
                                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded font-black border border-emerald-200">💵 Tunai</span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-sky-50 text-sky-700 rounded font-black border border-sky-200">🏦 Transfer</span>
                                                <span class="text-slate-500 font-mono text-[9px] truncate max-w-[120px]" title="{{ $item->payment_detail }}">
                                                    {{ $item->payment_detail }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200/80 space-y-1.5 text-[11px]">
                            @if($totalCash > 0)
                                <div class="flex justify-between items-center proj-summary-cash">
                                    <span class="font-bold text-emerald-700">💵 Kas Tunai Harus Siap:</span>
                                    <span class="font-black text-emerald-700">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            @if($totalTransfer > 0)
                                <div class="flex justify-between items-center proj-summary-transfer">
                                    <span class="font-bold text-sky-700">🏦 Kebutuhan Transfer:</span>
                                    <span class="font-black text-sky-700">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            @if($totalCash > 0)
                                <a href="{{ route('withdrawals.print-projection', ['date' => $date]) }}" target="_blank" 
                                   class="w-full mt-2 py-2 bg-slate-900 hover:bg-slate-800 text-white text-[10px] font-bold rounded-xl transition flex items-center justify-center gap-1 shadow-xs">
                                    🖨️ Cetak Lembar Kas Tunai
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 {{ auth()->user()->role === 'nasabah' ? 'lg:grid-cols-3' : '' }} gap-6 items-start">
        
        <!-- FORM TARIK SALDO (KHUSUS NASABAH) -->
        @if(auth()->user()->role === 'nasabah')
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-100 space-y-6 relative">
            <div class="pb-3 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-black text-slate-900">Form Permohonan</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pencairan dana langsung / transfer</p>
                </div>
                <div class="w-9 h-9 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-base shadow-xs">
                    💳
                </div>
            </div>
            
            <div class="p-6 bg-gradient-to-br from-slate-900 via-emerald-950 to-teal-900 rounded-3xl text-white shadow-xl shadow-emerald-950/20 relative overflow-hidden border border-emerald-500/20">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex justify-between items-start relative z-10">
                    <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20">
                        Saldo Aktif
                    </span>
                    <span class="text-xs font-black tracking-widest text-emerald-300">JAGATALA VIP</span>
                </div>
                <div class="text-3xl font-black tracking-tight mt-4 text-white relative z-10">
                    Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-slate-400 mt-2 font-medium">Bisa dicairkan kapan saja sesuai ketentuan.</p>
            </div>

            <form id="withdrawalForm" action="{{ route('withdrawals.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Nominal Penarikan</label>
                        <span id="sisaSaldoPreview" class="text-[11px] text-emerald-600 font-black"></span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400">Rp</span>
                        <input type="number" id="inputAmount" name="amount" placeholder="Min. 1.000" min="1000" max="{{ auth()->user()->balance }}" required 
                            class="w-full pl-11 pr-4 py-3 text-sm font-black rounded-2xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-900">
                    </div>
                    <p id="errorAmountText" class="text-[11px] text-rose-500 font-extrabold mt-1.5 hidden">⚠️ Nominal melebihi saldo aktif Anda!</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Pilih Nominal Cepat:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setQuickAmount(10000)" class="py-2.5 px-3 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 border border-slate-200/80 rounded-xl text-xs font-extrabold text-slate-700 transition active:scale-95 text-center shadow-2xs">
                            Rp 10.000
                        </button>
                        <button type="button" onclick="setQuickAmount(25000)" class="py-2.5 px-3 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 border border-slate-200/80 rounded-xl text-xs font-extrabold text-slate-700 transition active:scale-95 text-center shadow-2xs">
                            Rp 25.000
                        </button>
                        <button type="button" onclick="setQuickAmount(50000)" class="py-2.5 px-3 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 border border-slate-200/80 rounded-xl text-xs font-extrabold text-slate-700 transition active:scale-95 text-center shadow-2xs">
                            Rp 50.000
                        </button>
                        <button type="button" onclick="setQuickAmount({{ auth()->user()->balance }})" class="py-2.5 px-3 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-black transition active:scale-95 text-center shadow-2xs">
                            Tarik Semua
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Metode Pencairan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer" onclick="selectPaymentMethod('cash')">
                            <input type="radio" id="methodCash" name="payment_method" value="cash" checked class="peer sr-only">
                            <div class="p-3 border border-slate-200 rounded-2xl text-center peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
                                <span class="block text-base mb-0.5">💵</span>
                                <span class="text-xs font-black">Tunai (Cash)</span>
                            </div>
                        </label>
                        <label class="cursor-pointer" onclick="selectPaymentMethod('transfer')">
                            <input type="radio" id="methodTransfer" name="payment_method" value="transfer" class="peer sr-only">
                            <div class="p-3 border border-slate-200 rounded-2xl text-center peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
                                <span class="block text-base mb-0.5">🏦</span>
                                <span class="text-xs font-black">Bank / E-Wallet</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="paymentDetailContainer" class="hidden space-y-3 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Tipe Pembayaran</label>
                        <select id="transferType" onchange="updateProviderOptions()" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20">
                            <option value="bank">Bank Transfer</option>
                            <option value="ewallet">E-Wallet (Dompet Digital)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Pilih Penyedia</label>
                        <select id="providerName" onchange="toggleCustomProviderInput()" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20">
                        </select>
                    </div>

                    <div id="customProviderContainer" class="hidden">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Ketik Nama Bank / E-Wallet</label>
                        <input type="text" id="customProviderInput" placeholder="Contoh: Bank Nagari / SeaBank / iSaku" 
                            class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label id="accountNumberLabel" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Nomor Rekening</label>
                        <input type="text" id="accountNumber" placeholder="Masukkan nomor rekening" 
                            class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Nama Pemilik (Sesuai Rekening)</label>
                        <input type="text" id="accountHolder" placeholder="Contoh: Budi Santoso" value="{{ auth()->user()->name }}"
                            class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <input type="hidden" name="payment_detail" id="payment_detail">
                </div>

                <div id="cashNotice" class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-emerald-900 space-y-1">
                    <div class="flex items-center gap-1.5 font-black text-xs text-emerald-900">
                        <span>🏛️</span> Informasi Pencairan Tunai:
                    </div>
                    <p class="text-[11px] font-medium text-emerald-800 leading-relaxed">
                        Silakan **datang langsung ke kantor Bank Sampah Jagatala** pada tanggal pencairan yang tertera di tabel riwayat setelah pengajuan Anda disetujui.
                    </p>
                </div>

                <button type="button" onclick="openConfirmationModal()" id="btnSubmitWithdrawal"
                    class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-600/25 transition duration-150 {{ auth()->user()->balance < 1000 ? 'opacity-50 cursor-not-allowed' : '' }}" 
                    {{ auth()->user()->balance < 1000 ? 'disabled' : '' }}>
                    Kirim Pengajuan Sekarang
                </button>
            </form>
        </div>
        @endif

        <!-- BAGIAN TABEL DATA -->
        <div class="{{ auth()->user()->role === 'nasabah' ? 'lg:col-span-2' : '' }} space-y-6">
            
            @php
                $pendingList = isset($pendingWithdrawals) ? $pendingWithdrawals : collect();
            @endphp

            <!-- TABEL 1: ANTREAN MENUNGGU KONFIRMASI (PENDING - KHUSUS PENGELOLA) -->
            @if(auth()->user()->role === 'pengelola')
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-amber-50/30">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-black text-slate-900">Perlu Persetujuan (Pending)</h2>
                            <span class="px-2.5 py-0.5 text-xs font-black bg-amber-500 text-white rounded-full">
                                {{ $pendingList->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi pencairan yang membutuhkan tindakan pengelola.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto relative">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-4 px-6">Tgl Pengajuan</th>
                                <th class="py-4 px-6">Nasabah</th>
                                <th class="py-4 px-6">Nominal & Metode</th>
                                <th class="py-4 px-6">Tgl Pencairan / Ambil</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Opsi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($pendingList as $wd)
                                <tr class="hover:bg-amber-50/20 transition">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="font-extrabold text-slate-900 block">{{ $wd->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400">{{ $wd->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                                {{ strtoupper(substr($wd->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-slate-900">{{ $wd->user->name ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 min-w-[200px]">
                                        <div class="font-black text-slate-900 text-sm">
                                            Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] font-bold mt-1 flex flex-wrap items-center gap-1.5 text-slate-600">
                                            @if($wd->payment_method === 'transfer')
                                                <span class="px-1.5 py-0.5 bg-sky-50 text-sky-700 rounded border border-sky-200 shrink-0 font-black">🏦 Transfer</span>
                                                <span class="break-words font-semibold text-slate-700">{{ $wd->payment_detail ?? '-' }}</span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded border border-emerald-200 shrink-0 font-black">💵 Tunai</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($wd->pickup_date)
                                            <span class="font-bold text-slate-800 block">
                                                🗓️ {{ \Carbon\Carbon::parse($wd->pickup_date)->format('d M Y') }}
                                            </span>
                                            <span class="text-[10px] text-slate-400">
                                                {{ $wd->payment_method === 'cash' ? 'Ambil di Bank Sampah' : 'Estimasi Transfer' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-bold">—</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider animate-pulse">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <form id="actionForm-approve-{{ $wd->id }}" action="{{ route('withdrawals.update', $wd->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="approve">
                                                <button type="button" 
                                                    onclick="openAdminActionModal('approve', '{{ $wd->id }}', 'Rp {{ number_format($wd->amount, 0, ',', '.') }}', '{{ $wd->user->name ?? 'Nasabah' }}')"
                                                    class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-xs transition active:scale-95">
                                                    ✓ Setujui
                                                </button>
                                            </form>

                                            <form id="actionForm-reject-{{ $wd->id }}" action="{{ route('withdrawals.update', $wd->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="rejection_reason" id="rejectionReasonInput-{{ $wd->id }}">
                                                <button type="button" 
                                                    onclick="openAdminActionModal('reject', '{{ $wd->id }}', 'Rp {{ number_format($wd->amount, 0, ',', '.') }}', '{{ $wd->user->name ?? 'Nasabah' }}')"
                                                    class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-black rounded-xl border border-rose-200 transition active:scale-95">
                                                    ✕ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-slate-400 font-bold">
                                        🎉 Tidak ada antrean pencairan baru yang menunggu persetujuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- TABEL 2: RIWAYAT PENCAIRAN SELESAI / HISTORI -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">
                            {{ auth()->user()->role === 'pengelola' ? 'Riwayat Pencairan Selesai' : 'Riwayat Penarikan Saldo' }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ auth()->user()->role === 'pengelola' ? 'Daftar transaksi pencairan yang sudah disetujui atau ditolak' : 'Catatan histori pengajuan tarik saldo Anda' }}
                        </p>
                    </div>
                </div>
                
                <div class="overflow-x-auto relative">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="py-4 px-6">Tgl Pengajuan</th>
                                @if(auth()->user()->role === 'pengelola')
                                    <th class="py-4 px-6">Nasabah</th>
                                @endif
                                <th class="py-4 px-6">Nominal & Metode</th>
                                <th class="py-4 px-6">Tgl Pencairan / Ambil</th>
                                <th class="py-4 px-6 text-center">Status & Penyerahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @php
                                $historiData = (auth()->user()->role === 'pengelola') ? $completedWithdrawals : $withdrawals;
                            @endphp

                            @forelse($historiData as $wd)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="font-extrabold text-slate-900 block">{{ $wd->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400">{{ $wd->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    
                                    @if(auth()->user()->role === 'pengelola')
                                        <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                                    {{ strtoupper(substr($wd->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div class="text-xs font-black text-slate-900">{{ $wd->user->name ?? '-' }}</div>
                                            </div>
                                        </td>
                                    @endif

                                    <td class="py-4 px-6 min-w-[200px]">
                                        <div class="font-black text-slate-900 text-sm">
                                            Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] font-bold mt-1 flex flex-wrap items-center gap-1.5 text-slate-600">
                                            @if($wd->payment_method === 'transfer')
                                                <span class="px-1.5 py-0.5 bg-sky-50 text-sky-700 rounded border border-sky-200 shrink-0 font-black">🏦 Transfer</span>
                                                <span class="break-words font-semibold text-slate-700">{{ $wd->payment_detail ?? '-' }}</span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded border border-emerald-200 shrink-0 font-black">💵 Tunai</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($wd->pickup_date)
                                            <span class="font-bold text-slate-800 block">
                                                🗓️ {{ \Carbon\Carbon::parse($wd->pickup_date)->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-bold">—</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        @if($wd->status === 'approved')
                                            <div class="flex flex-col items-center gap-1.5">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui
                                                </span>

                                                @if($wd->completed_at)
                                                    <span class="text-[9px] font-extrabold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded border border-emerald-200" title="Diserahterimakan pada {{ $wd->completed_at->format('d M Y H:i') }}">
                                                        🤝 Tunai Diserahkan ({{ $wd->completed_at->format('d/m/Y') }})
                                                    </span>
                                                @elseif($wd->payment_method === 'cash' && auth()->user()->role === 'pengelola')
                                                    <button type="button" 
                                                        onclick="openFulfillmentModal('{{ $wd->id }}', '{{ $wd->user->name ?? 'Nasabah' }}', 'Rp {{ number_format($wd->amount, 0, ',', '.') }}')"
                                                        class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black rounded-lg shadow-2xs transition active:scale-95">
                                                        ✍️ Tangani Serah Terima Kas
                                                    </button>
                                                @endif

                                                <a href="{{ route('withdrawals.receipt', $wd->id) }}" target="_blank" 
                                                   class="text-[10px] font-extrabold text-emerald-600 hover:underline flex items-center gap-0.5">
                                                    🧾 Lihat Kuitansi
                                                </a>
                                            </div>
                                        @elseif($wd->status === 'rejected')
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Ditolak
                                                </span>
                                                @if(!empty($wd->rejection_reason))
                                                    <span class="text-[10px] text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-100 max-w-[150px] truncate" title="{{ $wd->rejection_reason }}">
                                                        Alasan: {{ $wd->rejection_reason }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'pengelola' ? '5' : '4' }}" class="py-8 text-center text-xs text-slate-400 font-bold">
                                        Belum ada riwayat transaksi yang selesai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- NAVIGATION PAGINATION -->
                @if($historiData->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $historiData->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>

<!-- MODAL POPUP STATUS RESPON (DISETUJUI / DITOLAK) -->
@if(session('status_popup'))
<div id="statusResultModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-5 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-200 text-center">
        @if(session('status_popup') === 'approved')
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 font-black text-3xl flex items-center justify-center mx-auto border border-emerald-100 shadow-xs">
                ✅
            </div>
            <div class="space-y-1.5">
                <h3 class="text-lg font-black text-slate-900">Pengajuan Disetujui!</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ session('success') }}
                </p>
            </div>
            <button type="button" onclick="closeStatusResultModal()" 
                class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition active:scale-95">
                Selesai / Tutup
            </button>
        @else
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 font-black text-3xl flex items-center justify-center mx-auto border border-rose-100 shadow-xs">
                ❌
            </div>
            <div class="space-y-2">
                <h3 class="text-lg font-black text-slate-900">Pengajuan Ditolak</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengajuan pencairan saldo telah ditolak oleh pengelola.
                </p>
                <div class="p-3 bg-rose-50 border border-rose-100 rounded-2xl text-left space-y-1">
                    <span class="block text-[10px] font-black text-rose-700 uppercase tracking-wider">Alasan Penolakan:</span>
                    <p class="text-xs font-bold text-rose-900 leading-snug">
                        "{{ session('rejection_reason') }}"
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeStatusResultModal()" 
                class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition active:scale-95">
                Tutup Peringatan
            </button>
        @endif
    </div>
</div>
@endif

<!-- MODAL PERINGATAN / ALERT KUSTOM -->
<div id="alertModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-5 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-150 text-center">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 font-black text-2xl flex items-center justify-center mx-auto border border-amber-100 shadow-xs">
            ⚠️
        </div>
        <div class="space-y-1">
            <h3 id="alertModalTitle" class="text-base font-black text-slate-900">Perhatian</h3>
            <p id="alertModalMessage" class="text-xs text-slate-500 leading-relaxed">
                Mohon masukkan nominal penarikan yang valid.
            </p>
        </div>
        <button type="button" onclick="closeAlertModal()" 
            class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl shadow-md transition active:scale-95">
            Mengerti
        </button>
    </div>
</div>

<!-- MODAL KONFIRMASI NASABAH -->
<div id="confirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-5 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-150">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 font-black text-2xl flex items-center justify-center mx-auto border border-emerald-100 shadow-xs">
                🔍
            </div>
            <h3 class="text-base font-black text-slate-900">Konfirmasi Pencairan</h3>
            <p class="text-xs text-slate-500">Pastikan detail tujuan pencairan saldo Anda sudah benar.</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-2">
                <span class="text-slate-400 font-medium">Nominal Penarikan</span>
                <span id="confirmAmount" class="font-black text-slate-900 text-sm">Rp 0</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-2">
                <span class="text-slate-400 font-medium">Metode</span>
                <span id="confirmMethod" class="font-black text-slate-900">Tunai</span>
            </div>
            
            <div id="confirmTransferDetails" class="space-y-2 hidden pt-1">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Penyedia</span>
                    <span id="confirmProvider" class="font-bold text-slate-800">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">No. Rekening/E-Wallet</span>
                    <span id="confirmAccountNum" class="font-bold text-slate-800">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Nama Pemilik</span>
                    <span id="confirmHolder" class="font-black text-emerald-700 bg-emerald-100/60 px-2 py-0.5 rounded">-</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="closeConfirmationModal()" 
                class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-2xl transition">
                Batal / Edit
            </button>
            <button type="button" onclick="submitFinalForm()" 
                class="w-1/2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition">
                Ya, Sudah Benar
            </button>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI SETUJUI / TOLAK (KHUSUS PENGELOLA) -->
<div id="adminActionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-5 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-150">
        <div class="text-center space-y-2">
            <div id="adminModalIcon" class="w-12 h-12 rounded-2xl font-black text-2xl flex items-center justify-center mx-auto shadow-xs">
                ⚠️
            </div>
            <h3 id="adminModalTitle" class="text-base font-black text-slate-900">Konfirmasi Tindakan</h3>
            <p id="adminModalDesc" class="text-xs text-slate-500">Apakah Anda yakin ingin melakukan tindakan ini?</p>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-2">
                <span class="text-slate-400 font-medium">Nama Nasabah</span>
                <span id="adminModalNasabah" class="font-black text-slate-900">-</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">Nominal Penarikan</span>
                <span id="adminModalNominal" class="font-black text-emerald-700 text-sm">-</span>
            </div>
        </div>

        <div id="rejectionReasonContainer" class="hidden space-y-1.5">
            <label class="block text-[10px] font-black text-rose-600 uppercase tracking-wider">Alasan Penolakan <span class="text-rose-500">*</span></label>
            <textarea id="modalRejectionReasonText" rows="3" placeholder="Masukkan alasan kenapa pengajuan ditolak..." 
                class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-rose-200 bg-rose-50/30 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-800"></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="closeAdminActionModal()" 
                class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-2xl transition">
                Batal
            </button>
            <button type="button" id="adminModalSubmitBtn" onclick="submitAdminAction()" 
                class="w-1/2 py-3 text-white font-extrabold text-xs rounded-2xl shadow-md transition">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<!-- MODAL SERAH TERIMA / TANDA TANGAN DIGITAL (KHUSUS PENGELOLA) -->
<div id="fulfillmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-150">
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 font-black text-2xl flex items-center justify-center mx-auto border border-emerald-100 shadow-xs">
                🤝
            </div>
            <h3 class="text-base font-black text-slate-900">Serah Terima Uang Fisik</h3>
            <p class="text-xs text-slate-500">Minta nasabah menandatangani area di bawah sebagai bukti serah terima kas.</p>
        </div>

        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 space-y-1 text-xs">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">Penerima (Nasabah):</span>
                <span id="fulfillmentNasabah" class="font-black text-slate-900">-</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">Nominal Kas Fisik:</span>
                <span id="fulfillmentNominal" class="font-black text-emerald-700 text-sm">-</span>
            </div>
        </div>

        <form id="fulfillmentForm" action="" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="signature" id="signatureInput">
            
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Tanda Tangan Nasabah</label>
                    <button type="button" onclick="clearCanvas()" class="text-[10px] font-bold text-rose-500 hover:underline">Hapus / Reset</button>
                </div>
                <div class="border border-slate-300 rounded-2xl bg-white overflow-hidden touch-none">
                    <canvas id="signatureCanvas" width="380" height="150" class="w-full h-36 cursor-crosshair"></canvas>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="closeFulfillmentModal()" 
                    class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="button" onclick="submitFulfillment()" 
                    class="w-1/2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition">
                    Konfirmasi & Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const userBalance = {{ auth()->user()->balance ?? 0 }};
    const inputAmount = document.getElementById('inputAmount');
    const errorText = document.getElementById('errorAmountText');
    const btnSubmit = document.getElementById('btnSubmitWithdrawal');
    const previewText = document.getElementById('sisaSaldoPreview');

    const bankList = ['BCA', 'Mandiri', 'BRI', 'BNI', 'CIMB Niaga', 'Permata', 'BTN', 'BSI (Bank Syariah Indonesia)'];
    const ewalletList = ['DANA', 'GoPay', 'OVO', 'ShopeePay', 'LinkAja'];

    function closeStatusResultModal() {
        const modal = document.getElementById('statusResultModal');
        if (modal) modal.classList.add('hidden');
    }

    function filterProjection(type) {
        const allItems = document.querySelectorAll('.projection-item');
        const btnAll = document.getElementById('btnFilterAll');
        const btnCash = document.getElementById('btnFilterCash');
        const btnTransfer = document.getElementById('btnFilterTransfer');

        [btnAll, btnCash, btnTransfer].forEach(btn => {
            if(btn) btn.className = "px-3 py-1 text-[11px] font-black rounded-xl text-slate-500 hover:text-slate-900 transition";
        });

        if (type === 'all') {
            if(btnAll) btnAll.className = "px-3 py-1 text-[11px] font-black rounded-xl bg-white text-slate-900 shadow-2xs transition";
            allItems.forEach(item => item.classList.remove('hidden'));
        } else if (type === 'cash') {
            if(btnCash) btnCash.className = "px-3 py-1 text-[11px] font-black rounded-xl bg-emerald-500 text-white shadow-2xs transition";
            allItems.forEach(item => item.getAttribute('data-method') === 'cash' ? item.classList.remove('hidden') : item.classList.add('hidden'));
        } else if (type === 'transfer') {
            if(btnTransfer) btnTransfer.className = "px-3 py-1 text-[11px] font-black rounded-xl bg-sky-500 text-white shadow-2xs transition";
            allItems.forEach(item => item.getAttribute('data-method') === 'transfer' ? item.classList.remove('hidden') : item.classList.add('hidden'));
        }
    }

    function showAlertModal(message, title = 'Perhatian') {
        document.getElementById('alertModalTitle').innerText = title;
        document.getElementById('alertModalMessage').innerText = message;
        document.getElementById('alertModal').classList.remove('hidden');
    }

    function closeAlertModal() {
        document.getElementById('alertModal').classList.add('hidden');
    }

    function selectPaymentMethod(type) {
        if(type === 'transfer') {
            document.getElementById('methodTransfer').checked = true;
        } else {
            document.getElementById('methodCash').checked = true;
        }
        togglePaymentDetail();
    }

    function updateProviderOptions() {
        const typeSelect = document.getElementById('transferType');
        if(!typeSelect) return;
        
        const type = typeSelect.value;
        const providerSelect = document.getElementById('providerName');
        const numLabel = document.getElementById('accountNumberLabel');
        const numInput = document.getElementById('accountNumber');

        providerSelect.innerHTML = '';
        const list = type === 'bank' ? bankList : ewalletList;

        list.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            opt.textContent = p;
            providerSelect.appendChild(opt);
        });

        const customOpt = document.createElement('option');
        customOpt.value = 'other';
        customOpt.textContent = '✏️ Lainnya (Ketik Manual)';
        providerSelect.appendChild(customOpt);

        if (type === 'bank') {
            numLabel.innerText = 'Nomor Rekening Bank';
            numInput.placeholder = 'Masukkan nomor rekening';
        } else {
            numLabel.innerText = 'Nomor HP E-Wallet';
            numInput.placeholder = 'Contoh: 081234567890';
        }

        toggleCustomProviderInput();
    }

    function toggleCustomProviderInput() {
        const providerSelect = document.getElementById('providerName');
        if(!providerSelect) return;
        
        const selectedVal = providerSelect.value;
        const customContainer = document.getElementById('customProviderContainer');
        
        if (selectedVal === 'other') {
            customContainer.classList.remove('hidden');
        } else {
            customContainer.classList.add('hidden');
        }
    }

    function togglePaymentDetail() {
        const methodEl = document.querySelector('input[name="payment_method"]:checked');
        if(!methodEl) return;
        
        const method = methodEl.value;
        const detailContainer = document.getElementById('paymentDetailContainer');
        const cashNotice = document.getElementById('cashNotice');

        if (method === 'transfer') {
            detailContainer.classList.remove('hidden');
            cashNotice.classList.add('hidden');
            updateProviderOptions();
        } else {
            detailContainer.classList.add('hidden');
            cashNotice.classList.remove('hidden');
        }
    }

    function setQuickAmount(amount) {
        if (!inputAmount) return;
        inputAmount.value = amount;
        validateAmount();
    }

    if (inputAmount) {
        inputAmount.addEventListener('input', validateAmount);
    }

    function validateAmount() {
        const val = parseInt(inputAmount.value) || 0;
        if (val > userBalance) {
            errorText.classList.remove('hidden');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            previewText.innerText = '';
        } else {
            errorText.classList.add('hidden');
            btnSubmit.disabled = (userBalance < 1000 || val < 1000);
            if (userBalance >= 1000 && val >= 1000) {
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (val > 0 && val <= userBalance) {
                const sisa = userBalance - val;
                previewText.innerText = 'Sisa Saldo: Rp ' + sisa.toLocaleString('id-ID');
            } else {
                previewText.innerText = '';
            }
        }
    }

    function openConfirmationModal() {
        const amount = parseInt(inputAmount.value) || 0;
        if (amount < 1000 || amount > userBalance) {
            showAlertModal('Mohon masukkan nominal penarikan yang valid (Min. Rp 1.000).', 'Nominal Tidak Valid');
            return;
        }

        const method = document.querySelector('input[name="payment_method"]:checked').value;
        document.getElementById('confirmAmount').innerText = 'Rp ' + amount.toLocaleString('id-ID');

        if (method === 'transfer') {
            const providerSelect = document.getElementById('providerName').value;
            const customProvider = document.getElementById('customProviderInput').value.trim();
            const accountNum = document.getElementById('accountNumber').value.trim();
            const holder = document.getElementById('accountHolder').value.trim();

            let providerFinal = providerSelect;
            if (providerSelect === 'other') {
                if (!customProvider) {
                    showAlertModal('Mohon ketik nama Bank/E-Wallet Anda!', 'Data Belum Lengkap');
                    return;
                }
                providerFinal = customProvider;
            }

            if (!accountNum || !holder) {
                showAlertModal('Mohon lengkapi Nomor Rekening/E-Wallet dan Nama Pemilik!', 'Data Belum Lengkap');
                return;
            }

            document.getElementById('payment_detail').value = `${providerFinal} - ${accountNum} a.n ${holder}`;

            document.getElementById('confirmMethod').innerText = 'Transfer (Bank/E-Wallet)';
            document.getElementById('confirmProvider').innerText = providerFinal;
            document.getElementById('confirmAccountNum').innerText = accountNum;
            document.getElementById('confirmHolder').innerText = holder;
            document.getElementById('confirmTransferDetails').classList.remove('hidden');
        } else {
            document.getElementById('confirmMethod').innerText = 'Tunai (Cash)';
            document.getElementById('payment_detail').value = '';
            document.getElementById('confirmTransferDetails').classList.add('hidden');
        }

        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function submitFinalForm() {
        document.getElementById('withdrawalForm').submit();
    }

    let currentTargetFormId = null;
    let currentWithdrawalId = null;
    let currentActionType = null;

    function openAdminActionModal(type, id, amountStr, nasabahName) {
        currentTargetFormId = `actionForm-${type}-${id}`;
        currentWithdrawalId = id;
        currentActionType = type;
        
        const iconEl = document.getElementById('adminModalIcon');
        const titleEl = document.getElementById('adminModalTitle');
        const descEl = document.getElementById('adminModalDesc');
        const submitBtn = document.getElementById('adminModalSubmitBtn');
        const rejectionContainer = document.getElementById('rejectionReasonContainer');
        const rejectionText = document.getElementById('modalRejectionReasonText');

        document.getElementById('adminModalNasabah').innerText = nasabahName;
        document.getElementById('adminModalNominal').innerText = amountStr;
        rejectionText.value = '';

        if (type === 'approve') {
            rejectionContainer.classList.add('hidden');
            iconEl.className = 'w-12 h-12 rounded-2xl font-black text-2xl flex items-center justify-center mx-auto shadow-xs bg-emerald-50 text-emerald-600 border border-emerald-100';
            iconEl.innerText = '✅';
            titleEl.innerText = 'Setujui Pencairan';
            descEl.innerText = 'Apakah Anda yakin ingin menyetujui pengajuan pencairan saldo ini? Saldo nasabah akan dipotong.';
            submitBtn.className = 'w-1/2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition';
            submitBtn.innerText = 'Ya, Setujui';
        } else {
            rejectionContainer.classList.remove('hidden');
            iconEl.className = 'w-12 h-12 rounded-2xl font-black text-2xl flex items-center justify-center mx-auto shadow-xs bg-rose-50 text-rose-600 border border-rose-100';
            iconEl.innerText = '⚠️';
            titleEl.innerText = 'Tolak Pencairan';
            descEl.innerText = 'Berikan alasan penolakan pengajuan pencairan saldo ini.';
            submitBtn.className = 'w-1/2 py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition';
            submitBtn.innerText = 'Ya, Tolak';
        }

        document.getElementById('adminActionModal').classList.remove('hidden');
    }

    function closeAdminActionModal() {
        document.getElementById('adminActionModal').classList.add('hidden');
        currentTargetFormId = null;
        currentWithdrawalId = null;
        currentActionType = null;
    }

    function submitAdminAction() {
        if (currentActionType === 'reject') {
            const reason = document.getElementById('modalRejectionReasonText').value.trim();
            if (!reason) {
                showAlertModal('Mohon isi alasan penolakan!', 'Peringatan');
                return;
            }
            document.getElementById(`rejectionReasonInput-${currentWithdrawalId}`).value = reason;
        }

        if (currentTargetFormId) {
            document.getElementById(currentTargetFormId).submit();
        }
    }

    /* KANVAS TANDA TANGAN DIGITAL */
    let canvas = document.getElementById('signatureCanvas');
    let ctx = canvas ? canvas.getContext('2d') : null;
    let isDrawing = false;
    let isCanvasEmpty = true;

    if (canvas && ctx) {
        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';

        function getCanvasCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            isCanvasEmpty = false;
            const coords = getCanvasCoords(e);
            ctx.beginPath();
            ctx.moveTo(coords.x, coords.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            const coords = getCanvasCoords(e);
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDrawing(e); });
        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); });
        canvas.addEventListener('touchend', stopDrawing);
    }

    function clearCanvas() {
        if (ctx && canvas) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            isCanvasEmpty = true;
        }
    }

    function openFulfillmentModal(id, nasabahName, amountStr) {
        document.getElementById('fulfillmentNasabah').innerText = nasabahName;
        document.getElementById('fulfillmentNominal').innerText = amountStr;
        document.getElementById('fulfillmentForm').action = `/withdrawals/${id}/fulfillment`;
        clearCanvas();
        document.getElementById('fulfillmentModal').classList.remove('hidden');
    }

    function closeFulfillmentModal() {
        document.getElementById('fulfillmentModal').classList.add('hidden');
    }

    function submitFulfillment() {
        if (isCanvasEmpty) {
            showAlertModal('Mohon minta nasabah membubuhkan tanda tangan terlebih dahulu!', 'Tanda Tangan Kosong');
            return;
        }
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('signatureInput').value = dataUrl;
        document.getElementById('fulfillmentForm').submit();
    }

    if(document.getElementById('transferType')) {
        updateProviderOptions();
    }
</script>
@endsection