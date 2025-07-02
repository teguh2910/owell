<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\RawMaterial; // Penting: Import model RawMaterial
use App\Models\MonthlyRequirement; // Import model MonthlyRequirement
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk bekerja dengan tanggal dan minggu
use Maatwebsite\Excel\Facades\Excel; // Import Facade Excel
use App\Imports\StockImport; // Import import class Anda

class StockController extends Controller
{
    
    /**
     * Display a listing of the resource (Menampilkan daftar stok raw material).
     */
    public function index()
    {
        // Mengambil semua data stok beserta nama raw materialnya
        // Urutkan berdasarkan estimated_depletion_date, nulls last (yang tidak ada estimasi akan ditaruh di akhir)
        $stocks = Stock::with('rawMaterial')
                        ->orderByRaw('CASE WHEN estimated_depletion_date IS NULL THEN 1 ELSE 0 END, estimated_depletion_date ASC')
                        ->get();
        return view('stocks.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new resource (Menampilkan form untuk menambah stok baru).
     * Biasanya, stok akan di-update, bukan dibuat baru terus menerus.
     * Fitur ini bisa digunakan jika ada raw material baru yang belum ada entri stoknya sama sekali.
     */
    public function create()
    {
        // Mengambil raw material yang belum memiliki entri stok
        $rawMaterials = RawMaterial::whereDoesntHave('stock')->get();
        return view('stocks.create', compact('rawMaterials'));
    }

    /**
     * Store a newly created resource in storage (Menyimpan data stok baru).
     */
    public function store(Request $request)
    {
        $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id|unique:stocks,raw_material_id', // Pastikan hanya satu entri stok per raw material
            'ready_stock' => 'required|integer|min:0',
            'in_process_stock' => 'required|integer|min:0',
            'process_status' => 'nullable|string|max:255',
            'expired_date' => 'nullable|date|after_or_equal:today', // Validasi tanggal kedaluwarsa
        ]);

        $stock = Stock::create($request->all());
        // Setelah stok disimpan, panggil logika pengecekan kritis
        $this->checkCriticalStock($stock);

        return redirect()->route('stocks.index')->with('success', 'Data stok berhasil ditambahkan!');
    }

    /**
     * Display the specified resource (Tidak terlalu dipakai untuk fitur ini).
     */
    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource (Menampilkan form edit stok).
     */
    public function edit(Stock $stock)
    {
        // Tidak perlu $rawMaterials karena kita mengedit stok untuk rawMaterial yang sudah ada
        return view('stocks.edit', compact('stock'));
    }

