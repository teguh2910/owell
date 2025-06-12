<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\MonthlyRequirementController;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('raw_materials', RawMaterialController::class);
Route::resource('monthly_requirements', MonthlyRequirementController::class);
Route::resource('stocks', StockController::class);

// Tambahkan route ini untuk fungsi refresh semua stok
// Gunakan method POST karena melakukan perubahan data
Route::post('stocks/refresh-all', [StockController::class, 'refreshAllStockStatus'])->name('stocks.refresh.all');