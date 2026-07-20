<?php

use App\Models\WhatsAppQueue;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\AuthMobileController;
use App\Http\Controllers\Api\Mobile\ArticuloMobileController;
use App\Http\Controllers\Api\Mobile\IngresoMobileController;
use App\Http\Controllers\Api\Mobile\VentaMobileController;
use App\Http\Controllers\Api\Mobile\MayoristaMobileController;
use App\Http\Controllers\Api\Mobile\IngresoAltaMobileController;
use App\Http\Controllers\Api\Mobile\TallerProcesoMobileController;
use App\Http\Controllers\Api\Mobile\CierreMobileController;
use App\Http\Controllers\Api\Mobile\ClienteMobileController;
use App\Http\Controllers\Api\Mobile\TallerInfoMobileController;

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
        Route::get('/articulos',                [ArticuloMobileController::class, 'index']);
        Route::get('/articulos/qr/{codigo}',   [ArticuloMobileController::class, 'porQr']);
        Route::get('/articulos/buscar',         [ArticuloMobileController::class, 'buscar']);
        Route::patch('/articulos/{id}/stock',   [ArticuloMobileController::class, 'actualizarStock']); // solo Admin
        Route::get('/categorias',               [ArticuloMobileController::class, 'categorias']);
        Route::get('/proveedores',              [ArticuloMobileController::class, 'proveedores']);

        // Ingresos / Bicis en taller
        Route::get('/ingresos',                                [IngresoMobileController::class, 'index']);
        Route::get('/ingresos/{id}',                           [IngresoMobileController::class, 'show']);
        Route::post('/ingresos/{id}/articulos',                [IngresoMobileController::class, 'agregarArticulo']);
        Route::patch('/ingresos/{id}/terminar',                [IngresoMobileController::class, 'terminar']);

        // Mecánicos (para selector al terminar)
        Route::get('/mecanicos',                               [IngresoMobileController::class, 'mecanicos']);

        // Alta de ingreso de bici (espeja IngresarBike + IngresoImp de la web)
        Route::get('/ingreso-bici/cliente',                    [IngresoAltaMobileController::class, 'buscarCliente']);
        Route::post('/ingreso-bici/cliente',                   [IngresoAltaMobileController::class, 'crearCliente']);
        Route::get('/ingreso-bici/datos',                      [IngresoAltaMobileController::class, 'datos']);
        Route::post('/ingreso-bici/marca',                     [IngresoAltaMobileController::class, 'crearMarca']);
        Route::post('/ingreso-bici/tipo',                      [IngresoAltaMobileController::class, 'crearTipo']);
        Route::post('/ingreso-bici/color',                     [IngresoAltaMobileController::class, 'crearColor']);
        Route::get('/ingreso-bici/procesos',                   [IngresoAltaMobileController::class, 'procesos']);
        Route::post('/ingreso-bici',                           [IngresoAltaMobileController::class, 'guardarIngreso']);
        Route::post('/ingreso-bici/{id}/notificar',            [IngresoAltaMobileController::class, 'notificar']);

        // Terminar y entregar (espeja EgresoTerminar + TerminarVentaProceso) — solo Admin
        Route::get('/taller/{nro}/terminar-datos',             [TallerProcesoMobileController::class, 'terminarDatos']);
        Route::post('/taller/{nro}/terminar',                  [TallerProcesoMobileController::class, 'terminar']);
        Route::get('/taller/{nro}/entrega-datos',              [TallerProcesoMobileController::class, 'entregaDatos']);
        Route::post('/taller/{nro}/entrega',                   [TallerProcesoMobileController::class, 'entrega']);

        // Venta (punto de venta) — solo Admin (validado en el controller)
        Route::get('/venta/articulos',                         [VentaMobileController::class, 'buscarArticulo']);
        Route::get('/venta/clientes',                          [VentaMobileController::class, 'buscarCliente']);
        Route::get('/venta/tipos',                             [VentaMobileController::class, 'tiposVenta']);
        Route::post('/venta/procesar',                         [VentaMobileController::class, 'procesarVenta']);

        // Cuenta Mecánico y Calendario
        Route::get('/mecanico/cuenta',                         [TallerInfoMobileController::class, 'cuentaMecanico']);
        Route::post('/mecanico/cuenta/{mecanicoId}/cerrar',    [TallerInfoMobileController::class, 'cerrarCuenta']);
        Route::get('/calendario',                              [TallerInfoMobileController::class, 'calendario']);

        // Cierre de Caja — Admin
        Route::get('/cierre/resumen',  [CierreMobileController::class, 'resumen']);
        Route::post('/cierre',         [CierreMobileController::class, 'cerrar']);

        // Cuenta corriente minorista
        Route::get('/clientes',                        [ClienteMobileController::class, 'buscar']);
        Route::get('/clientes/{id}/cuenta',            [ClienteMobileController::class, 'cuenta']);
        Route::post('/clientes/{id}/pago',             [ClienteMobileController::class, 'registrarPago']);

        // Historial de ventas
        Route::get('/ventas',                          [CierreMobileController::class, 'historial']);

        // Mayorista — solo Admin (validado en el controller)
        Route::get('/mayorista/articulos',                     [MayoristaMobileController::class, 'buscarArticulos']);
        Route::get('/mayorista/clientes',                      [MayoristaMobileController::class, 'clientes']);
        Route::post('/mayorista/clientes',                     [MayoristaMobileController::class, 'guardarCliente']);
        Route::put('/mayorista/clientes/{id}',                 [MayoristaMobileController::class, 'actualizarCliente']);
        Route::delete('/mayorista/clientes/{id}',              [MayoristaMobileController::class, 'eliminarCliente']);
        Route::post('/mayorista/venta',                        [MayoristaMobileController::class, 'procesarVenta']);
        Route::get('/mayorista/cuenta/{clienteId}',            [MayoristaMobileController::class, 'cuentaCorriente']);
        Route::post('/mayorista/cuenta/{clienteId}/pago',      [MayoristaMobileController::class, 'registrarPago']);
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