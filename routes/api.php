<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PatientController;

// Route utama untuk Service Data Pasien
Route::middleware('api.key')->group(function () {
    Route::get('v1', [PatientController::class, 'index']);
    Route::post('v1', [PatientController::class, 'store']);
    Route::get('v1/{id}', [PatientController::class, 'show'])->whereNumber('id');

    Route::get('v1/patients', [PatientController::class, 'index']);
    Route::post('v1/patients', [PatientController::class, 'store']);
    Route::get('v1/patients/{id}', [PatientController::class, 'show'])->whereNumber('id');

    Route::any('v1/{any?}', [PatientController::class, 'fallback'])->where('any', '.*');
});