    /**
     * Update the specified resource in storage (Memperbarui data stok).
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'ready_stock' => 'required|integer|min:0',
            'in_process_stock' => 'required|integer|min:0',
            'process_status' => 'nullable|string|max:255',
            'expired_date' => 'nullable|date|after_or_equal:today', // Validasi tanggal kedaluwarsa
        ]);

        $stock->update($request->all());
        // Setelah stok diperbarui, panggil logika pengecekan kritis
        $this->checkCriticalStock($stock);
        return redirect()->route('stocks.index')->with('success', 'Data stok berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage (Menghapus data stok).
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Data stok berhasil dihapus!');
    }
    /**
     * Refresh the critical status and estimated depletion date for a specific stock.
     */
    public function refreshStockStatus(Stock $stock)
    {
        // Panggil kembali logika pengecekan kritis dan estimasi habis
        $this->checkCriticalStock($stock);

        return redirect()->route('stocks.index')->with('success', 'Status stok ' . $stock->rawMaterial->name . ' berhasil diperbarui!');
    }
    /**
     * Refresh the critical status and estimated depletion date for ALL stocks.
     */
    public function refreshAllStockStatus()
    {
        // Ambil semua data stok
        $allStocks = Stock::all();

        // Iterasi setiap stok dan panggil logika pengecekan kritis dan estimasi habis
        foreach ($allStocks as $stock) {
            $this->checkCriticalStock($stock);
        }

        return redirect()->route('stocks.index')->with('success', 'Status semua stok berhasil diperbarui!');
    }
    /**
     * Logika untuk memeriksa apakah stok suatu raw material kritis dan menghitung estimasi habis.
     */
    protected function checkCriticalStock(Stock $stock)
    {
        $currentReadyStock = $stock->ready_stock;
        $isCritical = false; // Default
        $estimatedDepletionDate = null; // Default

        $today = Carbon::now();
        $currentYear = $today->year;
        $currentMonth = $today->month;

        // Ambil semua kebutuhan bulanan untuk raw material ini, diurutkan berdasarkan tahun dan bulan,
        // dimulai dari bulan saat ini atau bulan-bulan yang akan datang.
        $monthlyRequirements = MonthlyRequirement::where('raw_material_id', $stock->raw_material_id)
                                                ->where(function($query) use ($currentYear, $currentMonth) {
                                                    $query->where('year', '>', $currentYear)
                                                          ->orWhere(function($query) use ($currentYear, $currentMonth) {
                                                              $query->where('year', $currentYear)
                                                                    ->where('month', '>=', $currentMonth);
                                                          });
                                                })
                                                ->orderBy('year')
                                                ->orderBy('month')
                                                ->get();

        $remainingStockForProjection = $currentReadyStock;
        $projectedDepletionDate = null;

        // Iterasi melalui kebutuhan bulanan yang *akan datang* (mulai dari bulan ini)
        foreach ($monthlyRequirements as $req) {
            $usageForThisMonth = $req->total_monthly_usage;

            // Jika kebutuhan bulanan adalah 0, lewati bulan ini atau anggap stok tidak terkonsumsi
            if ($usageForThisMonth <= 0) {
                continue;
            }

            // Jika stok cukup untuk kebutuhan bulan ini
            if ($remainingStockForProjection >= $usageForThisMonth) {
                $remainingStockForProjection -= $usageForThisMonth;
                // Stok masih ada, lanjutkan ke bulan berikutnya
            } else {
                // Stok tidak cukup untuk kebutuhan bulan ini, hitung tanggal habis di bulan ini
                // Asumsi: Penggunaan merata selama bulan
                $firstDayOfMonth = Carbon::create($req->year, $req->month, 1)->startOfDay();
                $daysInMonth = $firstDayOfMonth->daysInMonth; // Jumlah hari di bulan tersebut

                $dailyUsage = $usageForThisMonth / $daysInMonth;

                // Pastikan dailyUsage tidak nol untuk menghindari division by zero
                if ($dailyUsage > 0) {
                    $daysToDepleteInMonth = $remainingStockForProjection / $dailyUsage;
                    // Tanggal habis adalah awal bulan + hari yang dibutuhkan
                    $projectedDepletionDate = $firstDayOfMonth->copy()->addDays(floor($daysToDepleteInMonth));
                    $estimatedDepletionDate = $projectedDepletionDate;
                }
                break; // Stok habis di bulan ini, hentikan loop
            }
        }

        // Jika setelah mengiterasi semua kebutuhan yang ada, stok masih tersisa
        // DAN projectedDepletionDate masih null (artinya stok tidak habis dalam periode yang terdaftar),
        // maka atur estimatedDepletionDate ke null atau "Aman jauh di depan".
        // Dalam kasus ini, kita akan biarkan null dan tampilan yang menginterpretasikannya.
        if (is_null($projectedDepletionDate) && $remainingStockForProjection > 0) {
            $estimatedDepletionDate = null; // Stok tidak akan habis dalam periode yang terdaftar dengan kebutuhan
        }


        // Logika Peringatan Kritis:
        // Stok kritis jika ada estimatedDepletionDate DAN tanggalnya kurang dari atau sama dengan 1 bulan dari sekarang
        $oneMonthFromNow = $today->copy()->addMonth(); // 1 bulan dari sekarang

        if ($estimatedDepletionDate) {
            // Jika ada estimasi tanggal habis, cek apakah tanggal tersebut <= 1 bulan dari sekarang
            if ($estimatedDepletionDate->lessThanOrEqualTo($oneMonthFromNow)) {
                $isCritical = true;
            }
        } else {
            // Kasus edge: Jika tidak ada kebutuhan yang diinput SAMA SEKALI,
            // atau kebutuhan sangat sedikit sehingga estimatedDepletionDate tidak terhitung,
            // tapi stoknya juga nol atau sangat sedikit.
            // Kita bisa tambahkan logika peringatan jika stok ready adalah 0 dan tidak ada estimasi habis
            if ($currentReadyStock == 0 && $monthlyRequirements->isEmpty()) {
                 // Tidak ada kebutuhan terdaftar, tapi stok juga 0. Ini bisa jadi kritis.
                 // Anda bisa memutuskan apakah ini juga dianggap kritis atau tidak.
                 // Untuk saat ini, kita hanya fokus pada perbandingan dengan kebutuhan.
            }
        }

        // Perbarui status 'is_critical' dan 'estimated_depletion_date' di tabel stocks
        $stock->update([
            'is_critical' => $isCritical,
            'estimated_depletion_date' => $estimatedDepletionDate,
        ]);
    } 
    /**
     * Show the form for uploading Excel file for Stocks.
     */
    public function importForm()
    {
        return view('stocks.import');
    }

    /**
     * Handle the Excel file upload and import for Stocks.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048', // Validasi file harus excel dan ukuran max 2MB
        ]);

        try {
            // Lakukan impor
            Excel::import(new StockImport, $request->file('file'));

            // Setelah semua stok diimpor/diperbarui, refresh semua status kritis dan estimasi habis
            $this->refreshAllStockStatus(); // Memanggil method yang sudah ada

            return redirect()->route('stocks.index')->with('success', 'Data stok berhasil diimpor dari Excel!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors(['excel_errors' => $errors]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }   
}