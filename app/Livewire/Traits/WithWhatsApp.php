<?php

namespace App\Livewire\Traits;

use App\Models\WhatsAppQueue;
use Illuminate\Support\Facades\Log;

trait WithWhatsApp
{
    protected function notify(string $message, string $type = 'success'): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('notify', $message, $type);
        } elseif (method_exists($this, 'emit')) {
            $this->emit('notify', $message, $type);
        }
    }

    protected function sendWhatsAppMessage(string $to, string $message): bool
    {
        if (empty(trim($to))) {
            Log::warning('WhatsApp: intento de envío sin número');
            $this->notify('El cliente no tiene teléfono registrado', 'warning');
            return false;
        }

        try {
            WhatsAppQueue::create([
                'telefono' => $to,
                'mensaje'  => $message,
                'enviado'  => false,
            ]);

            $this->notify('Mensaje de WhatsApp en cola ✓', 'success');
            Log::info("WhatsApp encolado para {$to}");
            return true;

        } catch (\Exception $e) {
            Log::error('Error al encolar WhatsApp: ' . $e->getMessage());
            $this->notify('Error al preparar el mensaje de WhatsApp', 'error');
            return false;
        }
    }
}
