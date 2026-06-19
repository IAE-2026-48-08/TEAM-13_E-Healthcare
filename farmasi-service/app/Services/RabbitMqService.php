<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RabbitMqService
{
    private string $baseUrl  = 'https://iae-sso.virtualfri.id';
    private string $exchange = 'iae.central.exchange';

    public function publishEvent(string $token, string $eventName, array $payload): array
    {
        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/api/v1/messages/publish", [
                    'exchange'    => $this->exchange,
                    'routing_key' => $eventName,
                    'payload'     => [
                        'event'     => $eventName,
                        'timestamp' => now()->toIso8601String(),
                        'service'   => 'E-Healthcare-Farmasi-dan-Obat',
                        'team_id'   => 'TEAM-13',
                        'data'      => $payload,
                    ],
                ]);

            Log::info('[RabbitMQ] Event published', [
                'event'  => $eventName,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('[RabbitMQ] Exception saat publish event', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}