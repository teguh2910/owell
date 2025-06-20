<?php

namespace App\Imports;

use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class RawMaterialImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan nama kolom di Excel adalah 'nama_raw_material'
        // Cek apakah raw material sudah ada berdasarkan nama
        $existingRawMaterial = (string) RawMaterial::where('name', $row['nama_raw_material'])->first();

        if ($existingRawMaterial) {
            // Jika sudah ada, kita bisa melewatkan atau memberikan notifikasi.
            // Untuk master data, umumnya kita skip jika sudah ada untuk menghindari duplikasi.
            return null; // Melewatkan baris jika sudah ada
        }

        // Membuat RawMaterial baru jika belum ada
        return new RawMaterial([
            'name' => $row['nama_raw_material'],
        ]);
    }

    // Aturan validasi untuk setiap baris dari file Excel
    public function rules(): array
    {
        return [
            'nama_raw_material' => 'required|max:255|unique:raw_materials,name', // Nama harus unik
        ];
    }

    // Custom messages untuk validasi (opsional)
    public function customValidationMessages()
    {
        return [
            'nama_raw_material.unique' => 'Nama Raw Material ini sudah ada di master data.',
            'nama_raw_material.required' => 'Nama Raw Material harus diisi.',
            // ... pesan lainnya
        ];
    }

    // Method untuk menangani error (melompati baris yang error)
    public function onError(\Throwable $e)
    {
        // Opsional: Anda bisa log error di sini
        // Log::error('Error importing raw material: ' . $e->getMessage());
    }
}