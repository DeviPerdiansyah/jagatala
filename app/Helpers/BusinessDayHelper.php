<?php

namespace App\Helpers;

use Carbon\Carbon;

class BusinessDayHelper
{
    /**
     * Menghitung tanggal pengambilan berdasarkan hari kerja (melompati Sabtu & Minggu)
     */
    public static function getNextBusinessDay(int $addDays = 3): Carbon
    {
        $date = Carbon::now();
        
        while ($addDays > 0) {
            $date->addDay();
            // Jika bukan Sabtu (6) dan bukan Minggu (0)
            if (!$date->isWeekend()) {
                $addDays--;
            }
        }

        return $date;
    }
}