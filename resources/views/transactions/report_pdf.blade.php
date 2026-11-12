<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Setoran - Bank Sampah Jagatala</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya Khusus Mode Cetak Kertas (Ketiadaannya tombol & scrollbar saat diprint) */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .table-scroll-container {
                max-height: none !important;
                overflow: visible !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-8 text-slate-800 font-sans">

    <!-- BAR AKSI TOP BAR (TIDAK AKAN IKUT TERCETAK DI KERTAS) -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2">
            <span class="text-xl">🖨️</span>
            <div>
                <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Cetak Laporan Transaksi</h3>
                <p class="text-[11px] text-slate-500">Pratinjau sebelum dicetak atau disimpan sebagai PDF</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <!-- Tombol Trigger Print Out Langsung -->
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>🖨️</span> Print / PDF
            </button>
            <!-- Tombol Tutup/Kembali -->
            <button onclick="window.close()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- WADAH UTAMA LAPORAN -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 print-container">
        
        <!-- HEADER KOP LAPORAN -->
        <div class="border-b-2 border-slate-900 pb-4 mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">Bank Sampah Jagatala</h1>
                <p class="text-xs text-slate-600 mt-0.5">Sistem Informasi Pengelolaan Sampah Terpadu & Real-Time</p>
                <p class="text-[11px] text-slate-400 mt-1">Dicetak pada: {{ date('d F Y, H:i') }} WIB oleh {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full uppercase tracking-wider">Laporan Resmi</span>
            </div>
        </div>

        <!-- PERIODE FILTER LAPORAN -->
        @if($startDate && $endDate)
        <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600">
            <strong>Periode Laporan:</strong> {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}
        </div>
        @endif

        <!-- TABEL LAPORAN DENGAN SCROLLBAR (Max Height 400px di Web Browser) -->
        <div class="table-scroll-container max-h-[400px] overflow-y-auto border border-slate-200 rounded-xl mb-6">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="sticky top-0 bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200 z-10">
                    <tr>
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">Tanggal</th>
                        @if(auth()->user()->role === 'pengelola')
                            <th class="py-3 px-3">Nasabah</th>
                        @endif
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3 text-center">Harga/Kg</th>
                        <th class="py-3 px-3 text-center">Berat (Kg)</th>
                        <th class="py-3 px-3 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($transactions as $index => $trx)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3 font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-3 font-medium whitespace-nowrap">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            @if(auth()->user()->role === 'pengelola')
                                <td class="py-2.5 px-3 font-bold text-slate-800 whitespace-nowrap">{{ $trx->user->name ?? '-' }}</td>
                            @endif
                            <td class="py-2.5 px-3 font-medium whitespace-nowrap">{{ $trx->category->name ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-center text-slate-500 whitespace-nowrap">Rp {{ number_format($trx->price_per_kg, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold whitespace-nowrap">{{ number_format($trx->weight, 1, ',', '.') }} Kg</td>
                            <td class="py-2.5 px-3 text-right font-extrabold text-emerald-700 whitespace-nowrap">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'pengelola' ? '7' : '6' }}" class="py-8 text-center text-slate-400">
                                Tidak ada data transaksi untuk dicetak.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- RINGKASAN AKUMULASI (FOOTER LAPORAN) -->
        <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs">
            <div>
                <p class="text-slate-500 uppercase font-bold text-[10px]">Total Akumulasi Berat</p>
                <p class="text-lg font-black text-slate-800 mt-0.5">{{ number_format($totalWeight, 1, ',', '.') }} Kg</p>
            </div>
            <div class="text-right">
                <p class="text-slate-500 uppercase font-bold text-[10px]">Total Nominal Nilai Setoran</p>
                <p class="text-lg font-black text-emerald-600 mt-0.5">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- TANDA TANGAN (HANYA TAMPIL SAAT DICETAK DI KERTAS) -->
        <div class="hidden print:grid grid-cols-2 gap-8 mt-12 text-center text-xs">
            <div>
                <p class="text-slate-500 mb-12">Petugas Lapangan,</p>
                <p class="font-bold border-b border-slate-400 inline-block px-8">( .................................... )</p>
            </div>
            <div>
                <p class="text-slate-500 mb-12">Penanggung Jawab / Admin,</p>
                <p class="font-bold border-b border-slate-400 inline-block px-8">{{ auth()->user()->name }}</p>
            </div>
        </div>

    </div>

</body>
</html>