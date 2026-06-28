<?php

namespace App\Console\Commands;

use App\Models\WhatsAppQueue;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcesarColaWhatsApp extends Command
{
    protected $signature = 'whatsapp:procesar';
    protected $description = 'Envía los mensajes de WhatsApp pendientes en la cola';

    public function handle(WhatsAppService $whatsapp): int
    {
        $pendientes = WhatsAppQueue::where(function ($q) {
                $q->where('enviado', false)->orWhereNull('enviado');
            })
            ->whereNull('error')
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        if ($pendientes->isEmpty()) {
            return self::SUCCESS;
        }

        $this->info("Procesando {$pendientes->count()} mensaje(s)...");

        foreach ($pendientes as $item) {
            if ($item->archivo && Storage::disk('local')->exists($item->archivo)) {
                $base64 = base64_encode(Storage::disk('local')->get($item->archivo));
                $resultado = $whatsapp->sendMedia(
                    $item->telefono,
                    $base64,
                    $item->nombre_archivo ?: 'documento.pdf',
                    $item->mensaje ?: ''
                );
            } else {
                $resultado = $whatsapp->sendText($item->telefono, $item->mensaje);
            }

            if ($resultado['success']) {
                if ($item->archivo) {
                    Storage::disk('local')->delete($item->archivo);
                }
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
