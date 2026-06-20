<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternalServiceClient
{
    /**
     * Base URL Data Pasien Service (dipanggil langsung via nama container Docker,
     * bukan lewat API Gateway, karena ini panggilan internal antar service).
     */
    private string $dataPasienBaseUrl;

    /**
     * Base URL Appointment Service (dipanggil langsung via nama container Docker).
     */
    private string $appointmentBaseUrl;

    public function __construct()
    {
        $this->dataPasienBaseUrl = config('services.internal.data_pasien_url', 'http://data-pasien-service:8000');
        $this->appointmentBaseUrl = config('services.internal.appointment_url', 'http://appointment-service:8000');
    }

    /**
     * Ambil detail pasien dari Data Pasien Service berdasarkan ID.
     *
     * @return array{success: bool, data: array|null, message: string}
     */
    public function getPatient(string $patientId): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-IAE-KEY' => config('services.internal.data_pasien_key', '102022400238'),
                ])
                ->get("{$this->dataPasienBaseUrl}/api/v1/patients/{$patientId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json('data'),
                    'message' => 'Data pasien ditemukan',
                ];
            }

            if ($response->status() === 404) {
                return [
                    'success' => false,
                    'data'    => null,
                    'message' => 'Pasien tidak ditemukan',
                ];
            }

            Log::warning('Data Pasien Service merespons error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'data'    => null,
                'message' => 'Gagal menghubungi Data Pasien Service',
            ];
        } catch (\Throwable $e) {
            Log::error('Gagal konek ke Data Pasien Service', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'data'    => null,
                'message' => 'Data Pasien Service tidak dapat diakses',
            ];
        }
    }

    /**
     * Ambil detail appointment dari Appointment Service berdasarkan ID.
     *
     * @return array{success: bool, data: array|null, message: string}
     */
    public function getAppointment(string $appointmentId): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-IAE-KEY' => config('services.internal.appointment_key', 'KEY-MHS-157'),
                ])
                ->get("{$this->appointmentBaseUrl}/api/v1/appointments/{$appointmentId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json('data'),
                    'message' => 'Data appointment ditemukan',
                ];
            }

            if ($response->status() === 404) {
                return [
                    'success' => false,
                    'data'    => null,
                    'message' => 'Appointment tidak ditemukan',
                ];
            }

            Log::warning('Appointment Service merespons error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'data'    => null,
                'message' => 'Gagal menghubungi Appointment Service',
            ];
        } catch (\Throwable $e) {
            Log::error('Gagal konek ke Appointment Service', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'data'    => null,
                'message' => 'Appointment Service tidak dapat diakses',
            ];
        }
    }
}
