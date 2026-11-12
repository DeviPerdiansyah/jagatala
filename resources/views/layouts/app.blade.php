<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bank Sampah Jagatala')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- LOGO RESMI BANK SAMPAH JAGATALA -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 hover:opacity-90 transition">
                    <img src="{{ asset('images/logo-jagatala.png') }}" 
                         alt="Logo Bank Sampah Jagatala" 
                         class="h-10 w-auto object-contain">
                    <span class="font-black text-xl text-slate-900 tracking-tight">Jagatala<span class="text-emerald-600">.</span></span>
                </a>

                <!-- Navigation Links (Jika sudah login) -->
                @auth
                <nav class="hidden md:flex space-x-6 text-sm font-medium items-center h-full">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="h-full flex items-center transition py-5 border-b-2 {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-bold border-emerald-600' : 'text-slate-600 hover:text-emerald-600 border-transparent' }}">
                       Dashboard
                    </a>

                    @if(auth()->user()->role === 'pengelola')
                       <!-- Data Nasabah + Badge Pendaftaran Baru -->
@php
    $pendingNasabahCount = \App\Models\User::where('role', 'nasabah')->where('status', 'pending')->count();
@endphp

<a href="{{ route('members.index') }}" 
   class="h-full flex items-center gap-1.5 transition py-5 border-b-2 {{ request()->routeIs('members.*') ? 'text-emerald-600 font-bold border-emerald-600' : 'text-slate-600 hover:text-emerald-600 border-transparent' }}">
   <span>Data Nasabah</span>

   @if($pendingNasabahCount > 0)
       <span class="px-2 py-0.5 text-[10px] font-extrabold bg-rose-500 text-white rounded-full shadow-sm animate-pulse" title="{{ $pendingNasabahCount }} Pendaftaran Baru Menunggu Konfirmasi">
           {{ $pendingNasabahCount }} Baru
       </span>
   @endif
</a>

                        <!-- Input Setoran -->
                        <a href="{{ route('transactions.create') }}" 
                           class="h-full flex items-center transition py-5 border-b-2 {{ request()->routeIs('transactions.create') ? 'text-emerald-600 font-bold border-emerald-600' : 'text-slate-600 hover:text-emerald-600 border-transparent' }}">
                           Input Setoran
                        </a>
                    @endif

                    <!-- Riwayat Transaksi -->
                    <a href="{{ route('transactions.index') }}" 
                       class="h-full flex items-center transition py-5 border-b-2 {{ request()->routeIs('transactions.index') ? 'text-emerald-600 font-bold border-emerald-600' : 'text-slate-600 hover:text-emerald-600 border-transparent' }}">
                       Riwayat Transaksi
                    </a>
                    
                    <!-- Link Tarik Saldo + Badge Notifikasi (Active State Handling) -->
                    @php
                        $pendingCount = auth()->user()->role === 'pengelola' 
                            ? \App\Models\Withdrawal::where('status', 'pending')->count()
                            : \App\Models\Withdrawal::where('user_id', auth()->id())->where('status', 'pending')->count();
                    @endphp
                    
                   <a href="{{ route('withdrawals.index') }}" 
   class="h-full flex items-center gap-1.5 transition py-5 border-b-2 {{ request()->routeIs('withdrawals.*') ? 'text-emerald-600 font-bold border-emerald-600' : 'text-slate-600 hover:text-emerald-600 border-transparent' }}">
    
    <!-- NAMA MENU DINAMIS BERDASARKAN ROLE -->
    <span>{{ auth()->user()->role === 'pengelola' ? 'Pencairan Saldo' : 'Tarik Saldo' }}</span>

    @if($pendingCount > 0)
        <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-500 text-white rounded-full shadow-sm animate-pulse">
            {{ $pendingCount }}
        </span>
    @endif
</a>
                </nav>

                <div class="flex items-center gap-4">
                    <!-- Link Ke Profil -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-800 leading-none">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-400 capitalize mt-0.5">{{ auth()->user()->role }}</div>
                        </div>
                    </a>

                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 font-semibold hover:underline">Logout</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        

        <!-- BANNER MAINTENANCE HARGA REAL-TIME -->
        <div id="priceMaintenanceBanner" class="hidden mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-2xl shadow-sm animate-pulse">
            <div class="flex items-start gap-3">
                <span class="text-amber-600 text-xl">⚠️</span>
                <div>
                    <h4 class="text-sm font-extrabold text-amber-900">Pembaruan Harga Sampah Sedang Berlangsung</h4>
                    <p id="priceMaintenanceMessage" class="text-xs text-amber-700 mt-0.5">
                        Saat ini pengelola sedang melakukan penyesuaian patokan harga sampah. Transaksi setoran sementara ditangguhkan sampai proses selesai.
                    </p>
                </div>
            </div>
        </div>

        <script>
            async function checkPriceMaintenance() {
                try {
                    const res = await fetch("{{ route('settings.price_maintenance_status') }}");
                    const data = await res.json();
                    const banner = document.getElementById('priceMaintenanceBanner');
                    
                    if (banner) {
                        if (data.is_maintenance) {
                            banner.classList.remove('hidden');
                            document.getElementById('priceMaintenanceMessage').innerText = data.message;
                        } else {
                            banner.classList.add('hidden');
                        }
                    }
                } catch (err) {
                    console.error('Gagal mengecek status maintenance:', err);
                }
            }

            // Cek saat halaman dimuat & periksa otomatis tiap 5 detik
            document.addEventListener("DOMContentLoaded", function() {
                checkPriceMaintenance();
                setInterval(checkPriceMaintenance, 5000);
            });
        </script>

        @yield('content')
    </main>

<!-- Footer Tugas Akhir (Opsi 2 - Simetris Tengah) -->
<footer class="bg-white border-t border-slate-200 py-4 text-xs text-slate-500">
    <div class="max-w-7xl mx-auto px-4 text-center space-y-1.5">
        <div class="flex items-center justify-center gap-2">
            &copy; {{ date('Y') }} <span class="font-extrabold text-slate-800">Bank Sampah Jagatala</span>
            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-semibold border border-slate-200">v1.0</span>
        </div>
        <div class="text-[11px] text-slate-400 flex items-center justify-center gap-2">
            <span>Tugas Akhir</span>
            <span>&bull;</span>
            <span>Developed by <strong class="font-extrabold text-emerald-600">Devi Perdiansyah</strong> <span class="font-bold text-slate-500">(NIM. 224260055)</span></span>
        </div>
        <div class="text-[10px] text-slate-400 font-medium">
            Program Studi Sistem Informasi &bull; Fakultas Teknologi Informasi
        </div>
    </div>
</footer>

    <!-- SWEETALERT2 INTEGRATION -->
    <script>
        // 1. POP-UP DARI SESSION FLASH (SUCCESS & ERROR)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif

        // 2. HELPER GLOBAL UNTUK KONFIRMASI (HAPUS / APPROVE / REJECT)
        function confirmAction(event, message = 'Apakah Anda yakin ingin melanjutkan?', confirmText = 'Ya, Lanjutkan!') {
            event.preventDefault();
            const form = event.target.closest('form');

            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669', // Emerald 600
                cancelButtonColor: '#64748b',  // Slate 500
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-4 py-2 text-sm rounded-lg font-medium',
                    cancelButton: 'px-4 py-2 text-sm rounded-lg font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>