<?php

use App\Modules\Cart\Controllers\CartController;
use App\Modules\Coupon\Controllers\CouponController;
use App\Modules\Inventory\Controllers\InventoryController;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Payment\Controllers\PaymentController;
use App\Modules\Product\Controllers\CategoryController;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\User\Controllers\AuthController;
use App\Modules\User\Controllers\RoleController;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api'])->group(function () {

    // Auth routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

    // Public product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);

    // Payment webhook (public, no auth)
    Route::post('payments/webhook', [PaymentController::class, 'webhook']);

    // Coupon validation (public)
    Route::post('coupons/validate', [CouponController::class, 'validate']);

    // Authenticated routes
    Route::middleware(['auth:sanctum'])->group(function () {

        // Cart routes
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('items', [CartController::class, 'addItem']);
            Route::put('items/{id}', [CartController::class, 'updateItem']);
            Route::delete('items/{id}', [CartController::class, 'removeItem']);
            Route::post('coupon', [CartController::class, 'applyCoupon']);
        });

        // Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::put('{id}/cancel', [OrderController::class, 'cancel']);
        });

        // Payment routes
        Route::post('payments/process', [PaymentController::class, 'process']);

        // Admin routes
        Route::middleware(['role:admin'])->group(function () {

            // Product management
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{id}', [ProductController::class, 'update']);
            Route::delete('products/{id}', [ProductController::class, 'destroy']);

            // Category management
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{id}', [CategoryController::class, 'update']);
            Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

            // Order status management
            Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);

            // Coupon management
            Route::get('coupons', [CouponController::class, 'index']);
            Route::post('coupons', [CouponController::class, 'store']);
            Route::put('coupons/{id}', [CouponController::class, 'update']);
            Route::delete('coupons/{id}', [CouponController::class, 'destroy']);

            // Inventory management
            Route::get('inventory/{productId}', [InventoryController::class, 'show']);
            Route::put('inventory/{productId}', [InventoryController::class, 'update']);

            // User management
            Route::get('users', [UserController::class, 'index']);
            Route::get('users/{id}', [UserController::class, 'show']);
            Route::put('users/{id}', [UserController::class, 'update']);
            Route::delete('users/{id}', [UserController::class, 'destroy']);

            // Role management
            Route::get('roles', [RoleController::class, 'index']);
        });
    });
});
