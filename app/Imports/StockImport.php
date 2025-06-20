<?php

namespace App\Imports;

use App\Models\Stock;
use App\Models\RawMaterial; // Penting: Import model RawMaterial
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon; // Untuk parsing tanggal

class StockImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Mendapatkan ID raw material berdasarkan nama
        $rawMaterial = RawMaterial::where('name', $row['nama_raw_material'])->first();

        // Jika raw material tidak ditemukan, lewati baris ini
        if (!$rawMaterial) {
            return null;
        }

        // Cari entri stok yang sudah ada untuk raw material ini
        $stock = Stock::where('raw_material_id', $rawMaterial->id)->first();

        // Parsing expired_date jika ada
        $expiredDate = null;
        if (isset($row['expired_date']) && !empty($row['expired_date'])) {
            // Maatwebsite/Excel biasanya mengembalikan tanggal sebagai nomor seri Excel (integer/float)
            // atau string. Kita coba konversi dari nomor seri dulu.
            try {
                if (is_numeric($row['expired_date'])) {
                    $expiredDate = Carbon::createFromTimestamp(
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row['expired_date'])
                    );
                } else {
                    $expiredDate = Carbon::parse($row['expired_date']);
                }
            } catch (\Exception $e) {
                // Log error atau abaikan jika tanggal tidak valid
                $expiredDate = null;
            }
        }

        $data = [
            'raw_material_id' => $rawMaterial->id,
            'ready_stock' => $row['ready_stock'],
            'in_process_stock' => $row['in_process_stock'],
            'process_status' => $row['process_status'] ?? null, // Opsional
            'expired_date' => $expiredDate, // Gunakan tanggal yang sudah di-parse
        ];

        if ($stock) {
            // Update stok yang sudah ada
            $stock->update($data);
        } else {
            // Buat stok baru jika belum ada
            $stock = Stock::create($data);
        }

        // Panggil logika pengecekan kritis setelah update/create
        // Anda perlu membuat instance dari StockController dan memanggil method protected
        // Atau lebih baik, refactor checkCriticalStock ke service class
        // Untuk saat ini, kita bisa memanggilnya langsung setelah membuat/mengupdate stok
        // Namun, memanggilnya di sini akan sedikit kompleks karena butuh konteks controller.
        // Akan lebih mudah jika checkCriticalStock dipanggil dari controller setelah proses import selesai,
        // atau menjadikannya static method, atau memindahkan ke sebuah service class.

        // Untuk kesederhanaan saat ini, kita akan biarkan logika checkCriticalStock dipanggil terpisah
        // setelah import selesai (di `StockController@import`).
        return $stock; // Return model yang dibuat/diupdate
    }

    // Aturan validasi untuk setiap baris dari file Excel
    public function rules(): array
    {
        return [
            'nama_raw_material' => 'required|string|max:255|exists:raw_materials,name',
            'ready_stock' => 'required|integer|min:0',
            'in_process_stock' => 'required|integer|min:0',
            'process_status' => 'nullable|string|max:255',
            'expired_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    // Custom messages untuk validasi (opsional)
    public function customValidationMessages()
    {
        return [
            'nama_raw_material.exists' => 'Nama Raw Material tidak ditemukan di master data.',
            'nama_raw_material.required' => 'Kolom Nama Raw Material wajib diisi.',
            'ready_stock.required' => 'Kolom Ready Stock wajib diisi.',
            'in_process_stock.required' => 'Kolom Stock Dalam Proses wajib diisi.',
            'expired_date.date' => 'Kolom Tanggal Kedaluwarsa harus format tanggal yang valid (YYYY-MM-DD).',
            'expired_date.after_or_equal' => 'Tanggal Kedaluwarsa tidak boleh di masa lalu.',
        ];
    }

    // Method untuk menangani error (melompati baris yang error)
    public function onError(\Throwable $e)
    {
        // Opsional: Anda bisa log error di sini
        // Log::error('Error importing stock: ' . $e->getMessage());
    }
}