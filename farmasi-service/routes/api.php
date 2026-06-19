<?php

use App\Http\Controllers\Api\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api.key')->group(function () {
    // Service Farmasi & Obat
    Route::apiResource('pharmacy', PharmacyController::class);
});