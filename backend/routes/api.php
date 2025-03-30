<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CoatingController,
    CustomerController,
    UserController, 
    ToolController,
    OfferController,
    SettingsController
};
use App\Http\Controllers\Import\CustomerImportController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('/klienci', CustomerController::class)
    ->only(['index', 'store']);

    Route::apiResource('/pracownicy', UserController::class)
    ->only(['index', 'store']);

    Route::put('/pracownicy/{user}', [UserController::class, 'edit']);
    Route::delete('/pracownicy/{user}', [UserController::class, 'delete']);

    Route::put('/klienci/{customer}', [CustomerController::class, 'edit']);

    Route::post('/klienci/import', [CustomerImportController::class, 'import']);
    Route::delete('/klienci/{customer}', [CustomerController::class, 'destroy']);
    Route::get('/dictionaries', [UserController::class, 'getUserDictionaries']);

    Route::get('/tools', [ToolController::class, 'index']);
    Route::get('/coatings', [CoatingController::class, 'index']);

    Route::get('/offers', [OfferController::class, 'index']);
    Route::post('/offers', [OfferController::class, 'store']);
    Route::put('/offers/{offer}', [OfferController::class, 'edit']);
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy']);
    Route::get('/offers/{offer}/generate-pdf', [OfferController::class, 'generateOfferPdf'])->name('offer.generatePdf');
    Route::put('/offers/{id}/update-number', [OfferController::class, 'updateOfferNumber']);


    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'store']);
    Route::put('/settings/{setting}', [SettingsController::class, 'update']);

});
