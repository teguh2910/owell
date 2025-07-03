<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\RawMaterial;
use App\Models\MonthlyRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;

        $totalRawMaterials = RawMaterial::count();
        $totalStockReady = Stock::sum('ready_stock');
        $totalStockInProcess = Stock::sum('in_process_stock');
        $totalStockAiiA = Stock::sum('aiia_stock');
        $criticalStocksCount = Stock::where('is_critical', true)->count();

        $expiringSoonCount = Stock::whereNotNull('expired_date')
                                ->where('expired_date', '<=', Carbon::now()->addDays(30))
                                ->count();
        $expiringSoonList = Stock::whereNotNull('expired_date')
                                ->where('expired_date', '<=', Carbon::now()->addDays(30))
                                ->with('rawMaterial')
                                ->get();

        $criticalStocksList = Stock::where('is_critical', true)
                                    ->with('rawMaterial')
                                    ->orderByRaw('CASE WHEN estimated_depletion_date IS NULL THEN 1 ELSE 0 END, estimated_depletion_date ASC')
                                    ->get();

        $topMonthlyUsages = MonthlyRequirement::selectRaw('raw_material_id, AVG(total_monthly_usage) as avg_usage')
                                            ->groupBy('raw_material_id')
                                            ->orderByDesc('avg_usage')
                                            ->limit(5)
                                            ->with('rawMaterial')
                                            ->get();

        $kansaiStatusCount = Stock::whereNotNull('kansai_process_status')->where('kansai_process_status', '!=', '')->count();
        $owellStatusCount = Stock::whereNotNull('owell_process_status')->where('owell_process_status', '!=', '')->count();
        $qaAiiaStatusCount = Stock::whereNotNull('qa_aiia_process_status')->where('qa_aiia_process_status', '!=', '')->count();
        
        $data = [
            'userRole' => $userRole,
            'totalRawMaterials' => $totalRawMaterials,
            'totalStockReady' => $totalStockReady,
            'totalStockInProcess' => $totalStockInProcess,
            'totalStockAiiA' => $totalStockAiiA,
            'criticalStocksCount' => $criticalStocksCount,
            'expiringSoonCount' => $expiringSoonCount,
            'expiringSoonList' => $expiringSoonList,
            'criticalStocksList' => $criticalStocksList,
            'topMonthlyUsages' => $topMonthlyUsages,
            'processStatusBreakdown' => [
                'Kansai' => $kansaiStatusCount,
                'Owell' => $owellStatusCount,
                'QA AiiA' => $qaAiiaStatusCount,
            ],
        ];

        // --- DATA UNTUK CHART ---

        // Chart 1: Distribusi Total Stok
        $data['chartStockDistribution'] = [
            'labels' => ['Stok Ready', 'Stok Dalam Proses'],
            'data' => [$totalStockReady, $totalStockInProcess],
            'backgroundColor' => ['#4CAF50', '#FFC107'], // Hijau, Kuning, Biru
            'hoverOffset' => 4,
        ];

        // Chart 2: Top 5 Material Kritis
        $chartCriticalStocksLabels = [];
        $chartCriticalStocksData = [];
        foreach ($criticalStocksList->take(5) as $stock) { // Ambil hanya 5 teratas jika ada banyak
            $chartCriticalStocksLabels[] = $stock->rawMaterial->name;
            $chartCriticalStocksData[] = $stock->ready_stock; // Bisa juga pakai total (ready + in_process) jika ingin
        }
        $data['chartCriticalStocks'] = [
            'labels' => $chartCriticalStocksLabels,
            'data' => $chartCriticalStocksData,
            'backgroundColor' => ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5'], // Warna merah/ungu
        ];


        return view('dashboard', $data);
    }
}