<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/upload', [InvoiceController::class, 'showUploadForm'])->name('invoices.upload.form');
Route::post('/invoices/import', [InvoiceController::class, 'import'])->name('invoices.import');
Route::get('/invoices/print', [InvoiceController::class, 'printReceipt'])->name('invoices.print'); // Menggunakan GET untuk contoh, bisa POST juga