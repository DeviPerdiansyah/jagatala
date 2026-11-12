@extends('layouts.app')

@section('title', 'Kategori Sampah - Bank Sampah Jagatala')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    @php
        $isPriceMaintenance = Illuminate\Support\Facades\Cache::get('price_maintenance_mode', false);
    @endphp

    <!-- HEADER HALAMAN & TOGGLE STATUS PEMBARUAN HARGA -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Kelola Kategori Sampah
                </h1>
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full">
                    Price Master
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Atur patokan harga dan satuan transaksi (Kg/Pcs) serta kontrol ketersediaan sistem.
            </p>
        </div>

        <!-- TOGGLE SWITCH PRICE MAINTENANCE -->
        <form id="formMaintenance" action="{{ route('settings.toggle_price_maintenance') }}" method="POST" class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-200/60 w-fit">
            @csrf
            <div class="text-left sm:text-right pl-1">
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sistem Transaksi</div>
                <div class="text-xs font-black flex items-center gap-1.5 mt-0.5 {{ $isPriceMaintenance ? 'text-amber-600' : 'text-emerald-600' }}">
                    <span class="w-2 h-2 rounded-full {{ $isPriceMaintenance ? 'bg-amber-500 animate-ping' : 'bg-emerald-500' }}"></span>
                    {{ $isPriceMaintenance ? 'Pembaruan Harga (Ditangguhkan)' : 'Normal / Buka' }}
                </div>
            </div>

            <button type="button" 
                onclick="promptMaintenance('{{ $isPriceMaintenance ? 'Buka kembali sistem transaksi untuk seluruh nasabah?' : 'Aktifkan mode pembaruan harga? Transaksi nasabah akan ditangguhkan sementara.' }}')"
                class="px-4 py-2.5 text-xs font-black rounded-xl transition shadow-md flex items-center gap-2 active:scale-95 {{ $isPriceMaintenance ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20' }}">
                <span>{{ $isPriceMaintenance ? '🔓 Buka Transaksi' : '🔒 Kunci & Update Harga' }}</span>
            </button>
        </form>
    </div>

    <!-- ALERT NOTIFIKASI -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm rounded-2xl font-bold flex items-center gap-2.5 shadow-sm">
            <span class="text-emerald-600 text-base">✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- FORM TAMBAH KATEGORI (PANEL KIRI) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900">➕ Tambah Jenis Sampah</h2>
                <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Input Baru</span>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Nama Sampah -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Nama Sampah / Item</label>
                    <input type="text" name="name" placeholder="misal: Botol Plastik PET / Galon" required 
                        class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>

                <!-- Jenis Sampah -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kategori / Sifat</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                        <option value="Anorganik"> Anorganik (Plastik, Kertas, Logam)</option>
                        <option value="Organik"> Organik (Sisa Makanan, Daun)</option>
                    </select>
                </div>

                <!-- Satuan Hitung -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Satuan Transaksi</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                        <option value="kg"> Kilogram (Kg)</option>
                        <option value="pcs"> Pcs / Buah</option>
                    </select>
                </div>

                <!-- Harga Per Satuan -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Harga Standard (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="price_per_kg" placeholder="3000" min="0" step="100" required 
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs font-black rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95 flex items-center justify-center gap-1.5">
                    <span>+ Simpan Kategori Baru</span>
                </button>
            </form>
        </div>

        <!-- TABEL DAFTAR KATEGORI (PANEL KANAN) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Daftar Jenis & Patokan Harga</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar jenis sampah yang terdaftar di sistem Bank Sampah</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 font-extrabold text-xs rounded-xl border border-slate-200/60">
                    Total: {{ count($categories) }} Item
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-4 px-6">Nama Sampah</th>
                            <th class="py-4 px-6">Kategori Sifat</th>
                            <th class="py-4 px-6">Harga / Satuan</th>
                            <th class="py-4 px-6 text-center">Aksi Pengelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="py-4 px-6 font-extrabold text-slate-800 group-hover:text-emerald-700 transition">
                                    {{ $category->name }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if(($category->type ?? 'Anorganik') === 'Organik')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200/80 uppercase tracking-wider">
                                            🍃 Organik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-200/80 uppercase tracking-wider">
                                            ♻️ Anorganik
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-black text-emerald-600 whitespace-nowrap text-sm">
                                    <span class="text-xs text-slate-400 font-normal">Rp</span> {{ number_format($category->price_per_kg, 0, ',', '.') }}
                                    <span class="text-[10px] text-slate-400 font-medium">/ {{ $category->unit ?? 'kg' }}</span>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Edit -->
                                        <button type="button" 
                                            onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type ?? 'Anorganik' }}', '{{ $category->unit ?? 'kg' }}', {{ $category->price_per_kg }})"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200/60 flex items-center gap-1">
                                            ✏️ Edit
                                        </button>

                                        <!-- Tombol Hapus dengan Custom Modal -->
                                        <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                onclick="confirmDeleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" 
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl transition border border-rose-200/60 flex items-center gap-1">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400 font-medium">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">🏷️</div>
                                    Belum ada kategori sampah yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div id="modalEdit" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition duration-300">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in duration-200">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-base">Edit Kategori Sampah</h3>
                    <p class="text-xs text-slate-400">Perbarui nama, jenis, satuan, atau harga</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 font-black text-base flex items-center justify-center transition">&times;</button>
            </div>

            <form id="formEditCategory" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Nama Sampah</label>
                    <input type="text" id="edit_name" name="name" required 
                        class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Sifat / Jenis</label>
                    <select id="edit_type" name="type" required class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                        <option value="Anorganik">Anorganik</option>
                        <option value="Organik">Organik</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Satuan Transaksi</label>
                    <select id="edit_unit" name="unit" required class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition bg-white">
                        <option value="kg">Kilogram (Kg)</option>
                        <option value="pcs">Pcs / Buah</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Harga Standard (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" id="edit_price_per_kg" name="price_per_kg" min="0" step="100" required 
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs font-black rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS CUSTOM PREMIER -->
    <div id="modalDelete" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition duration-300">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mx-auto border border-rose-100">
                🗑️
            </div>
            
            <div>
                <h3 class="font-black text-slate-900 text-base">Hapus Kategori Sampah?</h3>
                <p class="text-xs text-slate-500 mt-1" id="deleteTargetText">
                    Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="flex items-center justify-center gap-2.5 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="button" id="btnConfirmDelete" class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-md shadow-rose-600/20 transition active:scale-95">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI MAINTENANCE CUSTOM -->
    <div id="modalMaintenance" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition duration-300">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mx-auto border border-amber-100">
                ⚙️
            </div>
            
            <div>
                <h3 class="font-black text-slate-900 text-base">Ubah Status Transaksi?</h3>
                <p class="text-xs text-slate-500 mt-1" id="maintenanceText">
                    Ubah status pembaruan harga transaksi?
                </p>
            </div>

            <div class="flex items-center justify-center gap-2.5 pt-2">
                <button type="button" onclick="closeMaintenanceModal()" class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="button" id="btnConfirmMaintenance" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT HANDLER MODAL CUSTOM -->
<script>
    let currentDeleteId = null;

    // Modal Edit Handler
    function openEditModal(id, name, type, unit, price) {
        document.getElementById('formEditCategory').action = '/categories/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_unit').value = unit;
        document.getElementById('edit_price_per_kg').value = price;
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    // Modal Delete Handler
    function confirmDeleteCategory(id, name) {
        currentDeleteId = id;
        document.getElementById('deleteTargetText').innerText = `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('modalDelete').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('modalDelete').classList.add('hidden');
        currentDeleteId = null;
    }

    document.getElementById('btnConfirmDelete').addEventListener('click', function() {
        if(currentDeleteId) {
            document.getElementById('delete-form-' + currentDeleteId).submit();
        }
    });

    // Modal Maintenance Handler
    function promptMaintenance(message) {
        document.getElementById('maintenanceText').innerText = message;
        document.getElementById('modalMaintenance').classList.remove('hidden');
    }

    function closeMaintenanceModal() {
        document.getElementById('modalMaintenance').classList.add('hidden');
    }

    document.getElementById('btnConfirmMaintenance').addEventListener('click', function() {
        document.getElementById('formMaintenance').submit();
    });
</script>
@endsection