<?php

use ECommerce\Payment\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api'])->group(function () {
    Route::post('payments/webhook', [PaymentController::class, 'webhook']);
    Route::post('payments/process', [PaymentController::class, 'process'])->middleware('auth:sanctum');
});
