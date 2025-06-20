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

Route::post('stocks/refresh-all', [StockController::class, 'refreshAllStockStatus'])->name('stocks.refresh.all');

// Routes untuk import kebutuhan bulanan
Route::get('monthly_requirements/import/form', [MonthlyRequirementController::class, 'importForm'])->name('monthly_requirements.import.form');
Route::post('monthly_requirements/import', [MonthlyRequirementController::class, 'import'])->name('monthly_requirements.import');

// Routes untuk import Master Data Raw Material
Route::get('raw_materials/import/form', [RawMaterialController::class, 'importForm'])->name('raw_materials.import.form');
Route::post('raw_materials/import', [RawMaterialController::class, 'import'])->name('raw_materials.import');

// Routes baru untuk import Stok Raw Material
Route::get('stocks/import/form', [StockController::class, 'importForm'])->name('stocks.import.form');
Route::post('stocks/import', [StockController::class, 'import'])->name('stocks.import');