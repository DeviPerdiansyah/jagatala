<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// COMMAND BARU: Otomatis nonaktifkan nasabah inaktif (> 6 Bulan)
Artisan::command('members:deactivate-inactive', function () {
    $sixMonthsAgo = Carbon::now()->subMonths(6);

    // Cari nasabah aktif yang transaksi terakhirnya > 6 bulan / belum pernah transaksi > 6 bulan sejak daftar
    $usersToDeactivate = User::where('role', 'nasabah')
        ->where('status', 'active')
        ->where(function ($q) use ($sixMonthsAgo) {
            $q->whereHas('transactions', function ($subQ) use ($sixMonthsAgo) {
                $subQ->select('user_id')
                     ->latest()
                     ->havingRaw('MAX(created_at) < ?', [$sixMonthsAgo]);
            })
            ->orWhere(function ($subQ) use ($sixMonthsAgo) {
                $subQ->doesntHave('transactions')
                     ->where('created_at', '<', $sixMonthsAgo);
            });
        })
        ->update(['status' => 'inactive']);

    $this->info("Berhasil menonaktifkan {$usersToDeactivate} nasabah yang tidak aktif selama 6 bulan.");
})->purpose('Menonaktifkan otomatis nasabah yang tidak melakukan transaksi selama 6 bulan');