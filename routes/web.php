<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AppSettingController;

// 1. HALAMAN UTAMA PUBLIC LANDING PAGE
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// Authenticated Routes (Semua User Terautentikasi: Pengelola & Nasabah)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Tambahan Modul Pencairan Saldo
    Route::post('/withdrawals/{withdrawal}/fulfillment', [WithdrawalController::class, 'completeFulfillment'])->name('withdrawals.fulfillment');
    Route::get('/withdrawals/print-projection', [WithdrawalController::class, 'printDailyCashProjection'])->name('withdrawals.print-projection');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Transaksi & Laporan
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/report-pdf', [TransactionController::class, 'printReport'])->name('transactions.print-report');
    Route::get('/transactions/export-excel', [TransactionController::class, 'exportExcel'])->name('transactions.export-excel');

    // Penarikan Saldo & Cetak Bukti (Bisa diakses Pengelola & Nasabah)
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::patch('/withdrawals/{withdrawal}', [WithdrawalController::class, 'update'])->name('withdrawals.update');
    
    // BUKTI PENARIKAN
    Route::get('/withdrawals/{withdrawal}/receipt', [WithdrawalController::class, 'printReceipt'])->name('withdrawals.receipt');

    // Cek Status Maintenance
    Route::get('/settings/price-maintenance', [AppSettingController::class, 'getPriceMaintenanceStatus'])->name('settings.price_maintenance_status');

    // Khusus Pengelola
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':pengelola')->group(function () {
        Route::post('/settings/toggle-price-maintenance', [AppSettingController::class, 'togglePriceMaintenance'])->name('settings.toggle_price_maintenance');
        Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

        // Kelola & Verifikasi Nasabah
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::patch('/members/{user}/status', [MemberController::class, 'updateStatus'])->name('members.update_status');
        
        // ROUTE BARU: Aktivasi Ulang & Hapus Akun Ditolak
        Route::patch('/members/{user}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');
        Route::delete('/members/{user}', [MemberController::class, 'destroy'])->name('members.destroy');
    });
});