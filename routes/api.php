<?php

use App\Http\Controllers\Api\DatabaseOverviewController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\MobileOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/kiem-tra-du-lieu', DatabaseOverviewController::class);
Route::get('/chatbot/config', [ChatbotController::class, 'config']);
Route::post('/chatbot/message', [ChatbotController::class, 'message']);
Route::post('/chatbot/webhook', [ChatbotController::class, 'webhook']);

Route::prefix('mobile')->group(function () {
    Route::get('/tables', [MobileOrderController::class, 'tables']);
    Route::post('/tables/{table}/open', [MobileOrderController::class, 'openTable']);
    Route::get('/products', [MobileOrderController::class, 'products']);
    Route::post('/orders', [MobileOrderController::class, 'storeOrder']);
});
