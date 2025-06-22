<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); // Ini adalah rute default dari Laravel untuk API user

// Rute untuk chatbot Anda
Route::post('/chatbot/stock-status', [ChatbotController::class, 'getStockStatus']);

// Anda bisa menambahkan middleware otentikasi API di sini jika diperlukan
// Route::post('/chatbot/stock-status', [ChatbotController::class, 'getStockStatus'])->middleware('auth:sanctum');