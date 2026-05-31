<?php

namespace App\Console\Commands;

use App\Models\WhatsAppQueue;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcesarColaWhatsApp extends Command
{
    protected $signature = 'whatsapp:procesar';
    protected $description = 'Envía los mensajes de WhatsApp pendientes en la cola';

    public function handle(WhatsAppService $whatsapp): int
    {
        $pendientes = WhatsAppQueue::where('enviado', false)
            ->whereNull('error')
            ->orWhere(function ($q) {
                // Reintentar los que fallaron hace más de 5 minutos
                $q->where('enviado', false)
                  ->where('updated_at', '<', now()->subMinutes(5));
            })
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        if ($pendientes->isEmpty()) {
            return self::SUCCESS;
        }

        $this->info("Procesando {$pendientes->count()} mensaje(s)...");

        foreach ($pendientes as $item) {
            $resultado = $whatsapp->sendText($item->telefono, $item->mensaje);

            if ($resultado['success']) {
                $item->update([
                    'enviado'    => true,
                    'enviado_en' => now(),
                    'error'      => null,
                ]);
                Log::info("WhatsApp enviado a {$item->telefono}");
            } else {
                $error = $resultado['response']['error'] ?? 'Error desconocido';
                $item->update(['error' => is_array($error) ? json_encode($error) : $error]);
                Log::warning("WhatsApp fallido a {$item->telefono}: " . json_encode($error));
            }
        }

        return self::SUCCESS;
    }
}
