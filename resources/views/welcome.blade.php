<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah Jagatala - Sistem Tabungan Sampah Modern</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Background Pattern Bintik Halus ALAM */
        .bg-nature-pattern {
            background-image: radial-gradient(rgba(16, 185, 129, 0.08) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
        }

        /* Transition Smooth Fade Slider */
        .slide-item {
            transition: opacity 0.8s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50/70 bg-nature-pattern font-sans text-slate-800 min-h-screen flex flex-col selection:bg-emerald-200 selection:text-emerald-900 relative overflow-x-hidden">

    <!-- DEKORASI EFEK LIGHTING ALAM (GLOW SEGAR) -->
    <div class="fixed top-[-10%] left-1/2 -translate-x-1/2 w-[800px] h-[450px] bg-emerald-200/40 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="fixed top-[45%] right-[-5%] w-[500px] h-[500px] bg-teal-200/30 rounded-full blur-[150px] pointer-events-none -z-10"></div>

    <!-- NAVBAR GLASSMORPHISM TERANG -->
    <nav class="bg-white/80 backdrop-blur-xl border-b border-emerald-100/60 px-6 py-4 sticky top-0 z-50 transition-all shadow-sm">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            
            <!-- BRANDING LOGO -->
            <a href="#" class="flex items-center gap-3.5 group">
                <div class="p-2 bg-white rounded-2xl border border-emerald-100 shadow-md shadow-emerald-500/5 group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('images/logo-jagatala.png') }}" alt="Logo Bank Sampah Jagatala" class="w-8 h-8 object-contain">
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-lg text-slate-900 tracking-tight">Jagatala<span class="text-emerald-600">.</span></span>
                        <span class="text-[9px] bg-emerald-100 text-emerald-800 border border-emerald-200/80 px-2 py-0.5 rounded-full font-extrabold tracking-widest uppercase">
                            RW 18
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium">Desa Tanimulya, Ngamprah</span>
                </div>
            </a>
            
            <!-- CTAS NAVBAR -->
            <div class="flex items-center gap-3 text-xs md:text-sm font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                        <span>Dashboard Saya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-600 transition px-3 py-2 rounded-xl hover:bg-emerald-50/50">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                        <span>Mulai Menabung</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO HEADER TERANG DENGAN SLIDER GAMBAR AUTOMATIS -->
    <header class="relative pt-12 pb-20 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            
            <!-- KOLOM KIRI: TEKS & CALL TO ACTION -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- BADGE HIGHLIGHT -->
                <div class="inline-flex items-center gap-2 text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 px-4 py-2 rounded-full shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Inovasi Digital Pengelolaan Sampah RW 18</span>
                </div>
                
                <!-- JUDUL UTAMA -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] text-slate-900">
                    Kumpulkan Sampahnya, <br>
                    <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 bg-clip-text text-transparent">
                        Ubah Jadi Uang di Tabunganmu
                    </span>
                </h1>
                
                <!-- DESKRIPSI RINGKAS -->
                <p class="text-xs md:text-sm text-slate-600 max-w-xl font-medium leading-relaxed mx-auto lg:mx-0">
                    Setiap kilogram sampah anorganik yang disetor langsung dikonversi menjadi rupiah di rekening tabungan digital Jagatala. Hasil penimbangan transparan, akurat, dan dapat dicairkan kapan saja.
                </p>
                
                <!-- TOMBOL UTAMA -->
                <div class="pt-2 flex flex-wrap justify-center lg:justify-start items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs md:text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/20 transition transform hover:-translate-y-0.5">
                            <span>Akses Akun Saya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs md:text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/20 transition transform hover:-translate-y-0.5">
                            <span>Buka Rekening Nasabah</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#langkah" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs md:text-sm px-7 py-3.5 rounded-2xl shadow-sm transition">
                            <span>Pelajari Alur</span>
                        </a>
                    @endauth
                </div>

                <!-- STATS STATIS -->
                <div class="pt-4 grid grid-cols-3 gap-3 text-left max-w-md mx-auto lg:mx-0">
                    <div class="p-3 rounded-2xl bg-white/80 border border-emerald-100 shadow-sm backdrop-blur-md">
                        <div class="text-xl font-black text-emerald-600">100%</div>
                        <div class="text-[10px] text-slate-500 font-semibold">Transparan</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 border border-emerald-100 shadow-sm backdrop-blur-md">
                        <div class="text-xl font-black text-teal-600">Real-time</div>
                        <div class="text-[10px] text-slate-500 font-semibold">Pencatatan</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 border border-emerald-100 shadow-sm backdrop-blur-md">
                        <div class="text-xl font-black text-emerald-600">Cairkan</div>
                        <div class="text-[10px] text-slate-500 font-semibold">Uang Kapan Saja</div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: CAROUSEL 5 GAMBAR BERGILIR AUTOMATIS -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    
                    <!-- CONTAINER SLIDER UTAMA -->
                    <div class="overflow-hidden rounded-3xl border-4 border-white shadow-2xl shadow-emerald-600/15 bg-slate-900 relative h-[380px] group">
                        
                        <!-- SLIDE 1: Foto Tim Pengelola / Pos Bank Sampah Jagatala -->
                        <div class="slide-item absolute inset-0 opacity-100 z-10 transition-opacity duration-700">
                            <img src="{{ asset('images/pos-jagatala.jpg') }}" 
                                 alt="Tim Bank Sampah Jagatala RW 18" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/90 backdrop-blur-md px-2.5 py-1 rounded-lg">Pos Operasional</span>
                                <p class="text-xs font-bold mt-1.5 text-slate-100">Tim Tim Bank Sampah Jagatala RW 18 Desa Tanimulya</p>
                            </div>
                        </div>

                        <!-- SLIDE 2: Penimbangan & Pengangkutan Sampah -->
                        <div class="slide-item absolute inset-0 opacity-0 z-0 transition-opacity duration-700">
                            <img src="{{ asset('images/pengangkutan-sampah.jpg') }}" 
                                 alt="Penimbangan & Pengangkutan Sampah" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/90 backdrop-blur-md px-2.5 py-1 rounded-lg">Pengumpulan Sampah</span>
                                <p class="text-xs font-bold mt-1.5 text-slate-100">Penimbangan & Pengangkutan Hasil Setoran Sampah</p>
                            </div>
                        </div>

                        <!-- SLIDE 3: Pemilahan Sampah oleh Warga/Anak-anak -->
                        <div class="slide-item absolute inset-0 opacity-0 z-0 transition-opacity duration-700">
                            <img src="{{ asset('images/pemilahan-kertas.jpg') }}" 
                                 alt="Pemilahan Sampah Kertas & Kardus" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/90 backdrop-blur-md px-2.5 py-1 rounded-lg">Pilihan Komoditas</span>
                                <p class="text-xs font-bold mt-1.5 text-slate-100">Proses Pemilahan Sampah Kertas, Buku & Kardus</p>
                            </div>
                        </div>

                        <!-- SLIDE 4: Sosialisasi & Edukasi Pilah Sampah -->
                        <div class="slide-item absolute inset-0 opacity-0 z-0 transition-opacity duration-700">
                            <img src="{{ asset('images/sosialisasi-kategori.jpg') }}" 
                                 alt="Sosialisasi Kategori Sampah" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/90 backdrop-blur-md px-2.5 py-1 rounded-lg">Edukasi Warga</span>
                                <p class="text-xs font-bold mt-1.5 text-slate-100">Sosialisasi Pemilahan Sampah Sesuai Kategori</p>
                            </div>
                        </div>

                        <!-- SLIDE 5: Musyawarah & Forum Aksi Jaga Bumi -->
                        <div class="slide-item absolute inset-0 opacity-0 z-0 transition-opacity duration-700">
                            <img src="{{ asset('images/diskusi-pengelola.jpg') }}" 
                                 alt="Diskusi & Koordinasi Pengelola" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/90 backdrop-blur-md px-2.5 py-1 rounded-lg">Aksi Jaga Bumi</span>
                                <p class="text-xs font-bold mt-1.5 text-slate-100">Koordinasi & Perencanaan Program Lingkungan</p>
                            </div>
                        </div>

                        <!-- INDICATOR DOTS (NAVIGASI) -->
                        <div class="absolute top-4 right-4 z-30 flex items-center gap-1.5 bg-slate-900/50 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                            <button onclick="setSlide(0)" class="slide-dot w-2 h-2 rounded-full bg-white transition-all" aria-label="Slide 1"></button>
                            <button onclick="setSlide(1)" class="slide-dot w-2 h-2 rounded-full bg-white/40 transition-all" aria-label="Slide 2"></button>
                            <button onclick="setSlide(2)" class="slide-dot w-2 h-2 rounded-full bg-white/40 transition-all" aria-label="Slide 3"></button>
                            <button onclick="setSlide(3)" class="slide-dot w-2 h-2 rounded-full bg-white/40 transition-all" aria-label="Slide 4"></button>
                            <button onclick="setSlide(4)" class="slide-dot w-2 h-2 rounded-full bg-white/40 transition-all" aria-label="Slide 5"></button>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </header>

    <!-- MAIN KONTEN UTAMA -->
    <main class="max-w-6xl mx-auto px-6 space-y-16 flex-grow relative z-10">

        <!-- MISI OPERASIONAL UTAMA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- MISI 1 -->
            <div class="p-6 rounded-3xl bg-white border border-emerald-100/80 shadow-md shadow-emerald-500/5 hover:border-emerald-300 transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 font-bold group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Wadah Edukasi</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    Meningkatkan kesadaran dan kepedulian masyarakat dalam pemilahan sampah dapur harian secara mandiri dari rumah.
                </p>
            </div>

            <!-- MISI 2 -->
            <div class="p-6 rounded-3xl bg-white border border-teal-100/80 shadow-md shadow-teal-500/5 hover:border-teal-300 transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-teal-50 border border-teal-100 text-teal-600 rounded-2xl flex items-center justify-center mb-4 font-bold group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Reduksi Lingkungan</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    Menekan angka penumpukan pembuangan akhir residu sampah anorganik agar kenyamanan wilayah RW 18 tetap terjaga.
                </p>
            </div>

            <!-- MISI 3 -->
            <div class="p-6 rounded-3xl bg-white border border-emerald-100/80 shadow-md shadow-emerald-500/5 hover:border-emerald-300 transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 font-bold group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Sirkular Ekonomi</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    Mengubah komoditas yang tidak terpakai menjadi aset tabungan digital bernilai rupiah untuk kebutuhan keluarga.
                </p>
            </div>
        </div>

        <!-- ALUR PENYETORAN SAMPAH -->
        <div id="langkah" class="space-y-8 pt-4">
            <div class="text-center space-y-2">
                <span class="text-[10px] text-emerald-800 font-extrabold uppercase tracking-widest bg-emerald-100 border border-emerald-200 px-3 py-1 rounded-full">
                    SOP Penyetoran
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">4 Langkah Mudah Setor Sampah</h2>
                <p class="text-xs text-slate-500 max-w-lg mx-auto">Sistem digital Jagatala memproses setoran Anda secara cepat dan transparan</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- STEP 1 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-emerald-300 transition relative overflow-hidden group">
                    <div class="text-3xl font-black text-slate-100 group-hover:text-emerald-100 transition-colors absolute top-3 right-4 select-none">01</div>
                    <div class="text-2xl mb-3">📦</div>
                    <h4 class="font-bold text-slate-800 text-sm">Pilah Mandiri</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Kumpulkan dan pisahkan jenis sampah anorganik Anda dari rumah.</p>
                </div>
                <!-- STEP 2 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-emerald-300 transition relative overflow-hidden group">
                    <div class="text-3xl font-black text-slate-100 group-hover:text-emerald-100 transition-colors absolute top-3 right-4 select-none">02</div>
                    <div class="text-2xl mb-3">⚖️</div>
                    <h4 class="font-bold text-slate-800 text-sm">Timbang Digital</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Bawa sampah ke unit pengelola untuk ditimbang secara akurat.</p>
                </div>
                <!-- STEP 3 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-emerald-300 transition relative overflow-hidden group">
                    <div class="text-3xl font-black text-slate-100 group-hover:text-emerald-100 transition-colors absolute top-3 right-4 select-none">03</div>
                    <div class="text-2xl mb-3">💻</div>
                    <h4 class="font-bold text-slate-800 text-sm">Kalkulasi Otomatis</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Sistem menghitung nilai rupiah berdasarkan kategori & berat.</p>
                </div>
                <!-- STEP 4 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-emerald-300 transition relative overflow-hidden group">
                    <div class="text-3xl font-black text-slate-100 group-hover:text-emerald-100 transition-colors absolute top-3 right-4 select-none">04</div>
                    <div class="text-2xl mb-3">💰</div>
                    <h4 class="font-bold text-slate-800 text-sm">Saldo Bertambah</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Dana masuk otomatis ke rekening nasabah & dapat dicairkan.</p>
                </div>
            </div>
        </div>

        <!-- BROSUR KOMODITAS SAMPAH -->
        <div class="p-8 rounded-3xl bg-white border border-emerald-100 shadow-xl shadow-emerald-500/5 space-y-6">
            <div>
                <span class="text-[10px] text-amber-800 font-extrabold uppercase tracking-widest bg-amber-100 border border-amber-200 px-3 py-1 rounded-full">
                    Panduan Nasabah
                </span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-2">Komoditas Sampah Yang Diterima</h3>
                <p class="text-xs text-slate-500">Pastikan sampah dalam keadaan bersih dan kering saat disetorkan:</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- PLASTIK -->
                <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200/60 hover:border-amber-300 transition">
                    <span class="inline-flex items-center gap-1.5 font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-lg text-xs mb-3 border border-amber-200">
                        📦 Kategori Plastik
                    </span>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        Botol Air Minum, Ember, Perabotan Rumah Tangga, Tutup Botol, Kotak Makanan, Gelas Plastik, Sedotan, Botol Shampoo/Sabun, Wadah Kosmetik, Tutup Galon, Toples Kue, Helm Bekas, Sendok/Garpu Plastik, Paralon, Botol Tumblr, Gelas Sekali Pakai, Kresek, Plastik Bening, Kaset Tape, Kotak CD/DVD, Galon.
                    </p>
                </div>
                <!-- KERTAS -->
                <div class="p-5 rounded-2xl bg-sky-50/40 border border-sky-200/60 hover:border-sky-300 transition">
                    <span class="inline-flex items-center gap-1.5 font-bold text-sky-800 bg-sky-100 px-3 py-1 rounded-lg text-xs mb-3 border border-sky-200">
                        📄 Kategori Kertas
                    </span>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        Kardus Coklat, Trey Telur, Kertas HVS/Arsip, Duplex, Kones, Majalah Bekas, Koran Bekas, Buku Pelajaran Lama, Kertas Sticker, Kertas Semen, Kotak Susu/Sereal (Tetra Pak), dan Tabloid Berita.
                    </p>
                </div>
                <!-- LAINNYA -->
                <div class="p-5 rounded-2xl bg-emerald-50/40 border border-emerald-200/60 hover:border-emerald-300 transition">
                    <span class="inline-flex items-center gap-1.5 font-bold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-lg text-xs mb-3 border border-emerald-200">
                        🛢️ Kategori Lainnya
                    </span>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        Minyak Goreng Jelantah Rumah Tangga, Kabel Tembaga/Struktur, Besi Tua, Kaleng Minuman Ringan, Seng Kanopi, Alumunium, Panci/Wajan Bekas, Accu Motor/Mobil, dan Sampah Campuran Bernilai Ekonomis.
                    </p>
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER TERANG MEWAH DENGAN IDENTITAS TA -->
    <footer class="mt-20 border-t border-slate-200 bg-white text-xs text-slate-500 py-10 relative z-10">
        <div class="max-w-6xl mx-auto px-6 space-y-8">
            
            <!-- KARTU INFORMASI KONTAK LOKASI -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center md:text-left">
                <div>
                    <h4 class="font-extrabold text-slate-900 text-sm">Bank Sampah Jagatala</h4>
                    <p class="text-slate-500 text-xs mt-0.5">PCI II A6/26 RT 02 RW 18 Desa Tanimulya, Ngamprah, Bandung Barat</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 shadow-sm">
                        📞 <strong>+62 895-6250-90769</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 shadow-sm">
                        📸 <strong>@banksampah.jagatala</strong>
                    </span>
                </div>
            </div>

            <!-- FOOTER IDENTITY (TUGAS AKHIR) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 text-[11px] text-slate-500">
                <div>
                    &copy; {{ date('Y') }} <strong class="text-slate-800">Bank Sampah Jagatala</strong> <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-bold border border-emerald-200">v1.0</span>
                </div>
                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-1.5">
                    <span>Tugas Akhir</span>
                    <span class="text-slate-300">&bull;</span>
                    <span>Developed by <strong class="text-emerald-700 font-extrabold">Devi Perdiansyah</strong></span>
                    <span class="text-slate-500 font-bold">(NIM. 224260055)</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500">Sistem Informasi</span>
                </div>
            </div>

        </div>
    </footer>

    <!-- SCRIPT AUTOMATIC SLIDER -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide-item');
        const dots = document.querySelectorAll('.slide-dot');
        const totalSlides = slides.length;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.remove('opacity-0', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'z-0');
                }
            });

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('bg-white/40', 'w-2');
                    dot.classList.add('bg-emerald-400', 'w-5');
                } else {
                    dot.classList.remove('bg-emerald-400', 'w-5');
                    dot.classList.add('bg-white/40', 'w-2');
                }
            });

            currentSlide = index;
        }

        function nextSlide() {
            let nextIndex = (currentSlide + 1) % totalSlides;
            showSlide(nextIndex);
        }

        function setSlide(index) {
            showSlide(index);
            resetTimer();
        }

        function startTimer() {
            slideInterval = setInterval(nextSlide, 3500); // Pergantian setiap 3.5 detik
        }

        function resetTimer() {
            clearInterval(slideInterval);
            startTimer();
        }

        // Jalankan slider saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', () => {
            showSlide(0);
            startTimer();
        });
    </script>

</body>
</html>