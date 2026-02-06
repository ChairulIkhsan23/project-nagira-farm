<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TernakController;
use App\Http\Controllers\Api\ExportController;

// Route untuk API Ternak
Route::prefix('ternak')->group(function () {
    Route::get('/', [TernakController::class, 'index']);
    Route::get('/stats/summary', [TernakController::class, 'stats']);
    Route::get('/{id}', [TernakController::class, 'show']);
    Route::post('/', [TernakController::class, 'store']);
    Route::put('/{id}', [TernakController::class, 'update']);
    Route::delete('/{id}', [TernakController::class, 'destroy']);
});

// Route untuk Export 
Route::prefix('exports')->group(function () {
    Route::get('/ternak/formats', [ExportController::class, 'getFormats']);
    Route::get('/ternak/pdf', [ExportController::class, 'exportPDF']);      // GET
    Route::get('/ternak/excel', [ExportController::class, 'exportExcel']);  // GET
});