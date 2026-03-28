<?php

use Ecommerce\Products\Http\Controllers\CategoryController;
use Ecommerce\Products\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('products.middleware', ['web']))
    ->prefix(config('products.route_prefix', 'products'))
    ->name('products.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

Route::middleware(config('products.middleware', ['web']))
    ->prefix('categories')
    ->name('categories.')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    });
