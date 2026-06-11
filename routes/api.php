<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PatientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route bawaan Laravel Sanctum (di-comment agar tidak mengganggu)
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route utama untuk Service Data Pasien
Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('/patients', [PatientController::class, 'index']);      // Collection: Ambil semua data
    Route::get('/patients/{id}', [PatientController::class, 'show']);  // Resource: Ambil detail data
    Route::post('/patients', [PatientController::class, 'store']);     // Action: Tambah data pasien
});