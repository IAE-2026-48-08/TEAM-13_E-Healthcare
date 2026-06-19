<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePharmacyRequest;
use App\Http\Resources\PharmacyResource;
use App\Models\Pharmacy;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

// ── Schema: PharmacyResource ─────────────────────────────────────────────────
#[OA\Schema(
    schema: 'PharmacyResource',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440002'),
        new OA\Property(property: 'patient_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'appointment_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'medicine_name', type: 'string', example: 'Paracetamol 500mg'),
        new OA\Property(property: 'dosage', type: 'string', example: '500mg'),
        new OA\Property(property: 'frequency', type: 'string', example: '3x sehari'),
        new OA\Property(property: 'quantity', type: 'integer', example: 10),
        new OA\Property(property: 'instructions', type: 'string', example: 'Diminum setelah makan'),
        new OA\Property(property: 'status', type: 'string', enum: ['PENDING', 'PREPARING', 'READY_TO_PICKUP', 'DISPENSED'], example: 'PENDING'),
        new OA\Property(property: 'patient', ref: '#/components/schemas/PatientResource'),
        new OA\Property(property: 'appointment', ref: '#/components/schemas/AppointmentResource'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]

// ── Schema: PharmacyRequest ──────────────────────────────────────────────────
#[OA\Schema(
    schema: 'PharmacyRequest',
    required: ['patient_id', 'appointment_id', 'medicine_name', 'dosage', 'frequency', 'quantity'],
    properties: [
        new OA\Property(property: 'patient_id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
        new OA\Property(property: 'appointment_id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440001'),
        new OA\Property(property: 'medicine_name', type: 'string', example: 'Paracetamol 500mg'),
        new OA\Property(property: 'dosage', type: 'string', example: '500mg'),
        new OA\Property(property: 'frequency', type: 'string', example: '3x sehari'),
        new OA\Property(property: 'quantity', type: 'integer', example: 10),
        new OA\Property(property: 'instructions', type: 'string', example: 'Diminum setelah makan'),
        new OA\Property(property: 'status', type: 'string', enum: ['PENDING', 'PREPARING', 'READY_TO_PICKUP', 'DISPENSED'], example: 'PENDING'),
    ]
)]

#[OA\Tag(name: 'Pharmacy', description: 'Service Farmasi & Obat - Manajemen resep digital dan distribusi obat')]
class PharmacyController extends Controller
{
    #[OA\Get(
        path: '/api/v1/pharmacy',
        summary: 'Ambil semua data resep dan obat',
        security: [['apiKeyAuth' => []]],
        tags: ['Pharmacy'],
        responses: [
            new OA\Response(response: 200, description: 'Data resep berhasil diambil'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function index(): JsonResponse
    {
        $pharmacy = Pharmacy::with(['patient', 'appointment'])->orderByDesc('created_at')->get();

        return $this->successResponse(
            PharmacyResource::collection($pharmacy),
            'Data resep dan obat berhasil diambil',
            $this->apiMeta()
        );
    }

    #[OA\Post(
        path: '/api/v1/pharmacy',
        summary: 'Tambah resep obat digital baru',
        security: [['apiKeyAuth' => []]],
        tags: ['Pharmacy'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PharmacyRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Resep berhasil dicatat'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function store(StorePharmacyRequest $request): JsonResponse
    {
        $pharmacy = Pharmacy::create($request->validated());

        return $this->successResponse(
            new PharmacyResource($pharmacy->load(['patient', 'appointment'])),
            'Resep obat berhasil dicatat',
            $this->apiMeta()
        );
    }

    #[OA\Get(
        path: '/api/v1/pharmacy/{id}',
        summary: 'Ambil detail resep berdasarkan ID',
        security: [['apiKeyAuth' => []]],
        tags: ['Pharmacy'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data resep ditemukan'),
            new OA\Response(response: 404, description: 'Resep tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function show(Pharmacy $pharmacy): JsonResponse
    {
        return $this->successResponse(
            new PharmacyResource($pharmacy->load(['patient', 'appointment'])),
            'Data resep ditemukan',
            $this->apiMeta()
        );
    }

    #[OA\Put(
        path: '/api/v1/pharmacy/{id}',
        summary: 'Update data resep',
        security: [['apiKeyAuth' => []]],
        tags: ['Pharmacy'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PharmacyRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Resep berhasil diperbarui'),
            new OA\Response(response: 404, description: 'Resep tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function update(StorePharmacyRequest $request, Pharmacy $pharmacy): JsonResponse
    {
        if (! $pharmacy->update($request->validated())) {
            return $this->errorResponse('Gagal memperbarui data resep', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new PharmacyResource($pharmacy->load(['patient', 'appointment'])),
            'Data resep berhasil diperbarui',
            $this->apiMeta()
        );
    }

    #[OA\Delete(
        path: '/api/v1/pharmacy/{id}',
        summary: 'Hapus data resep',
        security: [['apiKeyAuth' => []]],
        tags: ['Pharmacy'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Resep berhasil dihapus'),
            new OA\Response(response: 404, description: 'Resep tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function destroy(Pharmacy $pharmacy): JsonResponse
    {
        if (! $pharmacy->delete()) {
            return $this->errorResponse('Gagal menghapus data resep', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new PharmacyResource($pharmacy),
            'Data resep berhasil dihapus',
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
