<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppSettingController extends Controller
{
    // Cek Status Maintenance Harga
    public function getPriceMaintenanceStatus(Request $request)
    {
        // Jika diakses langsung via address bar browser (bukan via AJAX/Fetch), lempar ke dashboard
        if (!$request->wantsJson() && !$request->ajax()) {
            return redirect()->route('dashboard');
        }

        $isMaintenance = Cache::get('price_maintenance_mode', false);
        
        return response()->json([
            'is_maintenance' => $isMaintenance,
            'message' => $isMaintenance 
                ? '⚠️ Pengelola sedang melakukan pembaruan patokan harga sampah. Transaksi setoran ditangguhkan sementara.' 
                : 'Sistem Berjalan Normal'
        ]);
    }

    // Toggle Switch Mode oleh Pengelola
    public function togglePriceMaintenance(Request $request)
    {
        $currentStatus = Cache::get('price_maintenance_mode', false);
        $newStatus = !$currentStatus;

        Cache::forever('price_maintenance_mode', $newStatus);

        $statusMessage = $newStatus 
            ? 'Mode Pembaruan Harga DIBUKA. Transaksi nasabah ditangguhkan sementara.' 
            : 'Mode Pembaruan Harga DITUTUP. Sistem transaksi kembali normal.';

        return back()->with('success', $statusMessage);
    }
}