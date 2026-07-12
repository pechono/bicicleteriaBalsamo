<?php

namespace App\Livewire\Traits;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

trait WithWhatsApp
{
    /**
     * Disparar evento según versión de Livewire
     */
    protected function notify(string $message, string $type = 'success'): void
    {
        if (method_exists($this, 'dispatch')) {
            // Livewire v3
            $this->dispatch('notify', $message, $type);
        } elseif (method_exists($this, 'emit')) {
            // Livewire v2
            $this->emit('notify', $message, $type);
        } else {
            // Fallback
            Log::info('Notify: ' . $message);
        }
    }

    /**
     * Enviar mensaje de WhatsApp
     *
     * @param string $to Número de teléfono (ej: 5493826541085)
     * @param string $message Texto del mensaje
     * @return bool
     */
    protected function sendWhatsAppMessage(string $to, string $message): bool
    {
        try {
            // Verificar que el número no esté vacío
            if (empty($to)) {
                Log::warning('Intento de envío sin número de teléfono');
                $this->notify('El cliente no tiene teléfono registrado', 'warning');
                return false;
            }

            // Verificar que el servicio existe
            if (!class_exists(WhatsAppService::class)) {
                Log::error('WhatsAppService no encontrado. Ejecutá: composer dump-autoload');
                $this->notify('Error de configuración de WhatsApp', 'error');
                return false;
            }

            // Obtener el servicio
            $service = app(WhatsAppService::class);
            
            // Formatear número (sacarle +, espacios, etc.)
            if (method_exists($service, 'formatPhoneNumber')) {
                $to = $service->formatPhoneNumber($to);
            } else {
                // Formateo manual básico
                $to = preg_replace('/[^0-9]/', '', $to);
                if (str_starts_with($to, '0')) {
                    $to = '549' . substr($to, 1);
                }
                // Si es argentino y tiene 10 dígitos (sin 9), agregar el 9
                if (strlen($to) === 10 && str_starts_with($to, '3826')) {
                    $to = '549' . $to;
                }
            }

            // Validar formato del número
            if (!preg_match('/^[0-9]{10,15}$/', $to)) {
                Log::error('Formato de número inválido: ' . $to);
                $this->notify('El número de teléfono no tiene formato válido', 'error');
                return false;
            }

            // Enviar mensaje
            $result = $service->sendText($to, $message);

            if (!$result['success']) {
                $errorMsg = $result['response']['error']['message'] ?? 'Error desconocido';
                Log::error('WhatsApp error: ' . $errorMsg);
                $this->notify('No se pudo enviar el WhatsApp: ' . substr($errorMsg, 0, 50), 'warning');
                return false;
            }

            Log::info('✅ WhatsApp enviado a: ' . $to);
            $this->notify('WhatsApp enviado correctamente', 'success');
            return true;

        } catch (\Exception $e) {
            Log::error('💥 Excepción en WhatsApp: ' . $e->getMessage());
            $this->notify('Error al enviar WhatsApp', 'error');
            return false;
        }
    }

    /**
     * Enviar mensaje con plantilla (primer mensaje)
     *
     * @param string $to Número de teléfono
     * @param string $templateName Nombre de la plantilla en Meta
     * @param array $parameters Parámetros para la plantilla
     * @return bool
     */
    protected function sendWhatsAppTemplate(string $to, string $templateName, array $parameters = []): bool
    {
        try {
            if (empty($to)) {
                return false;
            }

            $service = app(WhatsAppService::class);
            
            // Formatear número
            $to = preg_replace('/[^0-9]/', '', $to);
            
            $result = $service->sendTemplate($to, $templateName, $parameters);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error en template WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}