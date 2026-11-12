<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    // Menampilkan Halaman Dashboard Utama
    public function index()
    {
        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
        $pendingWithdrawalsAmount = Withdrawal::where('status', 'pending')->sum('amount');

        $data = [
            'role' => auth()->user()->role,
            'stats' => [
                'total_nasabah' => User::where('role', 'nasabah')->where('status', 'active')->count(),
                'pending_nasabah' => User::where('role', 'nasabah')->where('status', 'pending')->count(),
                'total_sampah' => number_format(Transaction::sum('weight'), 1, ',', '.'),
                'total_kas_nasabah' => 'Rp ' . number_format(User::where('role', 'nasabah')->sum('balance'), 0, ',', '.'),
                'pending_withdrawals' => $pendingWithdrawalsCount,
                'pending_withdrawals_amount' => 'Rp ' . number_format($pendingWithdrawalsAmount, 0, ',', '.'),
            ],
            'latest_transactions' => Transaction::with(['user', 'category', 'details.category'])->latest()->take(5)->get()
        ];

        return view('dashboard.index', $data);
    }

    // Endpoint API khusus untuk memberikan data Real-time (JSON)
    public function getStats(Request $request)
    {
        $user = auth()->user();

        // Validasi dan set rentang hari dinamis (7 atau 30 hari)
        $days = in_array($request->query('days'), ['7', '30']) ? (int) $request->query('days') : 7;

        // Data Patokan Harga Sampah Terkini
        $priceList = Category::orderBy('name', 'asc')->get()->map(function ($cat) {
            $unit = strtolower($cat->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
            return [
                'name' => $cat->name,
                'type' => $cat->type ?? 'Anorganik',
                'price' => 'Rp ' . number_format($cat->price_per_kg, 0, ',', '.') . ' / ' . $unit,
            ];
        });

        // Cek Status Maintenance
        $isMaintenance = Cache::get('price_maintenance_mode', false);

        if ($user->role === 'pengelola') {
            // 1. STATISTIK UTAMA PENGELOLA
            $totalNasabah = User::where('role', 'nasabah')->count();
            $totalSampah = Transaction::sum('weight');
            $totalSaldoSeluruhNasabah = User::where('role', 'nasabah')->sum('balance');
            
            // Query Penarikan Pending
            $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
            $pendingWithdrawalsSum = Withdrawal::where('status', 'pending')->sum('amount');

            // Rentang Waktu Dinamis (7 atau 30 Hari Terakhir)
            $startDate = now()->subDays($days - 1)->startOfDay();
            $endDate = now()->endOfDay();

            // 2. DATA TREN SETORAN (DINAMIS Sesuai Parameter)
            $chartDates = [];
            $chartWeights = [];
            $chartAmounts = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                // Format label tanggal: misal 'D' (Sen/Sel) jika 7 hari, atau 'd M' (12 Aug) jika 30 hari
                $chartDates[] = $days === 30 ? $date->translatedFormat('d M') : $date->translatedFormat('D');

                $dayWeight = Transaction::whereDate('created_at', $date->toDateString())->sum('weight');
                $dayAmount = Transaction::whereDate('created_at', $date->toDateString())->sum('total_amount');

                $chartWeights[] = round($dayWeight, 1);
                $chartAmounts[] = round($dayAmount, 0);
            }

            // 3. DATA KOMPOSISI SAMPAH (DONUT CHART)
            $categoriesData = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->whereBetween('transactions.created_at', [$startDate, $endDate])
                ->selectRaw('categories.name as cat_name, SUM(transactions.weight) as total_weight, SUM(transactions.total_amount) as total_rupiah, COUNT(transactions.id) as total_trx')
                ->groupBy('categories.name')
                ->orderByDesc('total_weight')
                ->get();

            $donutLabels = $categoriesData->pluck('cat_name')->toArray();
            $donutSeries = $categoriesData->pluck('total_weight')->map(fn($val) => (float)$val)->toArray();

            if (empty($donutSeries)) {
                $donutLabels = ['Belum Ada Transaksi'];
                $donutSeries = [0];
            }

            // 4. ANALISA KOMPLEKS & EVALUASI DINAMIS
            $totalWeightDays = array_sum($chartWeights);
            $totalAmountDays = array_sum($chartAmounts);
            $topCategory = $categoriesData->first();

            // Perbandingan dengan Periode Sebelumnya (misal 7 hari lalu vs 7 hari sebelumnya, atau 30 hari lalu vs 30 hari sebelumnya)
            $prevStartDate = now()->subDays(($days * 2) - 1)->startOfDay();
            $prevEndDate = now()->subDays($days)->endOfDay();
            $prevTotalWeight = Transaction::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('weight');

            $growthRate = 0;
            $growthStatus = 'stabil';
            if ($prevTotalWeight > 0) {
                $growthRate = (($totalWeightDays - $prevTotalWeight) / $prevTotalWeight) * 100;
                if ($growthRate > 5) $growthStatus = 'naik';
                elseif ($growthRate < -5) $growthStatus = 'turun';
            } elseif ($totalWeightDays > 0) {
                $growthStatus = 'naik';
                $growthRate = 100;
            }

            // Menyusun Narasi Evaluasi Dinamis
            $evaluationSummary = "";
            $actionRecommendation = "";

            if ($totalWeightDays == 0) {
                $evaluationSummary = "<strong>Status Operasional: Pasif.</strong> Tidak ada transaksi penyetoran sampah oleh warga dalam <strong>{$days} hari terakhir</strong>. Terdapat penurunan partisipasi signifikan dari periode sebelumnya.";
                $actionRecommendation = "<strong>Strategi Tindakan Pengelola:</strong><br>"
                    . "• <strong>Jemput Bola:</strong> Adakan armada keliling ke wilayah RT/RW yang partisipasinya paling rendah.<br>"
                    . "• <strong>Evaluasi Operasional:</strong> Cek hambatan teknis jam buka bank sampah atau kendala saluran informasi.<br>"
                    . "• <strong>Program Insentif:</strong> Terapkan program poin bonus untuk penyetor aktif di periode berikutnya.";
            } else {
                $topCatName = $topCategory ? $topCategory->cat_name : 'Tidak Teridentifikasi';
                $topCatWeight = $topCategory ? number_format($topCategory->total_weight, 1) : 0;
                $topCatTrx = $topCategory ? $topCategory->total_trx : 0;
                $topCatPercentage = ($topCategory && $totalWeightDays > 0) ? round(($topCategory->total_weight / $totalWeightDays) * 100, 1) : 0;

                $trendText = "";
                if ($growthStatus === 'naik') {
                    $trendText = "mengalami <strong>peningkatan " . abs(round($growthRate, 1)) . "%</strong> dibandingkan periode sebelumnya. Kesadaran menabung warga menunjukkan tren positif.";
                } elseif ($growthStatus === 'turun') {
                    $trendText = "mengalami <strong>penurunan " . abs(round($growthRate, 1)) . "%</strong> dibandingkan periode sebelumnya. Membutuhkan intervensi aktif dari pengelola.";
                } else {
                    $trendText = "berada pada kondisi <strong>stabil (fluktuasi " . round($growthRate, 1) . "%)</strong> jika dibandingkan tren periode sebelumnya.";
                }

                $evaluationSummary = "Dalam <strong>{$days} hari terakhir</strong>, total sampah terkelola sebanyak <strong>{$totalWeightDays}</strong> (Kg/Pcs) dengan nilai kas <strong>Rp " . number_format($totalAmountDays, 0, ',', '.') . "</strong>. Volume setoran {$trendText}<br><br>"
                    . "Transaksi didominasi oleh kategori <strong>{$topCatName}</strong> yaitu <strong>{$topCatWeight} ({$topCatPercentage}% dari total volume)</strong> melalui {$topCatTrx} transaksi.";

                // Solusi Berdasarkan Kategori Sampah
                $catLower = strtolower($topCatName);
                $solutions = [];

                if (str_contains($catLower, 'kertas') || str_contains($catLower, 'kardus')) {
                    $solutions[] = "• <strong>Manajemen Penyimpanan (Kertas/Kardus):</strong> Jaga tempat penyimpanan tetap kering agar kualifikasi harga jual ke pengepul utama tetap tinggi.";
                    $solutions[] = "• <strong>Pemilahan Kualitas:</strong> Edukasi warga untuk memisahkan HVS dan kardus gelombang karena nilai jual HVS lebih tinggi.";
                } elseif (str_contains($catLower, 'plastik') || str_contains($catLower, 'botol') || str_contains($catLower, 'galon')) {
                    $solutions[] = "• <strong>Pengolahan Volumetric:</strong> Rapikan area penampungan galon & botol agar tempat penyimpanan tidak cepat penuh.";
                    $solutions[] = "• <strong>Kemitraan Industri:</strong> Jalin kerja sama dengan pengolah daur ulang wadah plastik lokal.";
                } elseif (str_contains($catLower, 'logam') || str_contains($catLower, 'besi') || str_contains($catLower, 'tembaga')) {
                    $solutions[] = "• <strong>Keamanan & Presisi Timbangan (Logam):</strong> Kalibrasi timbangan secara berkala dan tingkatkan keamanan gudang penyimpanan komoditas bernilai tinggi ini.";
                } else {
                    $solutions[] = "• <strong>Diversifikasi Komoditas:</strong> Dorong warga untuk memilah jenis sampah anorganik bernilai ekonomis tinggi lainnya.";
                }

                if ($growthStatus === 'turun') {
                    $solutions[] = "• <strong>Evaluasi Tren Turun:</strong> Kirim notifikasi pengingat penimbangan via media sosial/WhatsApp grup warga.";
                } else {
                    $solutions[] = "• <strong>Apresiasi Warga:</strong> Umumkan *Top Nasabah Teraktif* periode ini untuk merawat semangat menabung warga.";
                }

                $actionRecommendation = implode("<br>", $solutions);
            }

            // 5. 5 TRANSAKSI TERAKHIR
            $latestTransactions = Transaction::with(['user', 'category', 'details.category'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($trx) {
                    $firstDetail = $trx->details->first();
                    $unit = 'Kg';
                    if ($firstDetail) {
                        $unit = strtolower($firstDetail->unit ?? $firstDetail->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
                    } elseif ($trx->category) {
                        $unit = strtolower($trx->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
                    }

                    return [
                        'date' => $trx->created_at->format('d M, H:i'),
                        'nasabah' => $trx->user->name ?? '-',
                        'category' => $trx->category->name ?? '-',
                        'weight' => number_format($trx->weight, 1, ',', '.'),
                        'unit' => $unit,
                        'total' => 'Rp ' . number_format($trx->total_amount, 0, ',', '.'),
                    ];
                });

            return response()->json([
                'role' => 'pengelola',
                'is_maintenance' => $isMaintenance,
                'active_days' => $days,
                'stats' => [
                    'total_nasabah' => number_format($totalNasabah),
                    'total_sampah' => number_format($totalSampah, 1, ',', '.'),
                    'total_kas_nasabah' => 'Rp ' . number_format($totalSaldoSeluruhNasabah, 0, ',', '.'),
                    'pending_withdrawals' => $pendingWithdrawalsCount,
                    'pending_withdrawals_amount' => 'Rp ' . number_format($pendingWithdrawalsSum, 0, ',', '.'),
                ],
                'chart_trend' => [
                    'categories' => $chartDates,
                    'weights' => $chartWeights,
                    'amounts' => $chartAmounts,
                ],
                'chart_donut' => [
                    'labels' => $donutLabels,
                    'series' => $donutSeries,
                    'top_category' => $donutLabels[0] ?? '-',
                    'total_categories' => count($categoriesData)
                ],
                'evaluation' => [
                    'summary' => $evaluationSummary,
                    'recommendation' => $actionRecommendation
                ],
                'latest_transactions' => $latestTransactions,
                'price_list' => $priceList,
            ]);
        } else {
            // Statistik Khusus Nasabah
            $user->refresh();
            $totalSetoranSaya = Transaction::where('user_id', $user->id)->sum('weight');
            $totalTransaksiSaya = Transaction::where('user_id', $user->id)->count();

            $myTransactions = Transaction::with(['category', 'details.category'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($trx) {
                    $firstDetail = $trx->details->first();
                    $unit = 'Kg';
                    if ($firstDetail) {
                        $unit = strtolower($firstDetail->unit ?? $firstDetail->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
                    } elseif ($trx->category) {
                        $unit = strtolower($trx->category->unit ?? 'kg') === 'pcs' ? 'Pcs' : 'Kg';
                    }

                    return [
                        'date' => $trx->created_at->format('d M, H:i'),
                        'category' => $trx->category->name ?? '-',
                        'weight' => number_format($trx->weight, 1, ',', '.'),
                        'unit' => $unit,
                        'total' => '+ Rp ' . number_format($trx->total_amount, 0, ',', '.'),
                    ];
                });

            return response()->json([
                'role' => 'nasabah',
                'is_maintenance' => $isMaintenance,
                'stats' => [
                    'balance' => 'Rp ' . number_format($user->balance, 0, ',', '.'),
                    'total_sampah' => number_format($totalSetoranSaya, 1, ',', '.'),
                    'total_transaksi' => $totalTransaksiSaya,
                ],
                'latest_transactions' => $myTransactions,
                'price_list' => $priceList,
            ]);
        }
    }
}