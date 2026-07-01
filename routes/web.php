<?php

use App\Http\Controllers\ImprimirPedidoController;
use App\Http\Controllers\ReportVentaController;
use App\Http\Controllers\ServicioController;
use App\Livewire\Print\PrintPedido;
use App\Livewire\Print\ReportVentaO;
use App\Livewire\Print\ReporIngreso;
use App\Livewire\Print\StockImprimir;
use App\Livewire\Print\PrintOperacion;
use App\Http\Controllers\Api\Mobile\IngresoMobileController;

use App\Livewire\Service\Comprobante;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\CargarImagenes;
Route::get('/', function () {
    return view('welcome');
});

// ── Descarga de la app móvil ─────────────────────────────────────────
Route::get('/app', function () {
    return view('app.download');
})->name('app.download');

// ── Comprobante público (desde app móvil) ────────────────────────
// hash = sha256(operacion_id . APP_KEY) – sin auth, solo lectura del PDF
Route::get('/comprobante/mobile/{operacion}/{hash}', function ($operacion, $hash) {
    $expected = hash('sha256', $operacion . config('app.key'));
    abort_unless(hash_equals($expected, $hash), 403);
    return app(\App\Livewire\Print\ReportVentaO::class)->generateReport($operacion);
})->name('comprobante.mobile');

// ── Comprobante de ingreso de bici (desde app móvil) ─────────────
// hash = sha256('ingreso' . nro_ingreso . APP_KEY) – sin auth, solo lectura del PDF
Route::get('/comprobante-ingreso/mobile/{nro}/{hash}', function ($nro, $hash) {
    $expected = hash('sha256', 'ingreso' . $nro . config('app.key'));
    abort_unless(hash_equals($expected, $hash), 403);
    return app(\App\Livewire\Print\ReporIngreso::class)->generateReport($nro);
})->name('comprobante.ingreso.mobile');

// ── Acceso por QR desde el celular del mecánico ──────────────────
// URL pública que redirige a la app o muestra una vista mobile-friendly
Route::get('/mobile/ingreso/{token}', function ($token) {
    $nro = \App\Models\NroIngreso::where('token_mobile', $token)->firstOrFail();
    return view('mobile.ingreso-qr', ['nroIngreso' => $nro, 'token' => $token]);
})->name('mobile.ingreso.qr');

// Redirigir /register al login (registro deshabilitado)
Route::redirect('/register', '/login');



