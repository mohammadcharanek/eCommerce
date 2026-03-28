<?php

use Ecommerce\Payments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('payments.api_middleware', ['api']))
    ->prefix('api/v1')
    ->name('api.payments.')
    ->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('index');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    });
