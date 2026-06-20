<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SoapAuditService
{
    public function sendAppointmentAudit(array $appointment, string $token): array
    {
        $url = env('IAE_SOAP_AUDIT_URL', 'https://iae-sso.virtualfri.id/soap/v1/audit');
        $apiKey = env('IAE_API_KEY', 'KEY-MHS-157');

        $xml = $this->buildSoapEnvelope($appointment);

       try {
    $response = Http::timeout(30)->withHeaders([
        'Content-Type' => 'text/xml; charset=utf-8',
        'Authorization' => 'Bearer ' . $token,
        'X-API-KEY' => $apiKey,
    ])->withBody($xml, 'text/xml')->post($url);
} catch (\Exception $e) {
    return [
        'success' => false,
        'status_code' => 500,
        'body' => $e->getMessage(),
        'receipt_number' => null,
    ];
}

        return [
            'success' => $response->successful(),
            'status_code' => $response->status(),
            'body' => $response->body(),
            'receipt_number' => $this->extractReceiptNumber($response->body()),
        ];
    }

private function buildSoapEnvelope(array $appointment): string
{
    $teamId = 'TEAM-13';
    $activityName = 'CREATE_APPOINTMENT';

    $logContent = json_encode([
        'service_name' => 'Appointment-Service',
        'api_key' => env('IAE_API_KEY'),
        'action' => 'appointment.created',
        'appointment' => [
            'patient_name' => $appointment['patient_name'],
            'doctor_name' => $appointment['doctor_name'],
            'specialization' => $appointment['specialization'],
            'appointment_date' => $appointment['appointment_date'],
            'appointment_time' => $appointment['appointment_time'],
            'status' => $appointment['status'] ?? 'scheduled',
        ],
    ]);

    return '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
    <soap:Body>
        <iae:AuditRequest>
            <iae:TeamID>' . htmlspecialchars($teamId) . '</iae:TeamID>
            <iae:ActivityName>' . htmlspecialchars($activityName) . '</iae:ActivityName>
            <iae:LogContent><![CDATA[' . $logContent . ']]></iae:LogContent>
        </iae:AuditRequest>
    </soap:Body>
</soap:Envelope>';
}

private function extractReceiptNumber(string $xml): ?string
{
    if (preg_match('/<[^:>]*:?ReceiptNumber>(.*?)<\/[^:>]*:?ReceiptNumber>/', $xml, $matches)) {
        return $matches[1];
    }

    if (preg_match('/<[^:>]*:?receipt_number>(.*?)<\/[^:>]*:?receipt_number>/', $xml, $matches)) {
        return $matches[1];
    }

    return null;
    }
}