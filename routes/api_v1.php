<?php

use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('groups', GroupController::class)->except(['update']);
    Route::patch('groups/{group}', [GroupController::class, 'update']);

    Route::apiResource('users', UserController::class)->except(['update']);
    Route::patch('users/{user}', [UserController::class, 'update']);

    Route::apiResource('payments', PaymentController::class)->except(['update']);
    Route::patch('payments/{payment}', [PaymentController::class, 'update']);
});
