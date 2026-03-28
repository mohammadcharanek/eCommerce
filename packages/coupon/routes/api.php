<?php

use ECommerce\Coupon\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api'])->group(function () {

    Route::post('coupons/validate', [CouponController::class, 'validate']);

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('coupons', [CouponController::class, 'index']);
        Route::post('coupons', [CouponController::class, 'store']);
        Route::put('coupons/{id}', [CouponController::class, 'update']);
        Route::delete('coupons/{id}', [CouponController::class, 'destroy']);
    });
});
