<?php

use ECommerce\Cart\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('items', [CartController::class, 'addItem']);
        Route::put('items/{id}', [CartController::class, 'updateItem']);
        Route::delete('items/{id}', [CartController::class, 'removeItem']);
        Route::post('coupon', [CartController::class, 'applyCoupon']);
    });
});
