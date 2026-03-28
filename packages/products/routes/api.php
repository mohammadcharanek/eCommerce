<?php

use Ecommerce\Products\Http\Controllers\CategoryController;
use Ecommerce\Products\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('products.api_middleware', ['api']))
    ->prefix('api/v1')
    ->name('api.products.')
    ->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('index');
        Route::post('/products', [ProductController::class, 'store'])->name('store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('show');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });
