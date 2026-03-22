<?php

namespace App\Livewire\Traits;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

trait WithWhatsApp
{
    /**
     * Enviar mensaje de WhatsApp
     *
     * @param string $telefono
     * @param string $mensaje
     * @return bool
     */
    public function sendWhatsAppMessage(string $telefono, string $mensaje): bool
    {
        try {
            $whatsapp = new WhatsAppService();
            
            // Formatear número
            $telefonoFormateado = $whatsapp->formatPhoneNumber($telefono);
            
            Log::info('Enviando WhatsApp', [
                'original' => $telefono,
                'formateado' => $telefonoFormateado
            ]);
            
            // Enviar mensaje
            $resultado = $whatsapp->sendText($telefonoFormateado, $mensaje);
            
            if ($resultado['success']) {
                Log::info('WhatsApp enviado correctamente');
                return true;
            } else {
                Log::error('Error enviando WhatsApp', $resultado);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Excepción en WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar conexión con WhatsApp
     *
     * @return bool
     */
    public function testWhatsAppConnection(): bool
    {
        try {
            $whatsapp = new WhatsAppService();
            return $whatsapp->testConnection();
        } catch (\Exception $e) {
            Log::error('Error probando conexión WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Formatear número (wrapper del servicio)
     *
     * @param string $telefono
     * @return string
     */
    public function formatPhoneNumber(string $telefono): string
    {
        $whatsapp = new WhatsAppService();
        return $whatsapp->formatPhoneNumber($telefono);
    }
}