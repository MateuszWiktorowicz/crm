<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CustomerController, UserController};


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('/klienci', CustomerController::class)
    ->only(['index', 'store', 'destroy']);

    Route::apiResource('/pracownicy', UserController::class)
    ->only(['index', 'store']);

    Route::put('/pracownicy/{user}', [UserController::class, 'edit']);
    Route::delete('/pracownicy/{user}', [UserController::class, 'delete']);
});
