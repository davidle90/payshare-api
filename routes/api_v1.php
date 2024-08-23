<?php

use App\Http\Controllers\Api\V1\FriendRequestController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GroupMemberController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {

    Route::apiResource('users', UserController::class)->except(['update']);
    Route::patch('users/{user}', [UserController::class, 'update']);

    Route::post('/friend-request/send', [FriendRequestController::class, 'sendRequest']);
    Route::post('/friend-request/accept/{friendRequest}', [FriendRequestController::class, 'acceptRequest']);
    Route::post('/friend-request/decline/{friendRequest}', [FriendRequestController::class, 'declineRequest']);
    Route::post('/friend-request/remove/{friend}', [FriendRequestController::class, 'removeFriend']);

    // Route::post('/users/{user}/add-friends', [UserController::class, 'add_friends']);
    // Route::post('/users/{user}/remove-friends', [UserController::class, 'remove_friends']);

    Route::apiResource('groups', GroupController::class)->except(['update']);
    Route::patch('groups/{group}', [GroupController::class, 'update']);

    Route::get('/groups/{group}/calculate-balance', [GroupController::class, 'calculate_balance']);
    Route::get('/group/{group}/simplify_payments', [GroupController::class, 'simplify_payments']);

    Route::apiResource('groups.payments', PaymentController::class)->except(['update']);
    Route::patch('groups/{group}/payments/{payment}', [PaymentController::class, 'update']);
    Route::post('/groups/{group}/payments/{payment}/contributors', [PaymentController::class, 'contributors']);
    Route::post('/groups/{group}/payments/{payment}/participants', [PaymentController::class, 'participants']);

    Route::post('/groups/{group}/add-members', [GroupMemberController::class, 'add_members']);
    Route::post('/groups/{group}/remove-members', [GroupMemberController::class, 'remove_members']);
    Route::post('/users/join-group', [GroupMemberController::class, 'join_group']);
    Route::post('/users/leave-group', [GroupMemberController::class, 'leave_group']);
});
