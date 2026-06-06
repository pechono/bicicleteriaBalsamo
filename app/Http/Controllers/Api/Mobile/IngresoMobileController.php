<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\NroIngreso;
use App\Models\IngresoBici;
use App\Models\EgresoBici;
use App\Models\Articulo;
use Illuminate\Http\Request;

class IngresoMobileController extends Controller
{
    /**
     * GET /api/mobile/ingresos
     * Lista todos los ingresos con info de bici y cliente.
     * Filtros: ?estado=Pendiente|Terminado|Entregado
     */
    public function index(Request $request)
    {
        $estado = $request->input('estado');

        $query = NroIngreso::with([
            'ingresoBicis.bici.cliente',
            'ingresoBicis.bici.marca',
            'ingresoBicis.bici.tipoBike',
        ])
        ->orderByDesc('created_at');

        if ($estado) {
            $query->where('estado', $estado);
        }

        $ingresos = $query->get()->map(function ($nro) {
            $primerIngreso = $nro->ingresoBicis->first();
            $bici          = $primerIngreso?->bici;
            $cliente       = $bici?->cliente;

            return [
                'id'           => $nro->id,
                'nro_ingreso'  => $nro->id,
                'estado'       => $nro->estado,
                'fecha_retiro' => $nro->fecha_retiro,
                'created_at'   => $nro->created_at,
                'detalles'     => $nro->detalles,
                'token_mobile' => $nro->token_mobile,
                'cliente'      => $cliente ? [
                    'nombre'   => $cliente->nombre . ' ' . $cliente->apellido,
                    'telefono' => $cliente->telefono,
                ] : null,
                'bici'         => $bici ? [
                    'marca'  => $bici->marca?->marca,
                    'tipo'   => $bici->tipoBike?->tipo,
                    'color'  => $bici->color,
                ] : null,
            ];
        });

        return response()->json($ingresos);
    }

    /**
     * GET /api/mobile/ingresos/{id}
     * Detalle de un ingreso: info completa + artículos a realizar + artículos aplicados (egreso).
     * Mecánico: solo ve precioF. Admin: ve precioF y precioI.
     */
    public function show(Request $request, $id)
    {
        $nro = NroIngreso::findOrFail($id);
        $isAdmin = $request->user()->user_type === 'Admin';

        // Bici y cliente
        $bicicleta = \App\Models\Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
            ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->where('ingreso_bicis.nro_ingreso', $id)
            ->select(
                'clientes.nombre', 'clientes.apellido', 'clientes.dni', 'clientes.telefono',
                'tipo_bikes.tipo', 'marcas.marca', 'bicis.color',
                'nro_ingresos.detalles'
            )
            ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
            ->first();

        // Artículos/servicios a realizar (ingreso_bicis)
        $procesos = IngresoBici::where('nro_ingreso', $id)
            ->with('articulo:id,articulo,presentacion,categoria_id', 'articulo.categoria:id,categoria')
            ->get()
            ->map(fn($item) => [
                'id'        => $item->id,
                'articulo'  => $item->articulo?->articulo,
                'presentacion' => $item->articulo?->presentacion,
                'categoria' => $item->articulo?->categoria?->categoria,
                'detalles'  => $item->detalles,
            ]);

        // Artículos aplicados (egreso_bicis) – con precios según rol
        $egresosQuery = EgresoBici::whereHas('ingresoBici', fn($q) => $q->where('nro_ingreso', $id))
            ->with('articulo:id,articulo,presentacion');

        $egresos = $egresosQuery->get()->map(function ($eg) use ($isAdmin) {
            $data = [
                'id'          => $eg->id,
                'articulo'    => $eg->articulo?->articulo,
                'presentacion'=> $eg->articulo?->presentacion,
                'cantidad'    => $eg->cantidad,
                'precio_final'=> $eg->precio_final,
                'detalles'    => $eg->detalles,
            ];
            if ($isAdmin) {
                $data['precio_inicial'] = $eg->precio_inicial;
            }
            return $data;
        });

        return response()->json([
            'id'           => $nro->id,
            'estado'       => $nro->estado,
            'fecha_retiro' => $nro->fecha_retiro,
            'detalles'     => $nro->detalles,
            'token_mobile' => $nro->token_mobile,
            'bicicleta'    => $bicicleta,
            'procesos'     => $procesos,
            'egresos'      => $egresos,
        ]);
    }

    /**
     * GET /api/mobile/ingresos/token/{token}
     * Acceso por QR escaneado (sin login necesario para ver, con login para operar).
     * Devuelve la info del ingreso identificado por su token.
     */
    public function porToken(Request $request, $token)
    {
        $nro = NroIngreso::where('token_mobile', $token)->firstOrFail();
        return $this->show($request, $nro->id);
    }

    /**
     * POST /api/mobile/ingresos/{id}/articulos
     * Agrega un artículo a la lista de trabajos realizados (egreso_bicis).
     * Disponible para Admin y Mecánico.
     */
    public function agregarArticulo(Request $request, $id)
    {
        $nro = NroIngreso::findOrFail($id);

        if ($nro->estado === 'Entregado') {
            return response()->json(['message' => 'No se pueden agregar artículos a un ingreso entregado.'], 422);
        }

        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',
            'cantidad'    => 'required|numeric|min:0.01',
            'detalles'    => 'nullable|string|max:500',
        ]);

        $articulo = Articulo::findOrFail($request->articulo_id);

        // Obtener el primer ingreso_bici asociado a este nro_ingreso
        $ingresoBici = IngresoBici::where('nro_ingreso', $id)->firstOrFail();

        $egreso = EgresoBici::create([
            'ingreso_bici_id' => $ingresoBici->id,
            'articulo_id'     => $request->articulo_id,
            'cantidad'        => $request->cantidad,
            'precio_inicial'  => $articulo->precioI ?? $articulo->precioF,
            'precio_final'    => $articulo->precioF,
            'detalles'        => $request->detalles,
        ]);

        return response()->json([
            'message'  => 'Artículo agregado correctamente.',
            'egreso'   => $egreso,
        ], 201);
    }

    /**
     * PATCH /api/mobile/ingresos/{id}/terminar
     * Marca el ingreso como Terminado. SOLO Admin.
     */
    public function terminar(Request $request, $id)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos. Solo el administrador puede terminar un ingreso.'], 403);
        }

        $nro = NroIngreso::findOrFail($id);

        if ($nro->estado === 'Terminado') {
            return response()->json(['message' => 'El ingreso ya está marcado como Terminado.'], 422);
        }

        if ($nro->estado === 'Entregado') {
            return response()->json(['message' => 'El ingreso ya fue entregado.'], 422);
        }

        $nro->update(['estado' => 'Terminado']);

        return response()->json(['message' => 'Bici marcada como Terminada.', 'estado' => 'Terminado']);
    }
}
