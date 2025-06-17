<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Imports\InvoicesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class InvoiceController extends Controller
{
    /**
     * Menampilkan daftar invoice dengan filter.
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

        // Filter berdasarkan Tanggal Invoice
        if ($request->has('invoice_date') && $request->invoice_date != '') {
            $query->whereDate('invoice_date', $request->invoice_date);
        }

        // Filter berdasarkan Customer
        if ($request->has('customer_name') && $request->customer_name != '') {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        $invoices = $query->latest()->paginate(10); // Paginate 10 item per halaman

        // Ambil daftar customer unik untuk dropdown filter
        $customers = Invoice::select('customer_name')->distinct()->pluck('customer_name');

        return view('invoices.index', compact('invoices', 'customers'));
    }

    /**
     * Menampilkan form upload Excel.
     */
    public function showUploadForm()
    {
        return view('invoices.upload');
    }

    /**
     * Mengimpor data dari Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048', // Max 2MB
        ]);

        try {
            Excel::import(new InvoicesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data invoice berhasil diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mencetak tanda terima berdasarkan tanggal invoice dan customer.
     */
    public function printReceipt(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string',
        ]);

        $invoiceDate = $request->invoice_date;
        $customerName = $request->customer_name;

        // Ambil invoice berdasarkan tanggal dan customer yang sama
        $invoicesToPrint = Invoice::whereDate('invoice_date', $invoiceDate)
                                ->where('customer_name', $customerName)
                                ->get();

        if ($invoicesToPrint->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada invoice ditemukan untuk tanggal dan customer tersebut.');
        }

        // Generate PDF
        $pdf = Pdf::loadView('invoices.receipt_template', compact('invoicesToPrint', 'invoiceDate', 'customerName'));

        // Simpan riwayat cetak ke database
        try {
            DB::beginTransaction(); // Mulai transaksi database
            foreach ($invoicesToPrint as $invoice) {
                Receipt::create([
                    'invoice_id' => $invoice->id,
                    'print_date' => now()->toDateString(), // Tanggal cetak hari ini
                ]);
            }
            DB::commit(); // Commit transaksi
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error
            return redirect()->back()->with('error', 'Gagal menyimpan riwayat cetak: ' . $e->getMessage());
        }

        // Unduh PDF
        return $pdf->download('tanda_terima_' . $customerName . '_' . $invoiceDate . '.pdf');
    }
}