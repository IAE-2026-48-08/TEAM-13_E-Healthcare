<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePharmacyRequest;
use App\Http\Resources\PharmacyResource;
use App\Models\Pharmacy;
use App\Services\RabbitMqService;
use App\Services\SoapAuditService;
use App\Services\SsoService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: '1.0.0',
        title: 'E-Healthcare Farmasi & Obat API',
        description: 'API Service untuk Service Farmasi & Obat dalam ekosistem E-Healthcare Telkom University',
    ),
    servers: [new OA\Server(url: 'http://127.0.0.1:8000')],
)]
#[OA\SecurityScheme(
    securityScheme: 'apiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-IAE-KEY',
)]
#[OA\Schema(
    schema: 'PharmacyResource',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440002'),
        new OA\Property(property: 'patient_id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
        new OA\Property(property: 'appointment_id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440001'),
        new OA\Property(property: 'medicine_name', type: 'string', example: 'Paracetamol 500mg'),
        new OA\Property(property: 'dosage', type: 'string', example: '500mg'),
        new OA\Property(property: 'frequency', type: 'string', example: '3x sehari'),
        new OA\Property(property: 'quantity', type: 'integer', example: 10),
        new OA\Property(property: 'instructions', type: 'string', example: 'Diminum setelah makan'),
        new OA\Property(property: 'status', type: 'string', enum: ['PENDING', 'PREPARING', 'READY_TO_PICKUP', 'DISPENSED'], example: 'PENDING'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'PharmacyRequest',
    required: ['medicine_name', 'dosage', 'frequency', 'quantity'],
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
    public function __construct(
        private SsoService $ssoService,
        private SoapAuditService $soapAuditService,
        private RabbitMqService $rabbitMqService,
    ) {}

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
        $pharmacy = Pharmacy::orderByDesc('created_at')->get();

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
        // Simpan resep ke database
        $pharmacy = Pharmacy::create($request->validated());

        $integrationResult = [
            'sso'      => null,
            'soap'     => null,
            'rabbitmq' => null,
        ];

        // Step 1: Login SSO Warga untuk dapat JWT (digunakan SOAP & RabbitMQ)
        $token = $this->ssoService->loginM2M();
        $integrationResult['sso'] = $token ? 'success' : 'failed';

        if ($token) {
            // Step 2: Kirim SOAP Audit (transaksi kritis: resep baru dibuat)
            $soapResult = $this->soapAuditService->sendAudit(
                $token,
                'PrescriptionCreated',
                [
                    'prescription_id' => $pharmacy->id,
                    'medicine_name'   => $pharmacy->medicine_name,
                    'dosage'          => $pharmacy->dosage,
                    'quantity'        => $pharmacy->quantity,
                    'status'          => $pharmacy->status,
                    'created_at'      => $pharmacy->created_at,
                ]
            );

            $integrationResult['soap'] = $soapResult['success'] ? 'success' : 'failed';
            if (isset($soapResult['receipt_number'])) {
                $integrationResult['soap_receipt'] = $soapResult['receipt_number'];
            }

            // Step 3: Publish event ke RabbitMQ
            $mqResult = $this->rabbitMqService->publishEvent(
                $token,
                'pharmacy.prescription.created',
                [
                    'prescription_id' => $pharmacy->id,
                    'patient_id'      => $pharmacy->patient_id,
                    'appointment_id'  => $pharmacy->appointment_id,
                    'medicine_name'   => $pharmacy->medicine_name,
                    'dosage'          => $pharmacy->dosage,
                    'frequency'       => $pharmacy->frequency,
                    'quantity'        => $pharmacy->quantity,
                    'instructions'    => $pharmacy->instructions,
                    'status'          => $pharmacy->status,
                    'service'         => 'E-Healthcare-Farmasi-dan-Obat',
                    'team_id'         => 'TEAM-13',
                    'created_at'      => $pharmacy->created_at,
                ]
            );

            $integrationResult['rabbitmq'] = $mqResult['success'] ? 'success' : 'failed';
        }

        return $this->successResponse(
            new PharmacyResource($pharmacy),
            'Resep obat berhasil dicatat',
            array_merge($this->apiMeta(), ['integration' => $integrationResult])
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
            new PharmacyResource($pharmacy),
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
            new PharmacyResource($pharmacy),
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
            'service_name' => 'E-Healthcare-Farmasi-dan-Obat',
            'api_version'  => 'v1',
        ];
    }
}
