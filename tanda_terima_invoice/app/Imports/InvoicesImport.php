<?php

namespace App\Imports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Untuk membaca header baris pertama
use PhpOffice\PhpSpreadsheet\Shared\Date; // Untuk mengkonversi tanggal Excel

class InvoicesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan nama kolom di Excel sesuai (case-insensitive, tapi lebih baik konsisten)
        $invoiceNumber = $row['nomor_invoice'] ?? null;
        $invoiceDate = $row['tanggal_invoice'] ?? null;
        $customerName = $row['customer'] ?? null;
        $taxInvoiceNumber = $row['nomor_faktur_pajak'] ?? null;

        // Konversi tanggal dari format Excel ke format Y-m-d
        // Maatwebsite/Excel biasanya sudah mencoba mengkonversi tanggal otomatis
        // Tapi jika ada masalah, Anda bisa menggunakan Date::excelToDateTimeObject
        if ($invoiceDate && is_numeric($invoiceDate)) {
             $invoiceDate = Date::excelToDateTimeObject($invoiceDate)->format('Y-m-d');
        } else {
             // Jika tanggal sudah dalam string, pastikan formatnya benar
             // Atau lakukan validasi/konversi lebih lanjut jika perlu
             $invoiceDate = date('Y-m-d', strtotime($invoiceDate)); // Contoh sederhana, sesuaikan
        }


        // Cek apakah invoice_number sudah ada untuk mencegah duplikasi
        // Anda bisa memilih untuk update, skip, atau lempar error
        $existingInvoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        if ($existingInvoice) {
            // Contoh: update data jika invoice_number sudah ada
            $existingInvoice->update([
                'invoice_date'       => $invoiceDate,
                'customer_name'      => $customerName,
                'tax_invoice_number' => $taxInvoiceNumber,
            ]);
            return null; // Tidak membuat record baru
        }

        return new Invoice([
            'invoice_number'     => $invoiceNumber,
            'invoice_date'       => $invoiceDate,
            'customer_name'      => $customerName,
            'tax_invoice_number' => $taxInvoiceNumber,
        ]);
    }
}