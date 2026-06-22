<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatRequestController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Endpoints (Accessible by guest Flutter clients)
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Public Chat Room API
Route::get('/messages', [ChatController::class, 'index'])->name('api.messages.index');
Route::post('/messages', [ChatController::class, 'store'])->name('api.messages.store');

// Authenticated Endpoints (Protected by Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // User directory & status
    Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('api.users.activity');

    // Chat requests
    Route::get('/chat-requests/incoming', [ChatRequestController::class, 'incoming'])->name('api.chat-requests.incoming');
    Route::get('/chat-requests/outgoing', [ChatRequestController::class, 'outgoing'])->name('api.chat-requests.outgoing');
    Route::post('/chat-requests', [ChatRequestController::class, 'store'])->name('api.chat-requests.store');
    Route::post('/chat-requests/{chatRequest}/accept', [ChatRequestController::class, 'accept'])->name('api.chat-requests.accept');
    Route::post('/chat-requests/{chatRequest}/decline', [ChatRequestController::class, 'decline'])->name('api.chat-requests.decline');

    // Private Direct messaging
    Route::get('/direct-messages/{userId}', [DirectMessageController::class, 'index'])->name('api.direct-messages.show');
    Route::post('/direct-messages', [DirectMessageController::class, 'store'])->name('api.direct-messages.store');
});
