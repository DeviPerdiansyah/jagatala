<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait WhatsAppNotifier
{
    public function sendWA($targetPhone, $message)
    {
        if (empty($targetPhone)) return false;

        // Clean Nomor Telepon ke Format Internasional (628xxx)
        $targetPhone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (str_starts_with($targetPhone, '0')) {
            $targetPhone = '62' . substr($targetPhone, 1);
        }

        // Contoh Integrasi dengan Fonnte / Provider API WA
        $token = env('FONNTE_TOKEN', ''); 

        if (empty($token)) {
            Log::info("WA Notifier (Simulation Only): Sending to {$targetPhone} - Msg: {$message}");
            return true;
        }

        try {
            Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $targetPhone,
                'message' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("WA Send Error: " . $e->getMessage());
            return false;
        }
    }
}