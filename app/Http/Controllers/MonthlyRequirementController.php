<?php

namespace App\Http\Controllers;

use App\Models\MonthlyRequirement;
use App\Models\RawMaterial; // Penting: Import model RawMaterial untuk dropdown
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // Import Facade Excel
use App\Imports\MonthlyRequirementImport; // Import import class Anda

class MonthlyRequirementController extends Controller
{
    /**
     * Display a listing of the resource (Menampilkan daftar kebutuhan bulanan).
     */
    public function index()
    {
        // Mengambil semua data kebutuhan bulanan beserta nama raw materialnya
        $monthlyRequirements = MonthlyRequirement::with('rawMaterial')->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        return view('monthly_requirements.index', compact('monthlyRequirements'));
    }

    /**
     * Show the form for creating a new resource (Menampilkan form input kebutuhan).
     */
    public function create()
    {
        // Mengambil semua raw material untuk dropdown pilihan
        $rawMaterials = RawMaterial::all();
        // Mengambil tahun dan bulan saat ini sebagai default
        $currentYear = date('Y');
        $currentMonth = date('n'); // n = month without leading zeros (1 to 12)

        return view('monthly_requirements.create', compact('rawMaterials', 'currentYear', 'currentMonth'));
    }

    /**
     * Store a newly created resource in storage (Menyimpan data kebutuhan baru).
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'year' => 'required|integer|min:' . date('Y'), // Tahun minimal tahun sekarang
            'month' => 'required|integer|min:1|max:12',
            'total_monthly_usage' => 'required|integer|min:1', // Kebutuhan harus positif
        ]);

        // Cek apakah sudah ada kebutuhan untuk raw material dan bulan/tahun yang sama
        $existingRequirement = MonthlyRequirement::where('raw_material_id', $request->raw_material_id)
                                                 ->where('year', $request->year)
                                                 ->where('month', $request->month)
                                                 ->first();

        if ($existingRequirement) {
            return redirect()->back()->withInput()->withErrors(['message' => 'Kebutuhan untuk Raw Material ini di bulan dan tahun yang sama sudah ada. Harap edit data yang sudah ada.']);
        }

        // Logika perhitungan kebutuhan mingguan (total dibagi 4)
        $totalUsage = $request->total_monthly_usage;
        $weeklyUsage = floor($totalUsage / 4); // Pembulatan ke bawah
        $remainder = $totalUsage % 4; // Sisa pembagian

        // Mendistribusikan sisa ke minggu-minggu awal
        $weekly_usage_1 = $weeklyUsage + ($remainder > 0 ? 1 : 0);
        $weekly_usage_2 = $weeklyUsage + ($remainder > 1 ? 1 : 0);
        $weekly_usage_3 = $weeklyUsage + ($remainder > 2 ? 1 : 0);
        $weekly_usage_4 = $weeklyUsage;


        MonthlyRequirement::create([
            'raw_material_id' => $request->raw_material_id,
            'year' => $request->year,
            'month' => $request->month,
            'total_monthly_usage' => $totalUsage,
            'weekly_usage_1' => $weekly_usage_1,
            'weekly_usage_2' => $weekly_usage_2,
            'weekly_usage_3' => $weekly_usage_3,
            'weekly_usage_4' => $weekly_usage_4,
        ]);

        return redirect()->route('monthly_requirements.index')->with('success', 'Kebutuhan bulanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource. (Tidak terlalu dipakai untuk fitur ini)
     */
    public function show(MonthlyRequirement $monthlyRequirement)
    {
        return view('monthly_requirements.show', compact('monthlyRequirement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyRequirement $monthlyRequirement)
    {
        $rawMaterials = RawMaterial::all(); // Untuk dropdown jika ingin ubah bahan baku
        return view('monthly_requirements.edit', compact('monthlyRequirement', 'rawMaterials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyRequirement $monthlyRequirement)
    {
        $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'year' => 'required|integer|min:' . date('Y'),
            'month' => 'required|integer|min:1|max:12',
            'total_monthly_usage' => 'required|integer|min:1',
        ]);

        // Cek apakah ada konflik dengan entri lain setelah update
        $existingRequirement = MonthlyRequirement::where('raw_material_id', $request->raw_material_id)
                                                 ->where('year', $request->year)
                                                 ->where('month', $request->month)
                                                 ->where('id', '!=', $monthlyRequirement->id) // Exclude current record
                                                 ->first();

        if ($existingRequirement) {
            return redirect()->back()->withInput()->withErrors(['message' => 'Kebutuhan untuk Raw Material ini di bulan dan tahun yang sama sudah ada. Harap edit data yang sudah ada.']);
        }

        // Logika perhitungan kebutuhan mingguan (sama seperti store)
        $totalUsage = $request->total_monthly_usage;
        $weeklyUsage = floor($totalUsage / 4);
        $remainder = $totalUsage % 4;

        $weekly_usage_1 = $weeklyUsage + ($remainder > 0 ? 1 : 0);
        $weekly_usage_2 = $weeklyUsage + ($remainder > 1 ? 1 : 0);
        $weekly_usage_3 = $weeklyUsage + ($remainder > 2 ? 1 : 0);
        $weekly_usage_4 = $weeklyUsage;

        $monthlyRequirement->update([
            'raw_material_id' => $request->raw_material_id,
            'year' => $request->year,
            'month' => $request->month,
            'total_monthly_usage' => $totalUsage,
            'weekly_usage_1' => $weekly_usage_1,
            'weekly_usage_2' => $weekly_usage_2,
            'weekly_usage_3' => $weekly_usage_3,
            'weekly_usage_4' => $weekly_usage_4,
        ]);

        return redirect()->route('monthly_requirements.index')->with('success', 'Kebutuhan bulanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyRequirement $monthlyRequirement)
    {
        $monthlyRequirement->delete();
        return redirect()->route('monthly_requirements.index')->with('success', 'Kebutuhan bulanan berhasil dihapus!');
    }
     /**
     * Show the form for uploading Excel file.
     */
    public function importForm()
    {
        return view('monthly_requirements.import');
    }

    /**
     * Handle the Excel file upload and import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048', // Validasi file harus excel dan ukuran max 2MB
        ]);

        try {
            // Lakukan impor
            Excel::import(new MonthlyRequirementImport, $request->file('file'));

            return redirect()->route('monthly_requirements.index')->with('success', 'Data kebutuhan bulanan berhasil diimpor dari Excel!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                // Baris mana yang error, atribut apa, dan pesan errornya
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            // Kirimkan error kembali ke view
            return redirect()->back()->withErrors(['excel_errors' => $errors]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}