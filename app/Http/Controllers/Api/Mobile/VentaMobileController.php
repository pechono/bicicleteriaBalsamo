<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Operacion;
use App\Models\Stock;
use App\Models\TipoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaMobileController extends Controller
{
    /**
     * El punto de venta es exclusivo del administrador (igual que la web).
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()?->user_type !== 'Admin') {
                return response()->json(['message' => 'Sin permisos. Solo el administrador puede usar el punto de venta.'], 403);
            }
            return $next($request);
        });
    }

    /**
     * GET /api/mobile/venta/articulos?q=texto
     * Busca artículos activos con stock. Devuelve precio, stock y descuento.
     */
    public function buscarArticulo(Request $request)
    {
        $q = $request->input('q', '');

        $articulos = Articulo::where('articulos.activo', true)
            ->where(function ($query) use ($q) {
                $query->where('articulos.articulo', 'like', "%{$q}%")
                      ->orWhere('articulos.codigo', 'like', "%{$q}%")
                      ->orWhere('categorias.categoria', 'like', "%{$q}%");
            })
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->select(
                'articulos.id',
                'articulos.articulo',
                'articulos.codigo',
                'articulos.presentacion',
                'articulos.precioF',
                'articulos.precioI',
                'articulos.descuento',
                'categorias.categoria',
                'stocks.stock'
            )
            ->limit(30)
            ->get();

        return response()->json($articulos);
    }

    /**
     * GET /api/mobile/venta/clientes?q=texto
     * Busca clientes activos por nombre/apellido/dni.
     */
    public function buscarCliente(Request $request)
    {
        $q = $request->input('q', '');

        $clientes = Cliente::where('activo', true)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('dni', 'like', "%{$q}%");
            })
            ->select('id', 'nombre', 'apellido', 'dni', 'telefono')
            ->orderBy('apellido')
            ->limit(20)
            ->get();

        return response()->json($clientes);
    }

    /**
     * GET /api/mobile/venta/tipos
     * Devuelve los tipos de venta disponibles.
     */
    public function tiposVenta()
    {
        return response()->json(TipoVenta::all(['id', 'tipoVenta']));
    }

    /**
     * POST /api/mobile/venta/procesar
     * Crea la venta, descuenta stock y devuelve el id de la operación.
     *
     * Body:
     * {
     *   "cliente_id": 1,
     *   "tipo_id": 1,
     *   "items": [
     *     { "articulo_id": 5, "cantidad": 2, "descuento": 0 },
     *     ...
     *   ]
     * }
     */
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,id',
            'tipo_id'    => 'required|integer|exists:tipo_ventas,id',
            'items'      => 'required|array|min:1',
            'items.*.articulo_id' => 'required|integer|exists:articulos,id',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.descuento'   => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;

            // Verify stock and calculate total
            foreach ($request->items as $item) {
                $art   = Articulo::findOrFail($item['articulo_id']);
                $stock = Stock::where('articulo_id', $art->id)->firstOrFail();
                $desc  = $item['descuento'] ?? 0;

                if ($stock->stock < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stock insuficiente para \"{$art->articulo}\" (disponible: {$stock->stock})"
                    ], 422);
                }

                $subtotal = ($item['cantidad'] * $art->precioF) * (1 - $desc / 100);
                $total   += $subtotal;
            }

            // Create operacion
            $operacion = Operacion::create([
                'usuario_id'   => $request->user()->id,
                'tipoVenta_id' => $request->tipo_id,
                'cliente_id'   => $request->cliente_id,
                'detalles'     => '-',
                'venta'        => $total,
            ]);

            // Create ventas & decrement stock
            foreach ($request->items as $item) {
                $art  = Articulo::findOrFail($item['articulo_id']);
                $desc = $item['descuento'] ?? 0;

                Venta::create([
                    'articulo_id' => $item['articulo_id'],
                    'cantidad'    => $item['cantidad'],
                    'precioI'     => $art->precioI,
                    'precioF'     => $art->precioF,
                    'descuento'   => $desc,
                    'operacion'   => $operacion->id,
                ]);

                Stock::where('articulo_id', $item['articulo_id'])
                    ->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            return response()->json([
                'operacion_id'  => $operacion->id,
                'total'         => $total,
                'comprobante_url' => url("/comprobante/mobile/{$operacion->id}/" . hash('sha256', $operacion->id . config('app.key'))),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al procesar la venta: ' . $e->getMessage()], 500);
        }
    }
}
