<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $serverUrl;

    public function __construct()
    {
        $this->serverUrl = config('services.whatsapp.server_url', 'http://localhost:3000');
    }

    public function sendText(string $to, string $message): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->serverUrl}/send", [
                'to'      => $this->formatPhoneNumber($to),
                'message' => $message,
            ]);

            if ($response->failed()) {
                Log::error('WhatsApp server error', [
                    'to'       => $to,
                    'status'   => $response->status(),
                    'response' => $response->json(),
                ]);
                return ['success' => false, 'response' => $response->json()];
            }

            return ['success' => true, 'response' => $response->json()];

        } catch (\Exception $e) {
            Log::error('WhatsApp excepción: ' . $e->getMessage());
            return ['success' => false, 'response' => ['error' => $e->getMessage()]];
        }
    }

    public function serverOnline(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->serverUrl}/status");
            return $response->successful() && ($response->json('ready') === true);
        } catch (\Exception) {
            return false;
        }
    }

    public function formatPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '0')) {
            $clean = substr($clean, 1);
        }

        // Argentina: si tiene 10 dígitos sin código de país, agregar 549
        if (strlen($clean) === 10) {
            $clean = '549' . $clean;
        }

        // Si tiene 11 dígitos y empieza con 54 (sin el 9 del móvil), agregar el 9
        if (strlen($clean) === 12 && str_starts_with($clean, '54') && !str_starts_with($clean, '549')) {
            $clean = '549' . substr($clean, 2);
        }

        return $clean;
    }
}
