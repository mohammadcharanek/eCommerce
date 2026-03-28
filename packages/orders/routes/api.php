<?php

use Ecommerce\Orders\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('orders.api_middleware', ['api']))
    ->prefix('api/v1')
    ->name('api.orders.')
    ->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('index');
        Route::post('/orders', [OrderController::class, 'store'])->name('store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
