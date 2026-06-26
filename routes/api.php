<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PatientController;
use App\Services\IaeCloudService;

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
Route::middleware('api.key')->group(function () {
    Route::get('v1', [PatientController::class, 'index']);
    Route::post('v1', [PatientController::class, 'store']);
    Route::get('v1/{id}', [PatientController::class, 'show'])->whereNumber('id');

    Route::get('v1/patients', [PatientController::class, 'index']);
    Route::post('v1/patients', [PatientController::class, 'store']);
    Route::get('v1/patients/{id}', [PatientController::class, 'show'])->whereNumber('id');

    Route::any('v1/{any?}', [PatientController::class, 'fallback'])->where('any', '.*');
});

// Route rahasia untuk tes ambil token SSO Dosen
// Route rahasia untuk tes ambil token SSO Dosen
Route::get('/test-sso', function (IaeCloudService $cloudService) {
    $hasil = $cloudService->getSsoToken();
    
    // Cek apakah hasilnya mengandung kata "ERROR" atau "DITOLAK"
    if (str_starts_with((string)$hasil, 'ERROR') || str_starts_with((string)$hasil, 'DITOLAK')) {
        return response()->json([
            'status' => 'Gagal',
            'penyebab_asli' => $hasil
        ], 500);
    }

    return response()->json([
        'status' => 'Sukses!',
        'pesan' => 'Berhasil membobol pintu masuk SSO Dosen.',
        'token_didapat' => $hasil
    ]);
});

// Route rahasia untuk tes kirim data Audit SOAP
Route::get('/test-soap', function (App\Services\IaeCloudService $cloudService) {
    // Simulasi data pasien baru yang akan diaudit
    $dataPasien = [
        "nik" => "3201010101010002",
        "name" => "Siti Aminah",
        "action" => "Pendaftaran Pasien Baru"
    ];

    $hasil = $cloudService->sendSoapAudit('PatientCreated', $dataPasien);
    
    return response()->json([
        'status_pengujian' => 'Selesai',
        'hasil_soap' => $hasil
    ]);
});

// Route rahasia untuk tes broadcast RabbitMQ
Route::get('/test-rabbitmq', function (App\Services\IaeCloudService $cloudService) {
    // Simulasi data pasien yang akan diumumkan ke departemen lain
    $dataPengumuman = [
        "event" => "PatientCreated",
        "data" => [
            "nik" => "3201010101010002",
            "name" => "Siti Aminah",
            "message" => "Tolong siapkan rekam medis untuk pasien baru ini."
        ]
    ];

    // Menggunakan routing key 'patient.created'
    $hasil = $cloudService->publishRabbitMQ('patient.created', $dataPengumuman);
    
    return response()->json([
        'status_pengujian' => 'Selesai',
        'hasil_rabbitmq' => $hasil
    ]);
});
