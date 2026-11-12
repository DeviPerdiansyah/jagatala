@extends('layouts.app')

@section('title', 'Input Setoran Sampah - Bank Sampah Jagatala')

@section('content')
<!-- CDN Tom Select Style & Script -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.75rem 0.875rem !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    .ts-control.focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }
    .ts-dropdown {
        border-radius: 1rem !important;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
        border: 1px solid #f1f5f9 !important;
        padding: 0.5rem !important;
    }
</style>

<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Input Setoran Sampah</h1>
                <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full">
                    PENCATATAN SETORAN
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Catat hasil penimbangan/perhitungan multi-item sampah nasabah dan konfirmasi penambahan saldo secara otomatis.</p>
        </div>

        <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200/60 w-fit flex items-center gap-1.5">
            <span>← Kembali ke Riwayat</span>
        </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- PANEL KIRI: FORM PENIMBANGAN MULTI-ITEM -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm relative space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>⚖️</span> Form Penimbangan / Hitung Sampah
                </h2>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Langkah 1 dari 2</span>
            </div>

            <form id="formTransaction" action="{{ route('transactions.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- PILIH NASABAH -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                        Cari / Pilih Nasabah <span class="text-rose-500">*</span>
                    </label>
                    <select id="user_id" name="user_id" required class="w-full">
                        <option value="" data-name="Belum dipilih" data-email="-">-- Cari / Pilih Nasabah --</option>
                        @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}" data-name="{{ $nasabah->name }}" data-email="{{ $nasabah->email }}">
                                {{ $nasabah->name }} ({{ $nasabah->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- CONTAINER DINAMIS ITEM SAMPAH -->
                <div id="items-container" class="space-y-4">
                    <!-- BARIS SAMPAH #1 (DEFAULT) -->
                    <div class="item-row p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3 relative">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black text-emerald-700 uppercase tracking-wider row-title">Item Sampah #1</span>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Jenis Sampah *</label>
                            <select name="items[0][category_id]" class="category-select w-full" required>
                                <option value="" data-price="0" data-name="Belum dipilih" data-type="-" data-unit="kg">-- Pilih Jenis Sampah --</option>
                                @foreach($categories as $cat)
                                    @php $unit = $cat->unit ?? 'kg'; @endphp
                                    <option value="{{ $cat->id }}" data-price="{{ $cat->price_per_kg }}" data-name="{{ $cat->name }}" data-type="{{ $cat->type }}" data-unit="{{ $unit }}">
                                        {{ $cat->name }} [{{ $cat->type }}] — Rp {{ number_format($cat->price_per_kg, 0, ',', '.') }}/{{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 label-qty">Jumlah / Berat *</label>
                            <div class="relative">
                                <input type="number" name="items[0][weight]" step="0.01" min="0.01" placeholder="Masukkan Jumlah" required
                                    class="weight-input w-full pl-3.5 pr-14 py-2.5 text-xs font-black rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-900">
                                <span class="unit-badge absolute right-3.5 top-2.5 text-xs font-black text-slate-400 uppercase">KG</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL TAMBAH BARIS SAMPAH -->
                <button type="button" id="btnAddItem" class="w-full py-3 border-2 border-dashed border-emerald-500/60 hover:border-emerald-600 text-emerald-700 hover:bg-emerald-50/50 font-extrabold text-xs rounded-2xl transition flex items-center justify-center gap-2">
                    ➕ Tambah Jenis Sampah Lain
                </button>

                <!-- ACTION BUTTON -->
                <div class="pt-2">
                    <button type="button" onclick="openConfirmModal()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95 flex items-center justify-center gap-2">
                        <span>✨ Proses & Tambah Saldo Nasabah</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- PANEL KANAN: RINGKASAN STRUK TRANSAKSI -->
        <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 sm:p-7 rounded-3xl border border-slate-700 shadow-xl space-y-5 relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/80">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400 text-base">🧾</span>
                    <h3 class="font-extrabold text-sm tracking-wide">Ringkasan Struk Transaksi</h3>
                </div>
                <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-md">
                    Live Calculation
                </span>
            </div>

            <div class="space-y-4 text-xs">
                <!-- NASABAH INFO -->
                <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/60 space-y-1">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Nasabah Penerima</span>
                    <div id="receiptNasabahName" class="font-black text-sm text-slate-100">Belum dipilih</div>
                    <div id="receiptNasabahEmail" class="text-[11px] text-slate-400">-</div>
                </div>

                <!-- DAFTAR ITEM SAMPAH -->
                <div class="space-y-2">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Rincian Items Sampah</span>
                    <div id="receiptItemsList" class="space-y-2 max-h-52 overflow-y-auto pr-1">
                        <div class="text-xs text-slate-500 italic">Belum ada item ditambahkan.</div>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-700 my-3"></div>

                <!-- TOTAL SALDO -->
                <div>
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Total Tambahan Saldo</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-extrabold text-emerald-400">Rp</span>
                        <span id="receiptGrandTotal" class="text-3xl font-black text-emerald-400 tracking-tight">0</span>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-emerald-900/30 border border-emerald-500/20 rounded-2xl text-[11px] text-emerald-200/80 flex items-start gap-2">
                <span class="text-xs">💡</span>
                <span>Saldo nasabah akan otomatis bertambah saat transaksi disimpan.</span>
            </div>
        </div>

    </div>

    <!-- MODAL KONFIRMASI CUSTOM -->
    <div id="modalConfirmTransaction" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition duration-300">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in duration-200">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto border border-emerald-100">
                💰
            </div>

            <div class="text-center space-y-1">
                <h3 class="font-black text-slate-900 text-base">Konfirmasi Setoran Sampah</h3>
                <p class="text-xs text-slate-500">Pastikan data penimbangan/perhitungan sudah sesuai sebelum menambahkan saldo nasabah.</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-3 text-xs">
                <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-bold">Nasabah:</span>
                    <span id="modalNasabah" class="font-black text-slate-800 text-sm"></span>
                </div>

                <!-- RINCIAN ITEM SAMPAH DI DALAM MODAL -->
                <div class="space-y-1.5">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Detail Jenis Sampah:</span>
                    <div id="modalItemsDetail" class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                        <!-- Item akan dimasukkan otomatis lewat JavaScript -->
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-2 flex justify-between items-center">
                    <span class="font-black text-slate-700">Total Saldo Ditambah:</span>
                    <span id="modalTotal" class="font-black text-base text-emerald-600"></span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2.5 pt-1">
                <button type="button" onclick="closeConfirmModal()" class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                    Periksa Kembali
                </button>
                <button type="button" onclick="submitTransaction()" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95">
                    Ya, Simpan Now
                </button>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT TOM SELECT & LIVE MULTI-ITEM CALCULATOR -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let userSelectInstance;
    let itemCounter = 1;

    document.addEventListener('DOMContentLoaded', function() {
        // Init TomSelect Nasabah
        userSelectInstance = new TomSelect("#user_id", {
            create: false,
            sortField: { field: "text", direction: "asc" },
            onChange: function() { updateReceipt(); }
        });

        // Init TomSelect Row Pertama
        initCategorySelect(document.querySelector('.category-select'));

        // Attach event listener berat row pertama
        document.querySelector('.weight-input').addEventListener('input', updateReceipt);

        // Event Tambah Row
        document.getElementById('btnAddItem').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const rowDiv = document.createElement('div');
            rowDiv.className = 'item-row p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3 relative';

            rowDiv.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-xs font-black text-emerald-700 uppercase tracking-wider row-title">Item Sampah #${itemCounter + 1}</span>
                    <button type="button" class="btn-remove-row text-xs text-rose-500 font-bold hover:underline">❌ Hapus</button>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Jenis Sampah *</label>
                    <select name="items[${itemCounter}][category_id]" class="category-select w-full" required>
                        <option value="" data-price="0" data-name="Belum dipilih" data-type="-" data-unit="kg">-- Pilih Jenis Sampah --</option>
                        @foreach($categories as $cat)
                            @php $unit = $cat->unit ?? 'kg'; @endphp
                            <option value="{{ $cat->id }}" data-price="{{ $cat->price_per_kg }}" data-name="{{ $cat->name }}" data-type="{{ $cat->type }}" data-unit="{{ $unit }}">
                                {{ $cat->name }} [{{ $cat->type }}] — Rp {{ number_format($cat->price_per_kg, 0, ',', '.') }}/{{ $unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 label-qty">Jumlah / Berat *</label>
                    <div class="relative">
                        <input type="number" name="items[${itemCounter}][weight]" step="0.01" min="0.01" placeholder="Masukkan Jumlah" required
                            class="weight-input w-full pl-3.5 pr-14 py-2.5 text-xs font-black rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-900">
                        <span class="unit-badge absolute right-3.5 top-2.5 text-xs font-black text-slate-400 uppercase">KG</span>
                    </div>
                </div>
            `;

            container.appendChild(rowDiv);
            
            // Init TomSelect & Event pada Row Baru
            initCategorySelect(rowDiv.querySelector('.category-select'));
            rowDiv.querySelector('.weight-input').addEventListener('input', updateReceipt);

            // Event Hapus Row
            rowDiv.querySelector('.btn-remove-row').addEventListener('click', function() {
                rowDiv.remove();
                reindexRows();
                updateReceipt();
            });

            itemCounter++;
            updateReceipt();
        });
    });

    function initCategorySelect(element) {
        new TomSelect(element, {
            create: false,
            sortField: { field: "text", direction: "asc" },
            onChange: function() { 
                const row = element.closest('.item-row');
                const selectedOpt = element.options[element.selectedIndex];
                const unit = selectedOpt ? (selectedOpt.getAttribute('data-unit') || 'kg') : 'kg';
                
                // Update badge unit & placeholder
                row.querySelector('.unit-badge').innerText = unit.toUpperCase();
                row.querySelector('.label-qty').innerText = unit.toLowerCase() === 'pcs' ? 'Jumlah (PCS) *' : 'Berat Timbangan (KG) *';
                
                updateReceipt(); 
            }
        });
    }

    function reindexRows() {
        document.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelector('.row-title').textContent = `Item Sampah #${index + 1}`;
        });
    }

    function updateReceipt() {
        // Nasabah Info
        const userSelect = document.getElementById('user_id');
        const selectedUserOpt = userSelect.options[userSelect.selectedIndex];
        document.getElementById('receiptNasabahName').innerText = selectedUserOpt ? selectedUserOpt.getAttribute('data-name') : 'Belum dipilih';
        document.getElementById('receiptNasabahEmail').innerText = selectedUserOpt ? selectedUserOpt.getAttribute('data-email') : '-';

        // Hitung Multi-Item
        const itemsList = document.getElementById('receiptItemsList');
        itemsList.innerHTML = '';
        let grandTotal = 0;
        let count = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const catSelect = row.querySelector('.category-select');
            const weightInput = row.querySelector('.weight-input');
            const selectedCatOpt = catSelect.options[catSelect.selectedIndex];

            const name = selectedCatOpt ? selectedCatOpt.getAttribute('data-name') : 'Belum dipilih';
            const price = parseFloat(selectedCatOpt ? selectedCatOpt.getAttribute('data-price') : 0) || 0;
            const unit = selectedCatOpt ? (selectedCatOpt.getAttribute('data-unit') || 'kg').toUpperCase() : 'KG';
            const weight = parseFloat(weightInput.value) || 0;

            if (catSelect.value && weight > 0) {
                count++;
                const subtotal = price * weight;
                grandTotal += subtotal;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex justify-between items-center bg-slate-800/80 p-2.5 rounded-xl border border-slate-700/60 text-xs';
                itemDiv.innerHTML = `
                    <div>
                        <div class="font-bold text-slate-100">${name}</div>
                        <div class="text-[10px] text-slate-400">${weight} ${unit} × Rp ${price.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="font-black text-emerald-400">Rp ${subtotal.toLocaleString('id-ID')}</div>
                `;
                itemsList.appendChild(itemDiv);
            }
        });

        if (count === 0) {
            itemsList.innerHTML = `<div class="text-xs text-slate-500 italic">Belum ada item ditambahkan.</div>`;
        }

        document.getElementById('receiptGrandTotal').innerText = grandTotal.toLocaleString('id-ID');
    }

    function openConfirmModal() {
        const form = document.getElementById('formTransaction');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const userSelect = document.getElementById('user_id');
        const selectedUserOpt = userSelect.options[userSelect.selectedIndex];
        const grandTotal = document.getElementById('receiptGrandTotal').innerText;

        // Set Nama Nasabah
        document.getElementById('modalNasabah').innerText = selectedUserOpt.getAttribute('data-name');
        document.getElementById('modalTotal').innerText = 'Rp ' + grandTotal;

        // Ambil Rincian Tiap Jenis Sampah
        const modalItemsDetail = document.getElementById('modalItemsDetail');
        modalItemsDetail.innerHTML = ''; // Reset list

        document.querySelectorAll('.item-row').forEach((row) => {
            const catSelect = row.querySelector('.category-select');
            const weightInput = row.querySelector('.weight-input');
            const selectedCatOpt = catSelect.options[catSelect.selectedIndex];

            const catName = selectedCatOpt ? selectedCatOpt.getAttribute('data-name') : '';
            const catPrice = parseFloat(selectedCatOpt ? selectedCatOpt.getAttribute('data-price') : 0) || 0;
            const unit = selectedCatOpt ? (selectedCatOpt.getAttribute('data-unit') || 'kg').toUpperCase() : 'KG';
            const weight = parseFloat(weightInput.value) || 0;

            if (catSelect.value && weight > 0) {
                const subtotal = catPrice * weight;
                
                const itemRowHtml = `
                    <div class="flex justify-between items-center p-2 bg-white rounded-xl border border-slate-200/80">
                        <div>
                            <span class="font-bold text-slate-800 block">${catName}</span>
                            <span class="text-[10px] text-slate-400 font-semibold">${weight} ${unit} × Rp ${catPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <span class="font-extrabold text-emerald-600">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
                modalItemsDetail.insertAdjacentHTML('beforeend', itemRowHtml);
            }
        });

        // Tampilkan Modal
        document.getElementById('modalConfirmTransaction').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('modalConfirmTransaction').classList.add('hidden');
    }

    function submitTransaction() {
        document.getElementById('formTransaction').submit();
    }
</script>
@endsection