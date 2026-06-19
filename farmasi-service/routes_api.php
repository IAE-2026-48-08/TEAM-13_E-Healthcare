<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api.key')->group(function () {
    // Service Data Pasien
    Route::apiResource('patients', PatientController::class);

    // Service Jadwal Dokter
    Route::apiResource('appointments', AppointmentController::class);

    // Service Farmasi & Obat
    Route::apiResource('pharmacy', PharmacyController::class);
});
