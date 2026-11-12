<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi-Pencairan-{{ Str::slug($withdrawal->user->name ?? 'Nasabah') }}-BSJ-WD-{{ str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 flex justify-center items-start">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-6 space-y-6 relative overflow-hidden">
        
        <!-- Header Kuitansi -->
        <div class="flex items-center justify-between pb-4 border-b border-dashed border-slate-200">
            <div class="flex items-center gap-3">
                @if(file_exists(public_path('images/logo-jagatala.png')))
                    <img src="{{ asset('images/logo-jagatala.png') }}" alt="Logo Jagatala" class="h-10 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-black flex items-center justify-center text-lg shadow-xs">
                        ♻️
                    </div>
                @endif
                <div>
                    <h1 class="text-sm font-black text-slate-900 leading-tight">Bank Sampah Jagatala</h1>
                    <p class="text-[10px] text-slate-400 font-semibold">Bukti Resmi Pencairan Saldo</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-2.5 py-1 text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                    ✓ LUNAS
                </span>
            </div>
        </div>

        <!-- Info Transaksi & Kode Unik -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-medium">No. Referensi</span>
                <span class="font-black text-slate-800">#BSJ-WD-{{ str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-medium">Waktu Disetujui</span>
                <span class="font-bold text-slate-700">{{ $withdrawal->updated_at ? $withdrawal->updated_at->format('d M Y, H:i') : '-' }} WIB</span>
            </div>
        </div>

        <!-- Data Nasabah & Detail Pembayaran -->
        <div class="space-y-2.5 text-xs border-b border-dashed border-slate-200 pb-4">
            <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Detail Nasabah & Tujuan</div>
            
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Nasabah:</span>
                <span class="font-bold text-slate-900">{{ $withdrawal->user->name ?? '-' }}</span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-slate-500">No. WhatsApp/HP:</span>
                <span class="font-bold text-slate-800">{{ $withdrawal->user->phone ?? ($withdrawal->user->no_hp ?? '-') }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-500">Metode Penarikan:</span>
                <span class="font-bold text-slate-800">
                    {{ $withdrawal->payment_method === 'transfer' ? '🏦 Transfer (Bank/E-Wallet)' : '💵 Tunai (Cash)' }}
                </span>
            </div>

            @if($withdrawal->payment_method === 'transfer')
            <div class="flex justify-between items-start pt-1">
                <span class="text-slate-500 shrink-0">Rekening / E-Wallet:</span>
                <span class="font-black text-emerald-800 text-right break-words pl-4">
                    {{ $withdrawal->payment_detail ?? '-' }}
                </span>
            </div>
            @endif
        </div>

        <!-- Total Nominal -->
        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 text-center space-y-1">
            <span class="text-[10px] font-black uppercase text-emerald-600 tracking-wider">Nominal Dicairkan</span>
            <div class="text-2xl font-black text-emerald-900">
                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
            </div>
        </div>

        <!-- Estimasi Pencairan -->
        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-center space-y-0.5">
            <p class="text-[10px] font-bold text-amber-800">
                {{ $withdrawal->payment_method === 'cash' ? '🏛️ Jadwal Pengambilan Tunai:' : '⚡ Estimasi Transfer:' }}
            </p>
            <p class="text-[10px] font-semibold text-amber-700">
                @if($withdrawal->pickup_date)
                    {{ \Carbon\Carbon::parse($withdrawal->pickup_date)->format('d M Y') }}
                @else
                    Maksimal 3 Hari Kerja setelah persetujuan.
                @endif
            </p>
        </div>

        <!-- Instruksi -->
        <p class="text-[10px] text-center text-slate-400 leading-relaxed italic">
            {{ $withdrawal->payment_method === 'cash' 
                ? 'Tunjukkan kuitansi digital ini kepada pengelola Bank Sampah Jagatala saat pengambilan dana tunai di lokasi.' 
                : 'Simpan kuitansi ini sebagai bukti sah pengajuan transfer dana penarikan saldo Anda.' }}
        </p>

        <!-- Tombol Cetak -->
        <div class="no-print pt-2 space-y-2">
            <button onclick="window.print()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2">
                🖨️ Cetak / Simpan PDF
            </button>
            <a href="{{ route('withdrawals.index') }}" class="w-full block text-center py-2 text-slate-500 hover:text-slate-800 font-bold text-xs">
                ← Kembali
            </a>
        </div>

    </div>

</body>
</html>