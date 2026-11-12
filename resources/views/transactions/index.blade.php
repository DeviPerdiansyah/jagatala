@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Bank Sampah Jagatala')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- HEADER HALAMAN & TOMBOL AKSI -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Riwayat Transaksi</h1>
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full">
                    Mutasi Resmi
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ auth()->user()->role === 'pengelola' ? 'Kelola, filter, dan cetak laporan seluruh transaksi setoran sampah nasabah.' : 'Pantau catatan dan mutasi setoran tabungan sampah digital Anda.' }}
            </p>
        </div>
        
        <!-- TOMBOL AKSI KHUSUS PENGELOLA -->
        @if(auth()->user()->role === 'pengelola')
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('transactions.export-excel', request()->all()) }}" 
               class="px-3.5 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-xs rounded-xl border border-emerald-200/80 transition inline-flex items-center gap-1.5 shadow-xs active:scale-95">
                <span>📊</span> Export Excel (.csv)
            </a>

            <a href="{{ route('transactions.print-report', request()->all()) }}" target="_blank"
               class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl border border-slate-200 transition inline-flex items-center gap-1.5 shadow-xs active:scale-95">
                <span>🖨️</span> Cetak Laporan
            </a>

            <a href="{{ route('transactions.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-emerald-600/20 transition inline-flex items-center gap-2 active:scale-95">
                <span>➕</span> Input Setoran Baru
            </a>
        </div>
        @endif
    </div>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="absolute right-0 bottom-0 translate-x-4 translate-y-4 w-28 h-28 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100/70 transition"></div>
            <div class="z-10">
                <p class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Total Berat / Jumlah Setor</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1">
                    {{ number_format($totalSummary['weight'], 1, ',', '.') }} <span class="text-sm font-black text-emerald-600 uppercase">Item</span>
                </h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-bold z-10 shadow-xs">
                ⚖️
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="absolute right-0 bottom-0 translate-x-4 translate-y-4 w-28 h-28 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100/70 transition"></div>
            <div class="z-10">
                <p class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Total Nilai Transaksi Setoran</p>
                <h3 class="text-3xl font-black text-emerald-600 mt-1">
                    Rp {{ number_format($totalSummary['amount'], 0, ',', '.') }}
                </h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-bold z-10 shadow-xs">
                💰
            </div>
        </div>
    </div>

    <!-- FILTER FORM PANEL -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 {{ auth()->user()->role === 'pengelola' ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-4">
                
                @if(auth()->user()->role === 'pengelola')
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Nasabah</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama nasabah..." 
                        class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                </div>
                @endif

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kategori Sampah</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Spesifik</label>
                    <input type="date" name="date" value="{{ request('date') }}" 
                        class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                </div>

                @php
                    $bulanIndo = [
                        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                @endphp
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Bulan</label>
                    <select name="month" class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                        <option value="">-- Semua Bulan --</option>
                        @foreach(range(1, 12) as $m)
                            @php $mVal = sprintf('%02d', $m); @endphp
                            <option value="{{ $mVal }}" {{ request('month') == $mVal ? 'selected' : '' }}>
                                {{ $bulanIndo[$m] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Tahun</label>
                    <select name="year" class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                        <option value="">-- Semua Tahun --</option>
                        @foreach(range(date('Y'), 2024) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl transition font-extrabold flex items-center gap-1.5" title="Reset Filter">
                    🔄 <span>Reset</span>
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs active:scale-95 flex items-center justify-center gap-1.5">
                    🔍 <span>Terapkan Filter</span>
                </button>
            </div>

        </form>
    </div>

    <!-- TABEL UTAMA TRANSAKSI -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto relative scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-slate-50/90 backdrop-blur-md border-b border-slate-200/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 z-10">
                    <tr>
                        <th class="py-4 px-6">Waktu Setor</th>
                        @if(auth()->user()->role === 'pengelola')
                            <th class="py-4 px-6">Nama Nasabah</th>
                        @endif
                        <th class="py-4 px-6">Kategori Sampah</th>
                        <th class="py-4 px-6 text-center">Jumlah / Berat</th>
                        <th class="py-4 px-6 text-right">Total Transaksi</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($transactions as $trx)
                        @php
                            $firstDetail = $trx->details->first();
                            $unitText = ($firstDetail && strtolower($firstDetail->unit ?? $firstDetail->category->unit ?? 'kg') === 'pcs') ? 'Pcs' : 'Kg';

                            $detailsPayload = $trx->details->map(function($d) {
                                $u = strtolower($d->unit ?? $d->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
                                return [
                                    'name' => $d->category->name ?? 'Sampah',
                                    'weight' => number_format($d->weight, 1, ',', '.'),
                                    'unit' => $u,
                                    'price' => 'Rp ' . number_format($d->price_per_kg, 0, ',', '.'),
                                    'subtotal' => 'Rp ' . number_format($d->subtotal, 0, ',', '.')
                                ];
                            });
                        @endphp
                        <tr class="hover:bg-emerald-50/30 transition group cursor-pointer" onclick="openReceiptModal({
                            id: 'TRX-{{ str_pad($trx->id, 5, "0", STR_PAD_LEFT) }}',
                            date: '{{ $trx->created_at->format("d M Y, H:i") }} WIB',
                            nasabah: '{{ addslashes($trx->user->name ?? "-") }}',
                            details: {{ json_encode($detailsPayload) }},
                            weight: '{{ number_format($trx->weight, 1, ",", ".") }} {{ $unitText }}',
                            total: '+ Rp {{ number_format($trx->total_amount, 0, ",", ".") }}'
                        })">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block">{{ $trx->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $trx->created_at->format('H:i') }} WIB</span>
                            </td>
                            @if(auth()->user()->role === 'pengelola')
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-xs border border-emerald-200/60">
                                            {{ strtoupper(substr($trx->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span>{{ $trx->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                            @endif
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($trx->details->count() > 1)
                                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 text-[11px] font-extrabold inline-block">
                                        📦 Multi-Item ({{ $trx->details->count() }} Jenis)
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-extrabold inline-block">
                                        ♻️ {{ $trx->details->first()->category->name ?? ($trx->category->name ?? '-') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center font-black text-slate-900 whitespace-nowrap">
                                {{ number_format($trx->weight, 1, ',', '.') }} <span class="text-[10px] font-bold text-slate-400">{{ $unitText }}</span>
                            </td>
                            <td class="py-4 px-6 text-right font-black text-emerald-600 whitespace-nowrap text-sm">
                                + Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap" onclick="event.stopPropagation();">
                                <button type="button" 
                                    onclick="openReceiptModal({
                                        id: 'TRX-{{ str_pad($trx->id, 5, "0", STR_PAD_LEFT) }}',
                                        date: '{{ $trx->created_at->format("d M Y, H:i") }} WIB',
                                        nasabah: '{{ addslashes($trx->user->name ?? "-") }}',
                                        details: {{ json_encode($detailsPayload) }},
                                        weight: '{{ number_format($trx->weight, 1, ",", ".") }} {{ $unitText }}',
                                        total: '+ Rp {{ number_format($trx->total_amount, 0, ",", ".") }}'
                                    })"
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-600 rounded-xl text-[10px] font-extrabold transition shadow-2xs active:scale-95">
                                    📄 Struk
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'pengelola' ? '6' : '5' }}" class="py-12 text-center text-xs text-slate-400 font-bold">
                                Tidak ada data transaksi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif

    </div>

</div>

<!-- MODAL STRUK DIGITAL -->
<div id="receiptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4 transition duration-300">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 sm:p-7 shadow-2xl border border-slate-100 relative transform transition-all scale-95 opacity-0 duration-200" id="receiptModalContent">
        
        <button onclick="closeReceiptModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 font-extrabold text-xs transition">
            ✕
        </button>

        <div class="text-center pb-4 border-b border-dashed border-slate-200 space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-black flex items-center justify-center text-xl mx-auto shadow-lg shadow-emerald-600/30">
                J
            </div>
            <h3 class="font-black text-slate-900 text-base">Bank Sampah Jagatala</h3>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Struk Digital Setoran Sampah</p>
            <span id="modalTrxId" class="inline-block mt-2 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[10px] font-mono font-bold tracking-wider">
                TRX-00000
            </span>
        </div>

        <!-- Detail Struk -->
        <div class="py-4 space-y-3 text-xs text-slate-600">
            <div class="flex justify-between items-center">
                <span class="text-slate-400">Waktu Transaksi</span>
                <span id="modalDate" class="font-extrabold text-slate-800">-</span>
            </div>
            @if(auth()->user()->role === 'pengelola')
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-400">Nama Nasabah</span>
                <span id="modalNasabah" class="font-extrabold text-slate-800">-</span>
            </div>
            @endif

            <!-- Rincian Multi-Item -->
            <div class="space-y-1.5 pt-1">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Rincian Item Sampah:</span>
                <div id="modalItemsList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    <!-- Dynamic Rows via JS -->
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-dashed border-slate-200 flex items-center justify-between">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Diterima</span>
            <span id="modalTotal" class="text-lg font-black text-emerald-600">+ Rp 0</span>
        </div>

        <div class="mt-6">
            <button onclick="closeReceiptModal()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-black rounded-xl transition shadow-md active:scale-95">
                Tutup Struk
            </button>
        </div>

    </div>
</div>

<!-- SCRIPT MODAL INTERAKTIF -->
<script>
    function openReceiptModal(data) {
        document.getElementById('modalTrxId').innerText = data.id;
        document.getElementById('modalDate').innerText = data.date;
        if(document.getElementById('modalNasabah')) document.getElementById('modalNasabah').innerText = data.nasabah;
        document.getElementById('modalTotal').innerText = data.total;

        // Render List Items dengan Satuan Dinamis
        const itemsContainer = document.getElementById('modalItemsList');
        itemsContainer.innerHTML = '';

        if (data.details && data.details.length > 0) {
            data.details.forEach(item => {
                const row = `
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <span class="font-bold text-slate-800 block">${item.name}</span>
                            <span class="text-[10px] text-slate-400 font-semibold">${item.weight} ${item.unit} × ${item.price}</span>
                        </div>
                        <span class="font-black text-slate-700">${item.subtotal}</span>
                    </div>
                `;
                itemsContainer.insertAdjacentHTML('beforeend', row);
            });
        }

        const modal = document.getElementById('receiptModal');
        const content = document.getElementById('receiptModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');
        const content = document.getElementById('receiptModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Auto-open modal struk setelah transaksi berhasil disimpan
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('show_receipt'))
            openReceiptModal(@json(session('show_receipt')));
        @endif
    });
</script>
@endsection