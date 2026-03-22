<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $fromNumber;
    protected string $apiVersion = 'v22.0';

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_NUMBER_ID');
        $this->fromNumber = env('WHATSAPP_FROM_PHONE_NUMBER');
    }

    /**
     * Enviar mensaje de texto
     *
     * @param string $to Número destino (formato: 5493826541085)
     * @param string $message Texto del mensaje
     * @return array
     */
    public function sendText(string $to, string $message): array
    {
        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message
                    ]
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp API error', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return [
                    'success' => false,
                    'response' => $response->json()
                ];
            }

            return [
                'success' => true,
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'response' => [
                    'error' => [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
    }

    /**
     * Enviar mensaje con plantilla (para primer mensaje)
     *
     * @param string $to Número destino
     * @param string $templateName Nombre de la plantilla
     * @param array $parameters Parámetros para la plantilla
     * @param string $language Código de idioma
     * @return array
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = [], string $language = 'es_AR'): array
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $language
                    ]
                ]
            ];

            // Agregar parámetros si existen
            if (!empty($parameters)) {
                $payload['template']['components'] = [
                    [
                        'type' => 'body',
                        'parameters' => array_map(function ($param) {
                            return ['type' => 'text', 'text' => $param];
                        }, $parameters)
                    ]
                ];
            }

            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", $payload);

            if ($response->failed()) {
                Log::error('WhatsApp Template error', [
                    'to' => $to,
                    'template' => $templateName,
                    'response' => $response->json()
                ]);

                return [
                    'success' => false,
                    'response' => $response->json()
                ];
            }

            return [
                'success' => true,
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Template Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'response' => [
                    'error' => [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
    }

    /**
     * Formatear número de teléfono a formato internacional
     *
     * @param string $phone
     * @return string
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Limpiar todo excepto números
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Si empieza con 0, sacarlo
        if (str_starts_with($clean, '0')) {
            $clean = substr($clean, 1);
        }
        
        // Si es Argentina y no tiene código de país, agregarlo
        if (strlen($clean) === 10 && str_starts_with($clean, '3826')) {
            $clean = '549' . $clean;
        }
        
        // Si tiene 11 dígitos y empieza con 549, bien
        if (strlen($clean) === 11 && str_starts_with($clean, '549')) {
            return $clean;
        }
        
        // Si tiene 12 dígitos y empieza con 549, está bien
        if (strlen($clean) === 12 && str_starts_with($clean, '549')) {
            return $clean;
        }
        
        return $clean;
    }

    /**
     * Verificar la conexión con la API
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withToken($this->token)
                ->get("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}