<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: '1.0.0',
        title: 'E-Healthcare Rawat Jalan API',
        description: 'API Service untuk proses bisnis Melakukan Rawat Jalan di E-Healthcare Telkom University',
    ),
    servers: [new OA\Server(url: 'http://localhost:8000')],
)]
#[OA\SecurityScheme(
    securityScheme: 'apiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-API-KEY',
)]

// ── Schema: PatientResource ──────────────────────────────────────────────────
#[OA\Schema(
    schema: 'PatientResource',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
        new OA\Property(property: 'nik', type: 'string', example: '3201234567890001'),
        new OA\Property(property: 'email', type: 'string', example: 'budi@example.com'),
        new OA\Property(property: 'phone', type: 'string', example: '081234567890'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1990-01-01'),
        new OA\Property(property: 'gender', type: 'string', enum: ['MALE', 'FEMALE'], example: 'MALE'),
        new OA\Property(property: 'address', type: 'string', example: 'Jl. Telekomunikasi No. 1, Bandung'),
        new OA\Property(property: 'medical_history', type: 'string', example: 'Riwayat hipertensi'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]

// ── Schema: PatientRequest ───────────────────────────────────────────────────
#[OA\Schema(
    schema: 'PatientRequest',
    required: ['name', 'nik', 'email', 'date_of_birth', 'gender'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
        new OA\Property(property: 'nik', type: 'string', example: '3201234567890001'),
        new OA\Property(property: 'email', type: 'string', example: 'budi@example.com'),
        new OA\Property(property: 'phone', type: 'string', example: '081234567890'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1990-01-01'),
        new OA\Property(property: 'gender', type: 'string', enum: ['MALE', 'FEMALE'], example: 'MALE'),
        new OA\Property(property: 'address', type: 'string', example: 'Jl. Telekomunikasi No. 1, Bandung'),
        new OA\Property(property: 'medical_history', type: 'string', example: 'Riwayat hipertensi'),
    ]
)]

// ── Schema: ApiMeta ──────────────────────────────────────────────────────────
#[OA\Schema(
    schema: 'ApiMeta',
    properties: [
        new OA\Property(property: 'service_name', type: 'string', example: 'E-Healthcare-Rawat-Jalan'),
        new OA\Property(property: 'api_version', type: 'string', example: 'v1'),
    ]
)]

// ── Schema: SuccessCollectionResponse ────────────────────────────────────────
#[OA\Schema(
    schema: 'SuccessCollectionResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'message', type: 'string', example: 'Data retrieved successfully'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PatientResource')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/ApiMeta'),
    ]
)]

// ── Schema: SuccessSingleResponse ────────────────────────────────────────────
#[OA\Schema(
    schema: 'SuccessSingleResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'message', type: 'string', example: 'Data retrieved successfully'),
        new OA\Property(property: 'data', ref: '#/components/schemas/PatientResource'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/ApiMeta'),
    ]
)]

// ── Schema: ErrorResponse ────────────────────────────────────────────────────
#[OA\Schema(
    schema: 'ErrorResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'message', type: 'string', example: 'Unable to process request'),
        new OA\Property(property: 'errors', nullable: true, example: null),
        new OA\Property(property: 'meta', ref: '#/components/schemas/ApiMeta'),
    ]
)]

#[OA\Tag(name: 'Patients', description: 'Service Data Pasien - Manajemen data rekam medis pasien')]
class PatientController extends Controller
{
    #[OA\Get(
        path: '/api/v1/patients',
        summary: 'Ambil semua data pasien',
        security: [['apiKeyAuth' => []]],
        tags: ['Patients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data pasien berhasil diambil',
                content: new OA\JsonContent(ref: '#/components/schemas/SuccessCollectionResponse')
            ),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function index(): JsonResponse
    {
        $patients = Patient::orderByDesc('created_at')->get();

        return $this->successResponse(
            PatientResource::collection($patients),
            'Data pasien berhasil diambil',
            $this->apiMeta()
        );
    }

    #[OA\Post(
        path: '/api/v1/patients',
        summary: 'Tambah data pasien baru',
        security: [['apiKeyAuth' => []]],
        tags: ['Patients'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PatientRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pasien berhasil ditambahkan',
                content: new OA\JsonContent(ref: '#/components/schemas/SuccessSingleResponse')
            ),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->validated());

        return $this->successResponse(
            new PatientResource($patient),
            'Pasien berhasil ditambahkan',
            $this->apiMeta()
        );
    }

    #[OA\Get(
        path: '/api/v1/patients/{id}',
        summary: 'Ambil data pasien berdasarkan ID',
        security: [['apiKeyAuth' => []]],
        tags: ['Patients'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'UUID pasien',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data pasien ditemukan',
                content: new OA\JsonContent(ref: '#/components/schemas/SuccessSingleResponse')
            ),
            new OA\Response(response: 404, description: 'Pasien tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function show(Patient $patient): JsonResponse
    {
        return $this->successResponse(
            new PatientResource($patient),
            'Data pasien ditemukan',
            $this->apiMeta()
        );
    }

    #[OA\Put(
        path: '/api/v1/patients/{id}',
        summary: 'Update data pasien',
        security: [['apiKeyAuth' => []]],
        tags: ['Patients'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PatientRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data pasien diperbarui'),
            new OA\Response(response: 404, description: 'Pasien tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function update(StorePatientRequest $request, Patient $patient): JsonResponse
    {
        if (! $patient->update($request->validated())) {
            return $this->errorResponse('Gagal memperbarui data pasien', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new PatientResource($patient),
            'Data pasien berhasil diperbarui',
            $this->apiMeta()
        );
    }

    #[OA\Delete(
        path: '/api/v1/patients/{id}',
        summary: 'Hapus data pasien',
        security: [['apiKeyAuth' => []]],
        tags: ['Patients'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pasien berhasil dihapus'),
            new OA\Response(response: 404, description: 'Pasien tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function destroy(Patient $patient): JsonResponse
    {
        if (! $patient->delete()) {
            return $this->errorResponse('Gagal menghapus data pasien', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new PatientResource($patient),
            'Pasien berhasil dihapus',
            $this->apiMeta()
        );
    }

    private function apiMeta(): array
    {
        return [
            'service_name' => 'E-Healthcare-Rawat-Jalan',
            'api_version' => 'v1',
        ];
    }
}
