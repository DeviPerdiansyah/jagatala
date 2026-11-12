<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Jalankan penjadwalan command otomatis.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan perintah penonaktifan nasabah inaktif setiap hari jam 00:00
        $schedule->command('members:deactivate-inactive')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}