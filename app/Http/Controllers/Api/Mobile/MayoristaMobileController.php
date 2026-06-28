<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\ClienteMayorista;
use App\Models\CuentaCorrienteMayorista;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\VentaMayorista;
use App\Models\VentaMayoristaItem;
use Illuminate\Http\Request;

class MayoristaMobileController extends Controller
{
    /**
     * Módulo mayorista exclusivo del administrador (igual que la web).
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()?->user_type !== 'Admin') {
                return response()->json(['message' => 'Sin permisos. Solo el administrador puede usar el módulo mayorista.'], 403);
            }
            return $next($request);
        });
    }

    /**
     * Misma lógica que VentaMayorista::mapArticulo de la web.
     */
    private function mapArticulo(Articulo $a): array
    {
        $grupoArt    = GruposArticulos::where('articulo_id', $a->id)->first();
        $grupo       = $grupoArt ? Grupos::find($grupoArt->grupo_id) : null;
        $proveedor   = $grupo ? Proveedor::find($grupo->proveedor_id) : null;
        $porcentaje  = $a->porcentaje_mayorista ?? ($grupo?->porsentaje ?? 0);
        $ivaIncluido = $proveedor?->iva_incluido ?? false;
        $precioMay   = $a->calcularPrecioMayorista((float)$porcentaje, (bool)$ivaIncluido);
        $stock       = Stock::where('articulo_id', $a->id)->sum('stock');

        return [
            'articulo_id'      => $a->id,
            'nombre'           => $a->articulo . ($a->presentacion ? ' ' . $a->presentacion : ''),
            'codigo'           => $a->codigo,
            'categoria'        => $a->categoria?->categoria,
            'precio_costo'     => (float)$a->precioI,
            'precio_final'     => (float)$a->precioF,
            'precio_mayorista' => $precioMay,
            'porcentaje'       => (float)$porcentaje,
            'iva_incluido'     => (bool)$ivaIncluido,
            'stock'            => (float)$stock,
            'proveedor'        => $proveedor?->nombre,
            'grupo'            => $grupo?->NombreGrupo,
        ];
    }

    /**
     * GET /api/mobile/mayorista/articulos?q=texto
     */
    public function buscarArticulos(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Join a stocks para buscar también por codigo_proveedor (igual que la web)
        $articulos = Articulo::where('articulos.activo', true)
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where(fn($query) => \App\Support\Busqueda::palabras($query, $q, [
                'articulos.articulo', 'articulos.codigo', 'stocks.codigo_proveedor',
            ]))
            ->select('articulos.*')
            ->with('categoria')
            ->limit(12)
            ->get()
            ->map(fn($a) => $this->mapArticulo($a));

        return response()->json($articulos);
    }

    /**
     * GET /api/mobile/mayorista/clientes?q=texto
     */
    public function clientes(Request $request)
    {
        $q = $request->input('q', '');

        $clientes = ClienteMayorista::when($q, fn($query) =>
                $query->where(fn($sub) =>
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellido', 'like', "%{$q}%")
                        ->orWhere('cuit', 'like', "%{$q}%")
                )
            )
            ->orderBy('apellido')
            ->get();

        return response()->json($clientes);
    }

