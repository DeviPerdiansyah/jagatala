<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Proyeksi Uang Kas Tunai - {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-8 bg-white text-slate-800" onload="window.print()">

    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-4 mb-6">
        <div>
            <h1 class="text-xl font-black uppercase tracking-tight">Bank Sampah Jagatala</h1>
            <p class="text-xs text-slate-500">Rekap Kebutuhan Uang Kas Fisik Harian</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-bold text-slate-400 block uppercase">TANGGAL PENCAIRAN</span>
            <div class="text-base font-black text-slate-900">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</div>
        </div>
    </div>

    <table class="w-full text-xs text-left border-collapse mb-6">
        <thead>
            <tr class="border-b border-slate-300 bg-slate-100">
                <th class="p-2 border border-slate-300 text-center w-12">No</th>
                <th class="p-2 border border-slate-300">Nama Nasabah</th>
                <th class="p-2 border border-slate-300">Nominal (Rp)</th>
                <th class="p-2 border border-slate-300 text-center">Paraf / Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr class="border-b border-slate-200">
                    <td class="p-2 border border-slate-200 text-center font-semibold">{{ $index + 1 }}</td>
                    <td class="p-2 border border-slate-200 font-bold capitalize">{{ $item->user->name ?? '-' }}</td>
                    <td class="p-2 border border-slate-200 font-extrabold">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    <td class="p-2 border border-slate-200 w-40 h-12"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-slate-400 border border-slate-200">Tidak ada transaksi kas pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-black">
                <td colspan="2" class="p-2 border border-slate-300 text-right">TOTAL UANG FISIK HARUS SIAP:</td>
                <td class="p-2 border border-slate-300 text-emerald-700">Rp {{ number_format($items->sum('amount'), 0, ',', '.') }}</td>
                <td class="p-2 border border-slate-300"></td>
            </tr>
        </tfoot>
    </table>

    <div class="flex justify-between text-xs mt-12 pt-6">
        <div class="text-center">
            <p>Pengelola Bank Sampah</p>
            <div class="h-16"></div>
            <p class="font-bold underline">( .................................... )</p>
        </div>
        <div class="text-center">
            <p>Nasabah</p>
            <div class="h-16"></div>
            <p class="font-bold underline">( .................................... )</p>
        </div>
    </div>

    <div class="no-print mt-8 text-center">
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold shadow hover:bg-slate-800 transition">Cetak Dokumen</button>
    </div>

</body>
</html>