<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoapAuditService
{
    private string $baseUrl = 'https://iae-sso.virtualfri.id';
    private string $teamId  = 'TEAM-13';

    /**
     * Kirim audit log ke SOAP endpoint dosen
     * Dipanggil saat transaksi kritis terjadi (resep baru dibuat)
     */
    public function sendAudit(string $token, string $activityName, array $logData): array
    {
        $logContent = json_encode($logData);

        $soapEnvelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
    <soap:Body>
        <iae:AuditRequest>
            <iae:TeamID>{$this->teamId}</iae:TeamID>
            <iae:ActivityName>{$activityName}</iae:ActivityName>
            <iae:LogContent><![CDATA[{$logContent}]]></iae:LogContent>
        </iae:AuditRequest>
    </soap:Body>
</soap:Envelope>
XML;

        try {
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction'   => 'audit',
                ])
                ->withBody($soapEnvelope, 'text/xml')
                ->post("{$this->baseUrl}/soap/v1/audit");

            $body = $response->body();
            Log::info('[SOAP] Audit terkirim', [
                'activity' => $activityName,
                'status'   => $response->status(),
                'response' => $body,
            ]);

            // Extract ReceiptNumber dari response XML
            $receiptNumber = null;
            if (preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/', $body, $matches)) {
                $receiptNumber = $matches[1];
            }

            return [
                'success'        => $response->successful(),
                'status'         => $response->status(),
                'receipt_number' => $receiptNumber,
                'raw_response'   => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[SOAP] Exception saat kirim audit', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
