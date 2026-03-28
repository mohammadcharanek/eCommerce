<?php

use ECommerce\Order\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('{id}', [OrderController::class, 'show']);
        Route::put('{id}/cancel', [OrderController::class, 'cancel']);
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
