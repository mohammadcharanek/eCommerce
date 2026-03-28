<?php

use Ecommerce\Orders\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('orders.middleware', ['web']))
    ->prefix(config('orders.route_prefix', 'orders'))
    ->name('orders.')
    ->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
