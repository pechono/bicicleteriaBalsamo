<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Livewire\Traits\WithWhatsApp;
use App\Models\Articulo;
use App\Models\Bici;
use App\Models\EgresoBici;
use App\Models\Mecanico;
use App\Models\MecanicoItem;
use App\Models\NroEgreso;
use App\Models\NroIngreso;
use App\Models\Operacion;
use App\Models\Stock;
use App\Models\TipoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Terminar y Entregar una bici desde la app — espeja Livewire\Service\EgresoTerminar
 * y TerminarVentaProceso. Solo Admin, igual que la web.
 */
class TallerProcesoMobileController extends Controller
{
    use WithWhatsApp;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()?->user_type !== 'Admin') {
                return response()->json(['message' => 'Sin permisos. Solo el administrador puede terminar o entregar.'], 403);
            }
            return $next($request);
        });
    }

    /**
     * Bici (id + datos del cliente) asociada a un nro de ingreso — misma consulta que la web.
     */
    private function biciDelIngreso($nro)
    {
        return Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
            ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->where('ingreso_bicis.nro_ingreso', $nro)
            ->select(
                'bicis.id', 'clientes.id as cliente_id', 'clientes.nombre', 'clientes.apellido',
                'clientes.telefono', 'marcas.marca', 'tipo_bikes.tipo', 'bicis.color',
                'ingreso_bicis.nro_ingreso'
            )
            ->first();
    }

    /**
     * GET /api/mobile/taller/{nro}/terminar-datos
     * Todo lo necesario para la pantalla de terminar:
     * procesos pedidos, artículos ya aplicados y mecánicos activos.
     */
    public function terminarDatos($nro)
    {
        $ingreso = NroIngreso::findOrFail($nro);

        // Procesos pedidos al ingresar (igual que EgresoTerminar::procesosCargar)
        $procesos = Bici::join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->join('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
            ->where('ingreso_bicis.nro_ingreso', $nro)
            ->select('articulos.id', 'articulos.codigo', 'articulos.articulo', 'articulos.presentacion', 'articulos.precioF')
            ->get();

        // Artículos ya aplicados (igual que Egreso::mostrarProcesosTerminado)
        $aplicados = EgresoBici::join('articulos', 'articulos.id', '=', 'egreso_bicis.articulo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'egreso_bicis.ingreso_bici_id')
            ->where('ingreso_bicis.nro_ingreso', $nro)
            ->select(
                'egreso_bicis.id', 'articulos.articulo', 'articulos.presentacion',
                'egreso_bicis.cantidad', 'egreso_bicis.precio_final'
            )
            ->distinct()
            ->get();

        $mecanicos = Mecanico::where('activo', true)->select('id', 'nombre')->orderBy('nombre')->get();

        return response()->json([
            'estado'    => $ingreso->estado,
            'detalles'  => $ingreso->detalles,
            'procesos'  => $procesos,
            'aplicados' => $aplicados,
            'mecanicos' => $mecanicos,
        ]);
    }

    /**
     * POST /api/mobile/taller/{nro}/terminar
     * Igual que EgresoTerminar::ConfirmarVenta de la web.
     *
     * Body: {
     *   mecanico_id,
     *   items:          [{ articulo_id, cantidad, descuento }],
     *   mecanico_items: [{ descripcion, monto }]
     * }
     */
    public function terminar(Request $request, $nro)
    {
        $ingreso = NroIngreso::findOrFail($nro);

        if ($ingreso->estado === 'Terminado') {
            return response()->json(['message' => 'El ingreso ya está marcado como Terminado.'], 422);
        }
        if ($ingreso->estado === 'Entregado') {
            return response()->json(['message' => 'El ingreso ya fue entregado.'], 422);
        }

        $request->validate([
            'mecanico_id'                 => 'required|exists:mecanicos,id',
            'items'                       => 'required|array|min:1',
            'items.*.articulo_id'         => 'required|exists:articulos,id',
            'items.*.cantidad'            => 'required|numeric|min:0.01',
            'items.*.descuento'           => 'nullable|numeric|min:0|max:100',
            'mecanico_items'              => 'nullable|array',
            'mecanico_items.*.descripcion'=> 'required_with:mecanico_items|string|max:255',
            'mecanico_items.*.monto'      => 'required_with:mecanico_items|numeric|min:0',
        ]);

        $bici = $this->biciDelIngreso($nro);
        if (!$bici) {
            return response()->json(['message' => 'No se encontró la bici de este ingreso.'], 404);
        }

        DB::beginTransaction();
        try {
            // Verificar stock y calcular total (misma fórmula que EgresoTerminar::Total)
            $total = 0;
            foreach ($request->items as $item) {
                $art   = Articulo::findOrFail($item['articulo_id']);
                $stock = Stock::where('articulo_id', $art->id)->first();
                $desc  = $item['descuento'] ?? 0;

                if ($stock && $stock->stock < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stock insuficiente para \"{$art->articulo}\" (disponible: {$stock->stock})"
                    ], 422);
                }

                $total += ($item['cantidad'] * $art->precioF) * (1 - $desc / 100);
            }

            // NroEgreso con el monto y el mecánico (igual que la web)
            $egresoNro = NroEgreso::create([
                'monto'       => $total,
                'detalles'    => '-',
                'mecanico_id' => $request->mecanico_id,
            ]);

            // EgresoBici por cada ítem + descuento de stock
            // (la web guarda bicis.id en ingreso_bici_id — se replica igual)
            foreach ($request->items as $item) {
                $art = Articulo::findOrFail($item['articulo_id']);

                EgresoBici::create([
                    'ingreso_bici_id' => $bici->id,
                    'articulo_id'     => $item['articulo_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_inicial'  => $art->precioI,
                    'precio_final'    => $art->precioF,
                    'nro_egreso'      => $egresoNro->id,
                ]);

                Stock::where('articulo_id', $item['articulo_id'])
                    ->decrement('stock', $item['cantidad']);
            }

            // Ítems del mecánico (descripcion + monto)
            foreach ($request->input('mecanico_items', []) as $item) {
                MecanicoItem::create([
                    'mecanico_id'   => $request->mecanico_id,
                    'descripcion'   => $item['descripcion'],
                    'monto'         => $item['monto'],
                    'nro_egreso_id' => $egresoNro->id,
                    'pagado'        => false,
                ]);
            }

            $ingreso->update(['estado' => 'Terminado']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al terminar: ' . $e->getMessage()], 500);
        }

        // WhatsApp "lista para retirar" (mismo mensaje que la web)
        if ($bici->telefono) {
            $nombre        = $bici->nombre;
            $nroFormateado = str_pad($nro, 4, '0', STR_PAD_LEFT);
            $marca         = $bici->marca ?? '';
            $color         = $bici->color ?? '';
            $this->sendWhatsAppMessage(
                $bici->telefono,
                "🔧 *BICICLETERÍA BALSAMO* 🔧\n----------------------------\nHola {$nombre}! 🎉\nTu bicicleta *#{$nroFormateado}* ya está lista\npara retirar en nuestro local.\n\n🚲 {$marca} | {$color}\n----------------------------\n⚠️ *Importante:*\nLa bici puede permanecer en el taller\nhasta *7 días* sin cargo adicional.\nPasado ese plazo se cobrará recargo\npor almacenamiento.\n\nEl local no se responsabiliza por daños\nocasionados por el clima, ni por robo o hurto.\n----------------------------\n¡Te esperamos! 📍"
            );
        }

        return response()->json([
            'message' => 'Bici marcada como Terminada.',
            'estado'  => 'Terminado',
            'total'   => $total,
        ]);
    }

    /**
     * GET /api/mobile/taller/{nro}/entrega-datos
     * Ítems fijos (lo cargado al terminar) + tipos de venta + cliente.
     * Igual que el mount de TerminarVentaProceso.
     */
    public function entregaDatos($nro)
    {
        $ingreso = NroIngreso::findOrFail($nro);
        $bici    = $this->biciDelIngreso($nro);

        $fijos = EgresoBici::join('articulos', 'articulos.id', '=', 'egreso_bicis.articulo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'egreso_bicis.ingreso_bici_id')
            ->where('ingreso_bicis.nro_ingreso', $nro)
            ->select(
                'articulos.id as articulo_id', 'articulos.articulo', 'articulos.presentacion',
                'articulos.precioF', 'egreso_bicis.cantidad'
            )
            ->distinct()
            ->get();

        return response()->json([
            'estado'  => $ingreso->estado,
            'cliente' => $bici ? [
                'id'     => $bici->cliente_id,
                'nombre' => $bici->nombre . ' ' . $bici->apellido,
            ] : null,
            'fijos'       => $fijos,
            'tipos_venta' => TipoVenta::all(['id', 'tipoVenta']),
        ]);
    }

    /**
     * POST /api/mobile/taller/{nro}/entrega
     * Igual que TerminarVentaProceso::ConfirmarVenta de la web.
     *
     * Body: {
     *   tipo_id,
     *   items: [{ articulo_id, cantidad, descuento, fijo }]
     * }
     * Los ítems "fijo" no vuelven a descontar stock (ya se descontó al terminar).
     */
    public function entrega(Request $request, $nro)
    {
        $ingreso = NroIngreso::findOrFail($nro);

        if ($ingreso->estado === 'Entregado') {
            return response()->json(['message' => 'El ingreso ya fue entregado.'], 422);
        }
        if ($ingreso->estado !== 'Terminado') {
            return response()->json(['message' => 'La bici debe estar Terminada antes de entregarse.'], 422);
        }

        $request->validate([
            'tipo_id'             => 'required|integer|exists:tipo_ventas,id',
            'items'               => 'required|array|min:1',
            'items.*.articulo_id' => 'required|exists:articulos,id',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.descuento'   => 'nullable|numeric|min:0|max:100',
            'items.*.fijo'        => 'nullable|boolean',
        ]);

        $bici = $this->biciDelIngreso($nro);
        if (!$bici) {
            return response()->json(['message' => 'No se encontró la bici de este ingreso.'], 404);
        }

        DB::beginTransaction();
        try {
            // Total con la misma fórmula de la web
            $total = 0;
            foreach ($request->items as $item) {
                $art  = Articulo::findOrFail($item['articulo_id']);
                $desc = $item['descuento'] ?? 0;
                $total += ($item['cantidad'] * $art->precioF) * (1 - $desc / 100);

                // Stock solo se valida para los ítems nuevos (no fijos)
                if (empty($item['fijo'])) {
                    $stock = Stock::where('articulo_id', $art->id)->first();
                    if ($stock && $stock->stock < $item['cantidad']) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Stock insuficiente para \"{$art->articulo}\" (disponible: {$stock->stock})"
                        ], 422);
                    }
                }
            }

            // tipo_id 5 = cuenta corriente: operación y ventas en 0 (igual que la web)
            $esCtaCte = (int)$request->tipo_id === 5;

            $operacion = Operacion::create([
                'usuario_id'   => $request->user()->id,
                'tipoVenta_id' => $request->tipo_id,
                'cliente_id'   => $bici->cliente_id,
                'detalles'     => '-',
                'venta'        => $esCtaCte ? 0 : $total,
            ]);

            foreach ($request->items as $item) {
                $art  = Articulo::findOrFail($item['articulo_id']);
                $desc = $item['descuento'] ?? 0;

                Venta::create([
                    'articulo_id' => $item['articulo_id'],
                    'cantidad'    => $item['cantidad'],
                    'precioI'     => $esCtaCte ? 0 : $art->precioI,
                    'precioF'     => $esCtaCte ? 0 : $art->precioF,
                    'descuento'   => $desc,
                    'operacion'   => $operacion->id,
                ]);

                // Solo los ítems nuevos descuentan stock (los fijos ya lo hicieron al terminar)
                if (empty($item['fijo'])) {
                    Stock::where('articulo_id', $item['articulo_id'])
                        ->decrement('stock', $item['cantidad']);
                }
            }

            // Vincular los egresos del ingreso con la operación (mismo update que la web)
            NroEgreso::join('egreso_bicis', 'egreso_bicis.nro_egreso', '=', 'nro_egresos.id')
                ->join('ingreso_bicis', 'ingreso_bicis.id', '=', 'egreso_bicis.ingreso_bici_id')
                ->where('ingreso_bicis.nro_ingreso', $nro)
                ->update(['nro_egresos.operacion' => $operacion->id]);

            $ingreso->update(['estado' => 'Entregado']);

            DB::commit();

            return response()->json([
                'message'         => 'Bici entregada.',
                'estado'          => 'Entregado',
                'operacion_id'    => $operacion->id,
                'total'           => $total,
                'comprobante_url' => url("/comprobante/mobile/{$operacion->id}/" . hash('sha256', $operacion->id . config('app.key'))),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al entregar: ' . $e->getMessage()], 500);
        }
    }
}
