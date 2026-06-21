<?php

use App\Http\Controllers\CounterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/counter', [CounterController::class, 'show'])->name('api.counter.show');
Route::get('/counter/stream', [CounterController::class, 'stream'])->name('api.counter.stream');
Route::post('/counter/increment', [CounterController::class, 'increment'])->name('api.counter.increment');
Route::post('/counter/decrement', [CounterController::class, 'decrement'])->name('api.counter.decrement');
