<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\IaeCloudService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Info(
    version: "1.0.0",
    title: "Service Data Pasien API",
    description: "API Documentation untuk Service Data Pasien E-Healthcare"
)]
#[OA\Server(
    url: "http://localhost:8001",
    description: "Local Docker server"
)]
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    in: "header",
    name: "X-IAE-KEY"
)]
#[OA\Schema(
    schema: "Patient",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nik", type: "string", example: "3201010101010001"),
        new OA\Property(property: "name", type: "string", example: "Budi Santoso"),
        new OA\Property(property: "birth_date", type: "string", format: "date", example: "1990-05-15"),
        new OA\Property(property: "gender", type: "string", example: "Laki-laki"),
        new OA\Property(property: "address", type: "string", nullable: true, example: "Jl. Telekomunikasi No. 1, Bandung"),
        new OA\Property(property: "medical_history", type: "string", nullable: true, example: "Alergi debu"),
    ]
)]
class PatientController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: "/api/v1",
        operationId: "getPatients",
        summary: "Mengambil daftar seluruh pasien",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Get(
        path: "/api/v1/patients",
        operationId: "getPatientsResource",
        summary: "Mengambil daftar seluruh pasien",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Response(
        response: 200,
        description: "Berhasil mengambil data",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Daftar seluruh data pasien berhasil diambil."),
                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Patient")),
                new OA\Property(property: "errors", nullable: true, example: null),
            ]
        )
    )]
    public function index()
    {
        $patients = Patient::all();
        return $this->successResponse($patients, 'Daftar seluruh data pasien berhasil diambil.');
    }

    #[OA\Get(
        path: "/api/v1/{id}",
        operationId: "getPatientById",
        summary: "Mengambil detail data pasien berdasarkan ID",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Get(
        path: "/api/v1/patients/{id}",
        operationId: "getPatientResourceById",
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
    #[OA\Response(
        response: 200,
        description: "Berhasil mengambil data",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Detail data pasien berhasil diambil."),
                new OA\Property(property: "data", ref: "#/components/schemas/Patient"),
                new OA\Property(property: "errors", nullable: true, example: null),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Data pasien tidak ditemukan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "Data pasien tidak ditemukan."),
                new OA\Property(property: "data", nullable: true, example: null),
                new OA\Property(property: "errors", nullable: true, example: null),
            ]
        )
    )]
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return $this->errorResponse('Data pasien tidak ditemukan.', 404);
        }

        return $this->successResponse($patient, 'Detail data pasien berhasil diambil.');
    }

    #[OA\Post(
        path: "/api/v1",
        operationId: "storePatient",
        summary: "Menambahkan data pasien baru",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\Post(
        path: "/api/v1/patients",
        operationId: "storePatientResource",
        summary: "Menambahkan data pasien baru",
        security: [["ApiKeyAuth" => []]],
        tags: ["Patients"]
    )]
    #[OA\RequestBody(
        required: false,
        content: new OA\JsonContent(
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
    #[OA\Response(
        response: 201,
        description: "Data berhasil ditambahkan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Data pasien baru berhasil ditambahkan."),
                new OA\Property(property: "data", type: "object"),
                new OA\Property(property: "errors", nullable: true, example: null),
            ]
        )
    )]

    public function store(Request $request)
    {
        $patient = Patient::create($this->patientPayload($request));
        $cloudStatus = $this->syncCloud($patient);

        return $this->successResponse([
            'patient' => $patient,
            'cloud_sync_status' => $cloudStatus,
        ], 'Data pasien baru berhasil ditambahkan.', 201);
    }

    public function fallback(Request $request, ?string $any = null)
    {
        $path = trim((string) $any, '/');

        if ($path === '') {
            if ($request->isMethod('get')) {
                return $this->index();
            }

            if ($request->isMethod('post')) {
                return $this->store($request);
            }

            return $this->errorResponse('Method tidak diizinkan untuk endpoint ini.', 405);
        }

        if ($request->isMethod('get') && ctype_digit($path)) {
            return $this->show($path);
        }

        return $this->errorResponse('Endpoint tidak ditemukan.', 404);
    }

    private function patientPayload(Request $request): array
    {
        return [
            'nik' => $this->uniqueNik($request->input('nik')),
            'name' => $this->stringValue($request->input('name'), 'Pasien Baru'),
            'birth_date' => $this->dateValue($request->input('birth_date')),
            'gender' => $this->genderValue($request->input('gender')),
            'address' => $this->nullableString($request->input('address')),
            'medical_history' => $this->nullableString($request->input('medical_history')),
        ];
    }

    private function uniqueNik(mixed $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        $nik = substr(str_pad($digits ?: '3201'.date('ymdHis'), 16, '0'), 0, 16);

        while (Patient::where('nik', $nik)->exists()) {
            $nik = substr($nik, 0, 10).random_int(100000, 999999);
        }

        return $nik;
    }

    private function stringValue(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        return $value !== '' ? substr($value, 0, 255) : $fallback;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function dateValue(mixed $value): string
    {
        $timestamp = strtotime((string) $value);

        return $timestamp ? date('Y-m-d', $timestamp) : '2000-01-01';
    }

    private function genderValue(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, ['perempuan', 'p'], true) ? 'Perempuan' : 'Laki-laki';
    }

    private function syncCloud(Patient $patient): array
    {
        if (!filter_var(env('IAE_CLOUD_SYNC', false), FILTER_VALIDATE_BOOL)) {
            return [
                'soap_audit' => 'SKIPPED: IAE_CLOUD_SYNC=false',
                'rabbitmq_broadcast' => 'SKIPPED: IAE_CLOUD_SYNC=false',
            ];
        }

        try {
            $cloudService = new IaeCloudService();
            $soapResponse = $cloudService->sendSoapAudit('PatientCreated', $patient->toArray());
            $rabbitResponse = $cloudService->publishRabbitMQ('patient.created', [
                'event' => 'PatientCreated',
                'message' => 'Pasien baru telah terdaftar di sistem.',
                'data' => $patient->toArray(),
            ]);

            return [
                'soap_audit' => $soapResponse,
                'rabbitmq_broadcast' => $rabbitResponse,
            ];
        } catch (Throwable $e) {
            return [
                'soap_audit' => 'ERROR: '.$e->getMessage(),
                'rabbitmq_broadcast' => 'ERROR: '.$e->getMessage(),
            ];
        }
    }
}
