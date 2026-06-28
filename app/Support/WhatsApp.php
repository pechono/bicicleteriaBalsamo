<?php

namespace App\Support;

use App\Models\WhatsAppQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsApp
{
    /** Encola un mensaje de texto. Devuelve false si no hay teléfono. */
    public static function encolarTexto(?string $telefono, string $mensaje): bool
    {
        if (empty(trim((string) $telefono))) {
            return false;
        }

        WhatsAppQueue::create([
            'telefono' => $telefono,
            'mensaje'  => $mensaje,
            'enviado'  => false,
        ]);

        return true;
    }

    /**
     * Encola un PDF para enviar por WhatsApp. Guarda los bytes en storage/app/whatsapp
     * y deja la cola apuntando al archivo. Devuelve false si no hay teléfono.
     */
    public static function encolarPdf(?string $telefono, string $pdfBytes, string $filename, string $caption = ''): bool
    {
        if (empty(trim((string) $telefono))) {
            return false;
        }

        $path = 'whatsapp/' . Str::uuid() . '.pdf';
        Storage::disk('local')->put($path, $pdfBytes);

        WhatsAppQueue::create([
            'telefono'       => $telefono,
            'mensaje'        => $caption,
            'archivo'        => $path,
            'nombre_archivo' => $filename,
            'enviado'        => false,
        ]);

        return true;
    }
}
