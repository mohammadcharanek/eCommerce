<?php

use Ecommerce\Payments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('payments.middleware', ['web']))
    ->prefix(config('payments.route_prefix', 'payments'))
    ->name('payments.')
    ->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/orders/{order}/pay', [PaymentController::class, 'create'])->name('create');
        Route::post('/orders/{order}/pay', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    });