    /**
     * POST /api/mobile/mayorista/clientes
     */
    public function guardarCliente(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|min:2',
            'apellido'         => 'required|string|min:2',
            'telefono'         => 'nullable|string',
            'email'            => 'nullable|email',
            'cuit'             => 'nullable|string',
            'direccion'        => 'nullable|string',
            'porcentaje_extra' => 'nullable|numeric|min:0',
            'activo'           => 'nullable|boolean',
        ]);
        $data['porcentaje_extra'] = $data['porcentaje_extra'] ?? 0;
        $data['activo'] = $data['activo'] ?? true;

        $cliente = ClienteMayorista::create($data);

        return response()->json(['message' => 'Cliente creado.', 'cliente' => $cliente], 201);
    }

    /**
     * PUT /api/mobile/mayorista/clientes/{id}
     */
    public function actualizarCliente(Request $request, $id)
    {
        $cliente = ClienteMayorista::findOrFail($id);

        $data = $request->validate([
            'nombre'           => 'required|string|min:2',
            'apellido'         => 'required|string|min:2',
            'telefono'         => 'nullable|string',
            'email'            => 'nullable|email',
            'cuit'             => 'nullable|string',
            'direccion'        => 'nullable|string',
            'porcentaje_extra' => 'nullable|numeric|min:0',
            'activo'           => 'nullable|boolean',
        ]);

        $cliente->update($data);

        return response()->json(['message' => 'Cliente actualizado.', 'cliente' => $cliente]);
    }

    /**
     * DELETE /api/mobile/mayorista/clientes/{id} — baja lógica, igual que la web.
     */
    public function eliminarCliente($id)
    {
        ClienteMayorista::findOrFail($id)->update(['activo' => false]);
        return response()->json(['message' => 'Cliente desactivado.']);
    }

    /**
     * POST /api/mobile/mayorista/venta
     * Misma lógica que VentaMayorista::procesarVenta de la web.
     *
     * Body: { cliente_id, tipo_pago, observaciones, items: [{ articulo_id, cantidad, porcentaje }] }
     * El precio mayorista se recalcula en el servidor para evitar manipulación.
     */
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'cliente_id'           => 'required|exists:clientes_mayoristas,id',
            'tipo_pago'            => 'required|in:efectivo,transferencia,cuenta_corriente',
            'observaciones'        => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.articulo_id'  => 'required|exists:articulos,id',
            'items.*.cantidad'     => 'required|numeric|min:0.01',
            'items.*.porcentaje'   => 'nullable|numeric|min:0',
        ]);

        // Recalcular precios en servidor
        $items = [];
        $total = 0;
        foreach ($request->input('items') as $item) {
            $a    = Articulo::findOrFail($item['articulo_id']);
            $base = $this->mapArticulo($a);
            $porcentaje = isset($item['porcentaje']) ? (float)$item['porcentaje'] : $base['porcentaje'];
            $precioMay  = $a->calcularPrecioMayorista($porcentaje, $base['iva_incluido']);

            $items[] = [
                'articulo_id'         => $a->id,
                'cantidad'            => (float)$item['cantidad'],
                'precio_costo'        => (float)$a->precioI,
                'precio_mayorista'    => $precioMay,
                'porcentaje_aplicado' => $porcentaje,
            ];
            $total += $precioMay * (float)$item['cantidad'];
        }

        $venta = VentaMayorista::create([
            'cliente_mayorista_id' => $request->cliente_id,
            'total'         => $total,
            'tipo_pago'     => $request->tipo_pago,
            'pagado'        => $request->tipo_pago !== 'cuenta_corriente',
            'observaciones' => $request->observaciones,
        ]);

        foreach ($items as $item) {
            VentaMayoristaItem::create(array_merge($item, ['venta_mayorista_id' => $venta->id]));
            $st = Stock::where('articulo_id', $item['articulo_id'])->first();
            if ($st) $st->decrement('stock', $item['cantidad']);
        }

        if ($request->tipo_pago === 'cuenta_corriente') {
            CuentaCorrienteMayorista::create([
                'cliente_mayorista_id' => $request->cliente_id,
                'tipo'  => 'venta',
                'monto' => $total,
                'venta_mayorista_id' => $venta->id,
            ]);
        }

        return response()->json([
            'message'  => 'Venta mayorista registrada.',
            'venta_id' => $venta->id,
            'total'    => $total,
        ], 201);
    }

    /**
     * GET /api/mobile/mayorista/cuenta/{clienteId}
     * Movimientos + saldo, igual que CuentaCorrienteMayorista web.
     */
    public function cuentaCorriente($clienteId)
    {
        $cliente = ClienteMayorista::findOrFail($clienteId);

        $movs = CuentaCorrienteMayorista::where('cliente_mayorista_id', $clienteId)
            ->orderByDesc('created_at')
            ->get();

        $movimientos = $movs->map(fn($m) => [
            'id'            => $m->id,
            'tipo'          => $m->tipo,
            'monto'         => (float)$m->monto,
            'observaciones' => $m->observaciones,
            'venta_id'      => $m->venta_mayorista_id,
            'fecha'         => $m->created_at->format('d/m/Y H:i'),
        ]);

        $saldo = $movs->sum(fn($m) => $m->tipo === 'venta' ? $m->monto : -$m->monto);

        return response()->json([
            'cliente'     => ['id' => $cliente->id, 'nombre' => $cliente->nombre . ' ' . $cliente->apellido],
            'saldo'       => $saldo,
            'movimientos' => $movimientos,
        ]);
    }

    /**
     * POST /api/mobile/mayorista/cuenta/{clienteId}/pago
     */
    public function registrarPago(Request $request, $clienteId)
    {
        ClienteMayorista::findOrFail($clienteId);

        $request->validate([
            'monto'         => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:500',
        ]);

        CuentaCorrienteMayorista::create([
            'cliente_mayorista_id' => $clienteId,
            'tipo'          => 'pago',
            'monto'         => $request->monto,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json(['message' => 'Pago registrado.'], 201);
    }
}
