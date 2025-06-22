<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute untuk chatbot Anda
Route::post('/chatbot/stock-status', [ChatbotController::class, 'getStockStatus']);
// Rute baru untuk cek stok kritis
Route::post('/chatbot/urgent-stocks', [ChatbotController::class, 'getUrgentStocks']);

// Anda bisa menambahkan middleware otentikasi API di sini jika diperlukan
// Route::post('/chatbot/stock-status', [ChatbotController::class, 'getStockStatus'])->middleware('auth:sanctum');
// Route::post('/chatbot/urgent-stocks', [ChatbotController::class, 'getUrgentStocks'])->middleware('auth:sanctum');