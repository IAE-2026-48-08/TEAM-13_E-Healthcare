<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

// ── Schema: AppointmentResource ──────────────────────────────────────────────
#[OA\Schema(
    schema: 'AppointmentResource',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440001'),
        new OA\Property(property: 'patient_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'doctor_name', type: 'string', example: 'dr. Siti Rahayu, Sp.PD'),
        new OA\Property(property: 'doctor_specialization', type: 'string', example: 'Penyakit Dalam'),
        new OA\Property(property: 'appointment_date', type: 'string', format: 'date-time', example: '2026-06-01T09:00:00Z'),
        new OA\Property(property: 'status', type: 'string', enum: ['SCHEDULED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'], example: 'SCHEDULED'),
        new OA\Property(property: 'complaint', type: 'string', example: 'Demam dan batuk sudah 3 hari'),
        new OA\Property(property: 'diagnosis', type: 'string', example: 'ISPA ringan'),
        new OA\Property(property: 'notes', type: 'string', example: 'Istirahat cukup, minum air putih'),
        new OA\Property(property: 'patient', ref: '#/components/schemas/PatientResource'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]

// ── Schema: AppointmentRequest ───────────────────────────────────────────────
#[OA\Schema(
    schema: 'AppointmentRequest',
    required: ['patient_id', 'doctor_name', 'appointment_date'],
    properties: [
        new OA\Property(property: 'patient_id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
        new OA\Property(property: 'doctor_name', type: 'string', example: 'dr. Siti Rahayu, Sp.PD'),
        new OA\Property(property: 'doctor_specialization', type: 'string', example: 'Penyakit Dalam'),
        new OA\Property(property: 'appointment_date', type: 'string', format: 'date-time', example: '2026-06-01T09:00:00Z'),
        new OA\Property(property: 'status', type: 'string', enum: ['SCHEDULED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'], example: 'SCHEDULED'),
        new OA\Property(property: 'complaint', type: 'string', example: 'Demam dan batuk sudah 3 hari'),
        new OA\Property(property: 'diagnosis', type: 'string', example: 'ISPA ringan'),
        new OA\Property(property: 'notes', type: 'string', example: 'Istirahat cukup, minum air putih'),
    ]
)]

#[OA\Tag(name: 'Appointments', description: 'Service Jadwal Dokter - Manajemen jadwal konsultasi dan booking pasien')]
class AppointmentController extends Controller
{
    #[OA\Get(
        path: '/api/v1/appointments',
        summary: 'Ambil semua jadwal konsultasi',
        security: [['apiKeyAuth' => []]],
        tags: ['Appointments'],
        responses: [
            new OA\Response(response: 200, description: 'Data jadwal berhasil diambil'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function index(): JsonResponse
    {
        $appointments = Appointment::with('patient')->orderByDesc('appointment_date')->get();

        return $this->successResponse(
            AppointmentResource::collection($appointments),
            'Data jadwal konsultasi berhasil diambil',
            $this->apiMeta()
        );
    }

    #[OA\Post(
        path: '/api/v1/appointments',
        summary: 'Buat jadwal konsultasi baru',
        security: [['apiKeyAuth' => []]],
        tags: ['Appointments'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AppointmentRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Jadwal berhasil dibuat'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = Appointment::create($request->validated());

        return $this->successResponse(
            new AppointmentResource($appointment->load('patient')),
            'Jadwal konsultasi berhasil dibuat',
            $this->apiMeta()
        );
    }

    #[OA\Get(
        path: '/api/v1/appointments/{id}',
        summary: 'Ambil detail jadwal konsultasi',
        security: [['apiKeyAuth' => []]],
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data jadwal ditemukan'),
            new OA\Response(response: 404, description: 'Jadwal tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function show(Appointment $appointment): JsonResponse
    {
        return $this->successResponse(
            new AppointmentResource($appointment->load('patient')),
            'Data jadwal konsultasi ditemukan',
            $this->apiMeta()
        );
    }

    #[OA\Put(
        path: '/api/v1/appointments/{id}',
        summary: 'Update jadwal konsultasi',
        security: [['apiKeyAuth' => []]],
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AppointmentRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Jadwal berhasil diperbarui'),
            new OA\Response(response: 404, description: 'Jadwal tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function update(StoreAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        if (! $appointment->update($request->validated())) {
            return $this->errorResponse('Gagal memperbarui jadwal', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new AppointmentResource($appointment->load('patient')),
            'Jadwal konsultasi berhasil diperbarui',
            $this->apiMeta()
        );
    }

    #[OA\Delete(
        path: '/api/v1/appointments/{id}',
        summary: 'Hapus jadwal konsultasi',
        security: [['apiKeyAuth' => []]],
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Jadwal berhasil dihapus'),
            new OA\Response(response: 404, description: 'Jadwal tidak ditemukan'),
            new OA\Response(response: 401, description: 'API Key tidak valid'),
        ]
    )]
    public function destroy(Appointment $appointment): JsonResponse
    {
        if (! $appointment->delete()) {
            return $this->errorResponse('Gagal menghapus jadwal', 500, null, $this->apiMeta());
        }

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Jadwal konsultasi berhasil dihapus',
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
