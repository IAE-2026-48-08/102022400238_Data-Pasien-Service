<?php

namespace App\Http\Controllers\Api\V1;
use App\Services\IaeCloudService;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Service Data Pasien API",
    description: "API Documentation untuk Service Data Pasien E-Healthcare"
)]
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    in: "header",
    name: "X-IAE-KEY"
)]
class PatientController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: "/api/v1/patients",
        operationId: "getPatients",
        summary: "Mengambil daftar seluruh pasien",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Response(response: 200, description: "Berhasil mengambil data")]
    public function index()
    {
        $patients = Patient::all();
        return $this->successResponse($patients, 'Daftar seluruh data pasien berhasil diambil.');
    }

    #[OA\Get(
        path: "/api/v1/patients/{id}",
        operationId: "getPatientById",
        summary: "Mengambil detail data pasien berdasarkan ID",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Berhasil mengambil data")]
    #[OA\Response(response: 404, description: "Data pasien tidak ditemukan")]
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return $this->errorResponse('Data pasien tidak ditemukan.', 404);
        }

        return $this->successResponse($patient, 'Detail data pasien berhasil diambil.');
    }

    #[OA\Post(
        path: "/api/v1/patients",
        operationId: "storePatient",
        summary: "Menambahkan data pasien baru",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nik", "name", "birth_date", "gender"],
            properties: [
                new OA\Property(property: "nik", type: "string", example: "3201010101010001"),
                new OA\Property(property: "name", type: "string", example: "Budi Santoso"),
                new OA\Property(property: "birth_date", type: "string", format: "date", example: "1990-05-15"),
                new OA\Property(property: "gender", type: "string", example: "Laki-laki"),
                new OA\Property(property: "address", type: "string", example: "Jl. Telekomunikasi No. 1, Bandung"),
                new OA\Property(property: "medical_history", type: "string", example: "Alergi debu")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Data berhasil ditambahkan")]
    #[OA\Response(response: 400, description: "Validasi gagal")]

    public function store(Request $request)
    {
        // 1. Validasi Input Data
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:16|unique:patients',
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 400, $validator->errors());
        }

        // 2. Simpan Data ke Database Lokal
        $patient = Patient::create($request->all());

        // --- MULAI INTEGRASI CLOUD DOSEN ---
        $cloudService = new IaeCloudService();

        // 3. Kirim Audit Log ke SOAP Dosen
        // Mengubah data ke array agar mudah di-encode menjadi format yang diminta dosen
        $soapResponse = $cloudService->sendSoapAudit('PatientCreated', $patient->toArray());

        // 4. Broadcast Pengumuman ke RabbitMQ Dosen
        // Mengirimkan notifikasi ke iae.central.exchange agar diketahui departemen lain
        $rabbitPayload = [
            'event' => 'PatientCreated',
            'message' => 'Pasien baru telah terdaftar di sistem.',
            'data' => $patient->toArray()
        ];
        $rabbitResponse = $cloudService->publishRabbitMQ('patient.created', $rabbitPayload);
        // --- SELESAI INTEGRASI ---

        // 5. Kembalikan Response Akhir (beserta bukti resi dan status cloud)
        return response()->json([
            'success' => true,
            'message' => 'Data pasien baru berhasil ditambahkan dan disinkronisasi ke Cloud Dosen.',
            'data' => $patient,
            'cloud_sync_status' => [
                'soap_audit' => $soapResponse,
                'rabbitmq_broadcast' => $rabbitResponse
            ]
        ], 201);
    }
}