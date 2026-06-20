<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\RabbitMqPublisherService;
use App\Services\SoapAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Appointment Service API",
 *     version="1.0.0",
 *     description="API documentation for Service Jadwal Dokter"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8081/api",
 *     description="API Gateway"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-IAE-KEY"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/appointments",
     *     summary="Mengambil daftar seluruh jadwal konsultasi dokter",
     *     tags={"Appointments"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data appointments retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $appointments = Appointment::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data appointments retrieved successfully',
            'data' => $appointments,
            'meta' => [
                'service_name' => 'Appointment-Service',
                'api_version' => 'v1',
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/v1/appointments/{id}",
     *     summary="Mengambil detail jadwal konsultasi berdasarkan ID",
     *     tags={"Appointments"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID appointment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment detail retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found"
     *     )
     * )
     */
    public function show($id)
    {
        $appointment = Appointment::find($id);

        if (! $appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Appointment not found',
                'errors' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment detail retrieved successfully',
            'data' => $appointment,
            'meta' => [
                'service_name' => 'Appointment-Service',
                'api_version' => 'v1',
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/appointments",
     *     summary="Membuat jadwal konsultasi berdasarkan data pasien",
     *     tags={"Appointments"},
     *     security={{"ApiKeyAuth":{},"BearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                 "patient_id",
     *                 "doctor_name",
     *                 "specialization",
     *                 "appointment_date",
     *                 "appointment_time"
     *             },
     *             @OA\Property(
     *                 property="patient_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="doctor_name",
     *                 type="string",
     *                 example="dr. Clara"
     *             ),
     *             @OA\Property(
     *                 property="specialization",
     *                 type="string",
     *                 example="Dokter Umum"
     *             ),
     *             @OA\Property(
     *                 property="appointment_date",
     *                 type="string",
     *                 format="date",
     *                 example="2026-06-22"
     *             ),
     *             @OA\Property(
     *                 property="appointment_time",
     *                 type="string",
     *                 example="11:00"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="scheduled"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(
        Request $request,
        SoapAuditService $soapAuditService,
        RabbitMqPublisherService $rabbitMqPublisherService
    ) {
        $validated = $request->validate([
            'patient_id' => 'required|integer|min:1',
            'doctor_name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'nullable|string|max:255',
        ]);

        try {
            $patientUrl = rtrim(
                config('services.data_pasien.url'),
                '/'
            ).'/api/v1/patients/'.$validated['patient_id'];

            $patientResponse = Http::withHeaders([
                'X-IAE-KEY' => config('services.data_pasien.api_key'),
                'Accept' => 'application/json',
            ])->timeout(15)->get($patientUrl);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Pasien Service tidak dapat dihubungi',
                'errors' => [
                    'detail' => $exception->getMessage(),
                ],
            ], 502);
        }

        if (! $patientResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pasien tidak ditemukan atau tidak dapat diverifikasi',
                'errors' => [
                    'patient_id' => $validated['patient_id'],
                    'service_status' => $patientResponse->status(),
                    'service_response' => $patientResponse->json(),
                ],
            ], $patientResponse->status() === 404 ? 404 : 502);
        }

        $patientData = $patientResponse->json('data');

        if (! is_array($patientData) || empty($patientData['name'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Respons Data Pasien Service tidak valid',
                'errors' => null,
            ], 502);
        }

        $validated['patient_name'] = $patientData['name'];

        $appointment = Appointment::create($validated);

        $m2mResult = [
            'success' => false,
            'status_code' => 500,
            'message' => 'M2M token belum diperoleh',
        ];

        $soapAudit = [
            'success' => false,
            'status_code' => 500,
            'receipt_number' => null,
            'body' => 'SOAP Audit belum dijalankan',
        ];

        $rabbitMq = [
            'success' => false,
            'status_code' => 500,
            'body' => 'RabbitMQ belum dijalankan',
        ];

        $m2mToken = null;

        try {
            $m2mResponse = Http::timeout(20)->post(
                config('services.iae_sso.token_url'),
                [
                    'api_key' => config('services.iae_sso.api_key'),
                    'nim' => config('services.iae_sso.nim'),
                ]
            );

            $m2mToken = $m2mResponse->json('token')
                ?? $m2mResponse->json('access_token');

            $m2mResult = [
                'success' => $m2mResponse->successful() && ! empty($m2mToken),
                'status_code' => $m2mResponse->status(),
                'message' => ! empty($m2mToken)
                    ? 'M2M token berhasil diperoleh'
                    : 'M2M token tidak ditemukan pada respons SSO',
            ];
        } catch (\Throwable $exception) {
            $m2mResult = [
                'success' => false,
                'status_code' => 500,
                'message' => $exception->getMessage(),
            ];
        }

        if ($m2mToken) {
            $soapAudit = $soapAuditService->sendAppointmentAudit(
                $appointment->toArray(),
                $m2mToken
            );

            $soapBody = $soapAudit['body'] ?? null;

            $appointment->update([
                'soap_receipt_number' => $soapAudit['receipt_number'] ?? null,
                'soap_audit_response' => is_string($soapBody)
                    ? $soapBody
                    : json_encode($soapBody),
            ]);
        }

        if ($m2mToken && ($soapAudit['success'] ?? false)) {
            $rabbitMq = $rabbitMqPublisherService->publishAppointmentCreated(
                $appointment->fresh()->toArray(),
                $m2mToken
            );
        } elseif ($m2mToken) {
            $rabbitMq = [
                'success' => false,
                'status_code' => 424,
                'body' => 'RabbitMQ dilewati karena SOAP Audit gagal',
            ];
        }

        try {
            $farmasiUrl = rtrim(
                config('services.farmasi.url'),
                '/'
            ).'/api/v1/pharmacy';

            $farmasiResponse = Http::withHeaders([
                'X-IAE-KEY' => config('services.farmasi.api_key'),
                'Accept' => 'application/json',
            ])->timeout(15)->post($farmasiUrl, [
                'patient_id' => (string) $validated['patient_id'],
                'appointment_id' => (string) $appointment->id,
                'medicine_name' => 'Konsultasi Standar (Vitamin C)',
                'dosage' => '1 tablet',
                'frequency' => '1x sehari',
                'quantity' => 10,
                'instructions' => 'Diminum setelah makan',
                'status' => 'PENDING',
            ]);

            $farmasiResult = $farmasiResponse->successful()
                ? $farmasiResponse->json()
                : [
                    'error' => 'Gagal memanggil Farmasi Service',
                    'status_code' => $farmasiResponse->status(),
                    'details' => $farmasiResponse->json(),
                ];
        } catch (\Throwable $exception) {
            $farmasiResult = [
                'error' => 'Farmasi Service tidak dapat dihubungi',
                'message' => $exception->getMessage(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment created successfully',
            'data' => $appointment->fresh(),
            'integration' => [
                'data_pasien' => [
                    'success' => true,
                    'patient_id' => $patientData['id'] ?? $validated['patient_id'],
                    'patient_name' => $patientData['name'],
                ],
                'm2m_sso' => $m2mResult,
                'soap_audit' => [
                    'success' => $soapAudit['success'] ?? false,
                    'status_code' => $soapAudit['status_code'] ?? null,
                    'receipt_number' => $soapAudit['receipt_number'] ?? null,
                ],
                'rabbitmq_publish' => [
                    'success' => $rabbitMq['success'] ?? false,
                    'status_code' => $rabbitMq['status_code'] ?? null,
                    'response' => $rabbitMq['body'] ?? null,
                ],
                'farmasi_cross_service' => $farmasiResult,
            ],
            'meta' => [
                'service_name' => 'Appointment-Service',
                'api_version' => 'v1',
            ],
        ], 201);
    }
}
