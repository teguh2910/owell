<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\MonthlyRequirementController;
use App\Http\Controllers\StockController;

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

// Rute untuk Halaman Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rute untuk Logout (menggunakan POST untuk keamanan)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {

    // Dashboard Utama (Setelah Login)
    // Bisa diakses oleh Admin, PPIC, dan Supplier
    Route::get('/', function () {
        return view('dashboard');
    })->middleware('role:admin,ppic,supplier')->name('dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('role:admin,ppic,supplier');


    // --- RUTE MASTER DATA RAW MATERIAL ---
    // Semua role (Admin, PPIC, Supplier) bisa melihat daftar dan detail Raw Material
    Route::middleware('role:admin,ppic,supplier')->group(function () {
        Route::get('raw_materials', [RawMaterialController::class, 'index'])->name('raw_materials.index');
        Route::get('raw_materials/{raw_material}', [RawMaterialController::class, 'show'])->name('raw_materials.show');
    });

    // Hanya Admin yang bisa CRUD dan import Master Data Raw Material
    Route::middleware('role:admin')->group(function () {
        Route::get('raw_materials/create', [RawMaterialController::class, 'create'])->name('raw_materials.create');
        Route::post('raw_materials', [RawMaterialController::class, 'store'])->name('raw_materials.store');
        Route::get('raw_materials/{raw_material}/edit', [RawMaterialController::class, 'edit'])->name('raw_materials.edit');
        Route::put('raw_materials/{raw_material}', [RawMaterialController::class, 'update'])->name('raw_materials.update');
        Route::delete('raw_materials/{raw_material}', [RawMaterialController::class, 'destroy'])->name('raw_materials.destroy');
        Route::get('raw_materials/import/form', [RawMaterialController::class, 'importForm'])->name('raw_materials.import.form');
        Route::post('raw_materials/import', [RawMaterialController::class, 'import'])->name('raw_materials.import');
    });


    // --- RUTE KEBUTUHAN BULANAN ---
    // Admin dan PPIC bisa CRUD dan import Kebutuhan Bulanan
    // Jika Anda ingin PPIC tidak bisa 'destroy' (hapus), tambahkan ->except(['destroy'])
    Route::middleware('role:admin,ppic')->group(function () {
        Route::resource('monthly_requirements', MonthlyRequirementController::class);
        Route::get('monthly_requirements/import/form', [MonthlyRequirementController::class, 'importForm'])->name('monthly_requirements.import.form');
        Route::post('monthly_requirements/import', [MonthlyRequirementController::class, 'import'])->name('monthly_requirements.import');
    });


    // --- RUTE MANAJEMEN STOK ---
    // Semua role (Admin, PPIC, Supplier) bisa melihat daftar Stok, edit/update Stok mereka, dan refresh semua status stok
    Route::middleware('role:admin,ppic,supplier')->group(function () {
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
        Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
        Route::post('stocks/refresh-all', [StockController::class, 'refreshAllStockStatus'])->name('stocks.refresh.all');
    });

    // Admin dan PPIC bisa import data Stok
    Route::middleware('role:admin,ppic')->group(function () {
        Route::get('stocks/import/form', [StockController::class, 'importForm'])->name('stocks.import.form');
        Route::post('stocks/import', [StockController::class, 'import'])->name('stocks.import');
    });

    // Hanya Admin yang bisa membuat (create/store) atau menghapus (destroy) data Stok
    Route::middleware('role:admin')->group(function () {
        Route::get('stocks/create', [StockController::class, 'create'])->name('stocks.create');
        Route::post('stocks', [StockController::class, 'store'])->name('stocks.store');
        Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
    });

});