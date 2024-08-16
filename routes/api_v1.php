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

    Route::apiResource('groups.payments', PaymentController::class)->except(['update']);
    Route::patch('groups/{group}/payments/{payment}', [PaymentController::class, 'update']);

    // Route::post('/groups/{group}/set-members', [GroupController::class, 'set_members']);
    Route::post('/groups/{group}/add-members', [GroupController::class, 'add_members']);
    Route::post('/groups/{group}/remove-members', [GroupController::class, 'remove_members']);

});
