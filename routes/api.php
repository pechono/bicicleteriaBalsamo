<?php

use App\Models\WhatsAppQueue;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\AuthMobileController;
use App\Http\Controllers\Api\Mobile\ArticuloMobileController;
use App\Http\Controllers\Api\Mobile\IngresoMobileController;
use App\Http\Controllers\Api\Mobile\VentaMobileController;

// ============================================================
// 📱 API MOBILE — App de taller de bicicletas
// ============================================================

// Auth (público)
Route::prefix('mobile')->group(function () {
    Route::post('/login', [AuthMobileController::class, 'login']);

    // Acceso público por token QR (solo lectura del detalle)
    Route::get('/ingresos/token/{token}', [IngresoMobileController::class, 'porToken'])
        ->middleware('auth:sanctum');

    // Rutas protegidas con Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthMobileController::class, 'logout']);

        // Artículos
        Route::get('/articulos/qr/{codigo}',   [ArticuloMobileController::class, 'porQr']);
        Route::get('/articulos/buscar',         [ArticuloMobileController::class, 'buscar']);

        // Ingresos / Bicis en taller
        Route::get('/ingresos',                                [IngresoMobileController::class, 'index']);
        Route::get('/ingresos/{id}',                           [IngresoMobileController::class, 'show']);
        Route::post('/ingresos/{id}/articulos',                [IngresoMobileController::class, 'agregarArticulo']);
        Route::patch('/ingresos/{id}/terminar',                [IngresoMobileController::class, 'terminar']);

        // Mecánicos (para selector al terminar)
        Route::get('/mecanicos',                               [IngresoMobileController::class, 'mecanicos']);

        // Venta (punto de venta)
        Route::get('/venta/articulos',                         [VentaMobileController::class, 'buscarArticulo']);
        Route::get('/venta/clientes',                          [VentaMobileController::class, 'buscarCliente']);
        Route::get('/venta/tipos',                             [VentaMobileController::class, 'tiposVenta']);
        Route::post('/venta/procesar',                         [VentaMobileController::class, 'procesarVenta']);
    });
});

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