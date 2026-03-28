<?php

use ECommerce\Inventory\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api', 'auth:sanctum', 'role:admin'])->group(function () {
    Route::get('inventory/{productId}', [InventoryController::class, 'show']);
    Route::put('inventory/{productId}', [InventoryController::class, 'update']);
});
