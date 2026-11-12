<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA USER (PENGELOLA & NASABAH)
        $pengelola = User::create([
            'name' => 'Admin Pengelola',
            'email' => 'admin@banksampah.test',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
            'address' => 'PCI II A6/26 RT 02 RW 18 Desa Tanimulya',
            'phone' => '0895625090769',
            'balance' => 0,
            'status' => 'active',
        ]);

        $nasabah1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@banksampah.test',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'address' => 'RT 02 RW 18 Desa Tanimulya',
            'phone' => '081298765432',
            'balance' => 0,
            'status' => 'active',
        ]);

        $nasabah2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@banksampah.test',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'address' => 'RT 05 RW 18 Desa Tanimulya',
            'phone' => '081211223344',
            'balance' => 0,
            'status' => 'active',
        ]);

        // 2. DATA KATEGORI SAMPAH
        $categories = [
            // PLASTIK
            ['name' => 'Plastik - Botol Air Minum & Galon (PET/Clear)', 'price_per_kg' => 3000],
            ['name' => 'Plastik - Ember, Perabotan & Wadah Keras', 'price_per_kg' => 2000],
            ['name' => 'Plastik - Gelas Plastik & Botol Tumblr', 'price_per_kg' => 2500],
            ['name' => 'Plastik - Tutup Botol, Sedotan & Sendok Plastik', 'price_per_kg' => 1000],
            ['name' => 'Plastik - Paralon & Helm Bekas', 'price_per_kg' => 1500],
            ['name' => 'Plastik - Plastik Bening & Kantong Kresek', 'price_per_kg' => 500],

            // KERTAS
            ['name' => 'Kertas - Kardus Coklat & Trey Telur', 'price_per_kg' => 1800],
            ['name' => 'Kertas - Kertas HVS, Arsip & Buku Pelajaran', 'price_per_kg' => 2000],
            ['name' => 'Kertas - Koran, Majalah & Tabloid Bekas', 'price_per_kg' => 1200],
            ['name' => 'Kertas - Duplex, Kones & Kertas Semen', 'price_per_kg' => 1000],
            ['name' => 'Kertas - Kotak Susu / Sereal (Tetra Pak)', 'price_per_kg' => 800],

            // LAINNYA & LOGAM
            ['name' => 'Minyak - Minyak Goreng Jelantah (Liter/Kg)', 'price_per_kg' => 6000],
            ['name' => 'Logam - Besi Tua & Seng Kanopi', 'price_per_kg' => 4000],
            ['name' => 'Logam - Kaleng Alumunium, Panci & Wajan Bekas', 'price_per_kg' => 9500],
            ['name' => 'Logam - Kabel Tembaga & Struktur Logam', 'price_per_kg' => 12000],
            ['name' => 'Logam - Accu / Aki Bekas Motor & Mobil', 'price_per_kg' => 10000],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // 3. SAMPLE TRANSAKSI AWAL (Menambahkan 'price_per_kg')
        $botolPet = Category::where('name', 'like', '%Botol Air Minum%')->first();
        $kardus = Category::where('name', 'like', '%Kardus Coklat%')->first();

        if ($botolPet) {
            $totalAmount1 = 5.50 * $botolPet->price_per_kg;
            Transaction::create([
                'user_id' => $nasabah1->id,
                'category_id' => $botolPet->id,
                'weight' => 5.50,
                'price_per_kg' => $botolPet->price_per_kg, // Tambahkan baris ini
                'total_amount' => $totalAmount1,
            ]);
            $nasabah1->increment('balance', $totalAmount1);
        }

        if ($kardus) {
            $totalAmount2 = 10.00 * $kardus->price_per_kg;
            Transaction::create([
                'user_id' => $nasabah2->id,
                'category_id' => $kardus->id,
                'weight' => 10.00,
                'price_per_kg' => $kardus->price_per_kg, // Tambahkan baris ini
                'total_amount' => $totalAmount2,
            ]);
            $nasabah2->increment('balance', $totalAmount2);
        }
    }
}