// Agrupa todas las rutas que requieren autenticación y verificación
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
   
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Agrupación por prefijo para clientes, artículos, y ventas
    Route::prefix('cliente')->group(function () {
        Route::get('/', fn() => view('cliente.index'))->name('cliente.index');
    });

    Route::prefix('articulo')->group(function () {
        Route::get('/grupo', fn() => view('articulo.articuloGrupo'))->name('articulo.articuloGrupo');
        Route::get('/importar-lista', fn() => view('articulo.importarLista'))->name('articulo.importarLista');
        Route::get('/catalogo', fn() => view('articulo.catalogo'))->name('articulo.catalogo');
        Route::get('/mano-de-obra', fn() => view('articulo.manoDeObra'))->name('articulo.manoDeObra');

    });

    Route::prefix('venta')->group(function () {
        Route::get('/', fn() => view('venta.index'))->name('venta.index');
        Route::get('/list', fn() => view('venta.list'))->name('venta.list');
        Route::get('/cuentacorriente', fn() => view('venta.cuentaCorriente'))->name('venta.cuentaCorriente');
        Route::get('/listcuentacorriente', fn() => view('venta.listCuentaCorriente'))->name('venta.listCuentaCorriente');
        Route::get('/express', fn() => view('venta.ventaExpress'))->name('venta.ventaExpress');
        Route::get('/card', fn() => view('venta.ventaCard'))->name('venta.ventaCard');

    });

    // Agrupación para operaciones, cierre de caja y stock
    Route::prefix('cierre')->group(function () {
        Route::get('/', fn() => view('cierre.cierreCaja'))->name('cierre.cierreCaja');
    });

    Route::prefix('operacion')->group(function () {
        Route::get('/', fn() => view('operacion.index'))->name('operacion.index');
        Route::get('/list', fn() => view('operacion.list'))->name('operacion.list');

        Route::get('/operacion/info-op-imprimir/{datos}',[PrintOperacion::class,'generateReport'])
        ->name('infoOpImprimir');
    });
        






    Route::prefix('stock')->group(function () {
        Route::get('/', fn() => view('stock.index'))->name('stock.index');
        Route::get('/pedido', fn() => view('stock.pedido'))->name('stock.pedido');
        Route::get('/pedido/confirmar', fn() => view('stock.confirmarPedido'))->name('stock.confirmarPedido');
        Route::get('/pedido/pedido/{id}', [PrintPedido::class, 'generateReport'])->name('pedidoImprimir');
        Route::get('/pedidorealizados', fn() => view('stock.pedidoRealizado'))->name('stock.pedidoRealizado');
        Route::get('/stock', [StockImprimir::class, 'generateReport'])->name('stockImprimir');
    });

    // Rutas para reportes y comprobantes
    Route::prefix('report/comprobante')->group(function () {
        Route::get('/reporteVenta/{operacion}/{volver}', [ReportVentaController::class, 'pasar'])->name('venta.reporte');
        Route::get('/reporteVenta/whatsapp/{operacion}/{volver}', [ReportVentaController::class, 'enviarWhatsApp'])->name('venta.reporte.whatsapp');
        Route::get('/{operacion}', [ReportVentaO::class, 'generateReport'])->name('comprobante');
    });

    // Rutas para informes y proveedores
    Route::prefix('informes')->group(function () {
        Route::get('/masvendidos', fn() => view('informes.masVendidos'))->name('informes.masVendidos');
    });

    // Mayorista
    Route::prefix('mayorista')->group(function () {
        Route::get('/', fn() => view('mayorista.index'))->name('mayorista.index');
        Route::get('/clientes', fn() => view('mayorista.clientes'))->name('mayorista.clientes');
        Route::get('/cuenta-corriente', fn() => view('mayorista.cuentaCorriente'))->name('mayorista.cuentaCorriente');
    });

    Route::prefix('proveedor')->group(function () {
        Route::get('/', fn() => view('proveedor.proveedor'))->name('proveedor.proveedor');
        Route::get('/creargrupo', fn() => view('proveedor.crearGrupo'))->name('proveedor.crearGrupo');
        Route::get('/grupoarticulo', fn() => view('proveedor.articuloGrupo'))->name('proveedor.articuloGrupo');
    });

    // Rutas de gestión de precios
    Route::prefix('gestion/precio')->group(function () {
        Route::get('/preciogrupo', fn() => view('gestion.precio.precioGrupo'))->name('gestion.precio.precioGrupo');
        Route::get('/preciocambiar', fn() => view('gestion.precio.precioCambiar'))->name('gestion.precio.precioCambiar');
    });

    // Rutas de ofertas
    Route::prefix('oferta')->group(function () {
        Route::get('/list', fn() => view('oferta.ofertaList'))->name('oferta.ofertaList');
        Route::get('/crear', fn() => view('oferta.ofertaCreate'))->name('oferta.ofertaCreate');
        Route::get('/gestion', fn() => view('oferta.ofertaGestion'))->name('oferta.ofertaGestion');

    }); 
    Route::get('/imagenes/cargar', fn() => view('imagenes.imagenes'))->name('imagenes.imagenes');


    Route::get('/servicio/ingresar', fn() => view('service.ingresarBike'))->name('service.ingresarBike');
    Route::get('/servicio/ingreso-imp/{nro_ingreso}', function($nro_ingreso) { return view('service.ingresoImp', compact('nro_ingreso'));
        })->name('service.ingresoImp');
    Route::get('/servicios/imprimir/ingreso{nro_ingreso}', [ReporIngreso::class, 'generateReport'])->name('imprimirIngreso');
    Route::get('/servicio/egreso', fn() => view('service.egresoBici'))->name('service.egresoBici');
    Route::get('/servicio/egreso/terminar/{nro_ingreso}', function($nro_ingreso) { return view('service.egresoTerminar', compact('nro_ingreso'));
            })->name('service.egresoTerminar');
    Route::get('/servicio/terminar/venta/{nro_ingreso}', function($nro_ingreso) { return view('service.terminarVentaProceso', compact('nro_ingreso'));
            })->name('service.terminarVentaProceso');
        
    
    Route::get('/servicio/calendario', fn() => view('service.calendarioServicios'))->name('service.calendarioServicios');
    Route::get('/servicio/cuenta-mecanico', fn() => view('service.cuentaMecanico'))->name('service.cuentaMecanico');

    Route::get('/gestion/user', function() { return view('admin.gestionUsuario');
            })->name('admin.gestionUsuario');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        // ... otras rutas ...
        
        // Perfil de usuario
    Route::get('/profile', function () {return view('admin.index'); })->name('profile');});


});
