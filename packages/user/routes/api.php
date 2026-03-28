<?php

use ECommerce\User\Controllers\AuthController;
use ECommerce\User\Controllers\RoleController;
use ECommerce\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
        Route::get('roles', [RoleController::class, 'index']);
    });
});
