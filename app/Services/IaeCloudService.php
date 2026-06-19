<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IaeCloudService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('IAE_SSO_URL');
    }

    /**
     * Modul 1: Mendapatkan JWT Token dari Cloud Dosen via M2M
     */
   public function getSsoToken()
    {
        try {
            // Tambahkan withoutVerifying() untuk mem-bypass error SSL bawaan Windows
            $response = Http::withoutVerifying()->post($this->baseUrl . '/api/v1/auth/token', [
    'api_key' => env('IAE_API_KEY'),
    'nim' => '102022400238'
    ]);

            if ($response->successful()) {
                return $response->json('token') ?? $response->json('access_token');
            }

            // Tangkap dan kembalikan pesan error ASLI dari Dosen
            return 'DITOLAK DOSEN: Status ' . $response->status() . ' | Pesan: ' . $response->body();

        } catch (\Exception $e) {
            // Tangkap jika error terjadi di laptop lokalmu (misal koneksi terputus)
            return 'ERROR LOKAL: ' . $e->getMessage();
        }
    }
    /**
     * Modul 2: Mengirim Audit Log ke Sistem SOAP Dosen
     */
   /**
     * Modul 2: Mengirim Audit Log ke Sistem SOAP Dosen
     */
    public function sendSoapAudit($activityName, $logDataArray)
    {
        $token = $this->getSsoToken();
        if (!$token) {
            return 'Gagal: Tidak ada token SSO.';
        }

        $jsonData = json_encode($logDataArray);

        // XML harus RATA KIRI, tidak boleh ada spasi atau tab sebelum <?xml
        $xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
<soap:Body>
<iae:AuditRequest>
<iae:TeamID>TEAM-08</iae:TeamID>
<iae:ActivityName>' . $activityName . '</iae:ActivityName>
<iae:LogContent><![CDATA[' . $jsonData . ']]></iae:LogContent>
</iae:AuditRequest>
</soap:Body>
</soap:Envelope>';

        try {
            // Gunakan withBody() agar Laravel mengirimnya sebagai XML murni, bukan form-data
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->withBody($xmlBody, 'text/xml')
                ->post($this->baseUrl . '/soap/v1/audit');

            if ($response->successful()) {
                // Mengambil nomor resi dari response dosen
                preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/is', $response->body(), $matches);
                $receipt = $matches[1] ?? 'Resi tidak terbaca (Cek log)';
                return 'SUKSES SOAP | Resi: ' . $receipt;
            }

            return 'DITOLAK SOAP: ' . $response->body();

        } catch (\Exception $e) {
            return 'ERROR LOKAL SOAP: ' . $e->getMessage();
        }
    }

    /**
     * Modul 3: Mengirim Event Asinkron ke RabbitMQ Dosen
     */
    public function publishRabbitMQ($routingKey, $messagePayload)
    {
        // 1. Ambil token SSO lagi sebagai izin masuk
        $token = $this->getSsoToken();
        if (!$token) {
            return 'Gagal: Tidak ada token SSO.';
        }

        try {
            // 2. Tembak ke endpoint RabbitMQ dosen dengan payload JSON
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->post($this->baseUrl . '/api/v1/messages/publish', [
                    'routing_key' => $routingKey,
                    'payload' => $messagePayload
                ]);

            if ($response->successful()) {
                return 'SUKSES RABBITMQ | Pesan berhasil disebar ke exchange dosen.';
            }

            return 'DITOLAK RABBITMQ: ' . $response->body();

        } catch (\Exception $e) {
            return 'ERROR LOKAL RABBITMQ: ' . $e->getMessage();
        }
    }
}