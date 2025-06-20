<?php

namespace App\Imports;

use App\Models\MonthlyRequirement;
use App\Models\RawMaterial; // Import model RawMaterial
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Untuk membaca header/judul kolom
use Maatwebsite\Excel\Concerns\WithValidation; // Untuk validasi data
use Maatwebsite\Excel\Concerns\SkipsOnError; // Untuk melompati baris yang error
use Maatwebsite\Excel\Concerns\SkipsErrors; // Trait untuk SkipsOnError

class MonthlyRequirementImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors; // Implementasi trait untuk melompati error

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Mendapatkan ID raw material berdasarkan nama
        // Pastikan nama kolom di Excel adalah 'nama_raw_material'
        $rawMaterial = RawMaterial::where('name', $row['nama_raw_material'])->first();

        // Jika raw material tidak ditemukan, kita bisa melewatkan baris ini atau memberikan error
        if (!$rawMaterial) {
            // Melewatkan baris ini, atau log error
            // throw new \Exception('Raw material "' . $row['nama_raw_material'] . '" not found.');
            return null; // Melewatkan baris jika raw material tidak ditemukan
        }

        // Cek apakah sudah ada kebutuhan untuk raw material dan bulan/tahun yang sama
        $existingRequirement = MonthlyRequirement::where('raw_material_id', $rawMaterial->id)
                                                 ->where('year', $row['tahun'])
                                                 ->where('month', $row['bulan'])
                                                 ->first();

        // Jika sudah ada, kita bisa update atau skip.
        // Untuk fitur ini, kita akan *update* data yang sudah ada.
        if ($existingRequirement) {
            $existingRequirement->fill([
                'total_monthly_usage' => $row['total_kebutuhan_bulanan'],
                'weekly_usage_1' => floor($row['total_kebutuhan_bulanan'] / 4) + ($row['total_kebutuhan_bulanan'] % 4 > 0 ? 1 : 0),
                'weekly_usage_2' => floor($row['total_kebutuhan_bulanan'] / 4) + ($row['total_kebutuhan_bulanan'] % 4 > 1 ? 1 : 0),
                'weekly_usage_3' => floor($row['total_kebutuhan_bulanan'] / 4) + ($row['total_kebutuhan_bulanan'] % 4 > 2 ? 1 : 0),
                'weekly_usage_4' => floor($row['total_kebutuhan_bulanan'] / 4),
            ]);
            $existingRequirement->save();
            return null; // Return null karena sudah update yang existing, tidak membuat model baru
        }

        // Logika perhitungan kebutuhan mingguan (total dibagi 4, dengan distribusi sisa)
        $totalUsage = $row['total_kebutuhan_bulanan'];
        $weeklyUsage = floor($totalUsage / 4);
        $remainder = $totalUsage % 4;

        $weekly_usage_1 = $weeklyUsage + ($remainder > 0 ? 1 : 0);
        $weekly_usage_2 = $weeklyUsage + ($remainder > 1 ? 1 : 0);
        $weekly_usage_3 = $weeklyUsage + ($remainder > 2 ? 1 : 0);
        $weekly_usage_4 = $weeklyUsage;

        // Membuat MonthlyRequirement baru jika belum ada
        return new MonthlyRequirement([
            'raw_material_id' => $rawMaterial->id,
            'year' => $row['tahun'],
            'month' => $row['bulan'],
            'total_monthly_usage' => $totalUsage,
            'weekly_usage_1' => $weekly_usage_1,
            'weekly_usage_2' => $weekly_usage_2,
            'weekly_usage_3' => $weekly_usage_3,
            'weekly_usage_4' => $weekly_usage_4,
        ]);
    }

    // Aturan validasi untuk setiap baris dari file Excel
    public function rules(): array
    {
        return [
            'nama_raw_material' => 'required|max:255|exists:raw_materials,name', // Pastikan raw material ada
            'tahun' => 'required|integer|min:' . date('Y'),
            'bulan' => 'required|integer|min:1|max:12',
            'total_kebutuhan_bulanan' => 'required|integer|min:0', // Bisa 0 jika tidak ada kebutuhan
        ];
    }

    // Custom messages untuk validasi (opsional)
    public function customValidationMessages()
    {
        return [
            'nama_raw_material.exists' => 'Nama Raw Material tidak ditemukan di master data.',
            'tahun.min' => 'Tahun harus sama atau lebih besar dari tahun sekarang.',
            // ... pesan lainnya
        ];
    }

    // Method untuk menangani error (melompati baris yang error)
    public function onError(\Throwable $e)
    {
        // Opsional: Anda bisa log error di sini
        // Log::error('Error importing monthly requirement: ' . $e->getMessage());
    }
}