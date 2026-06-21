<?php

use App\Http\Controllers\CounterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('/counter', [CounterController::class, 'show'])->name('api.counter.show');
    Route::post('/counter/increment', [CounterController::class, 'increment'])->name('api.counter.increment');
    Route::post('/counter/decrement', [CounterController::class, 'decrement'])->name('api.counter.decrement');
});
