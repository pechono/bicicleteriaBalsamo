<?php

use App\Models\WhatsAppQueue;
use Illuminate\Support\Facades\Route;

// Endpoints que tu PC local va a consumir
Route::prefix('whatsapp')->group(function () {
    
    // Tu PC local consulta este endpoint para obtener mensajes pendientes
    Route::get('/pending', function () {
        return WhatsAppQueue::where('enviado', false)
            ->orderBy('created_at', 'asc')
            ->get(['id', 'telefono', 'mensaje']);
    });
    
    // Tu PC local avisa cuando envió un mensaje
    Route::post('/mark-sent/{id}', function ($id) {
        $message = WhatsAppQueue::findOrFail($id);
        $message->update([
            'enviado' => true,
            'enviado_en' => now()
        ]);
        return response()->json(['success' => true]);
    });
    
    // Tu PC local avisa si hubo error
    Route::post('/mark-error/{id}', function ($id) {
        $message = WhatsAppQueue::findOrFail($id);
        $message->update(['error' => request('error')]);
        return response()->json(['success' => true]);
    });
});