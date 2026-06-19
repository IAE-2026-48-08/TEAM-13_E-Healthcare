<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsoService
{
    private string $baseUrl  = 'https://iae-sso.virtualfri.id';
    private string $apiKey   = 'KEY-MHS-157';
    private string $email    = 'warga29@ktp.iae.id';
    private string $password = 'KtpDigital2026!';

    /**
     * Login ke SSO dosen menggunakan M2M API Key
     */
    public function loginM2M(): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/v1/auth/token", [
                'api_key' => $this->apiKey, 'nim' => '102022400084',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[SSO] Login M2M berhasil', ['response' => $data]);
                return $data['token'] ?? $data['access_token'] ?? null;
            }

            Log::error('[SSO] Login M2M gagal', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('[SSO] Exception saat login M2M', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Login ke SSO dosen menggunakan email & password warga
     * Digunakan untuk SOAP dan RabbitMQ
     */
    public function loginWarga(): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/v1/auth/token", [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[SSO] Login Warga berhasil', ['response' => $data]);
                return $data['token'] ?? $data['access_token'] ?? null;
            }

            Log::error('[SSO] Login Warga gagal', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('[SSO] Exception saat login Warga', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Health check SSO
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
