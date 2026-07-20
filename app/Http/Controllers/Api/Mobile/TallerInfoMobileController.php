<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Mecanico;
use App\Models\MecanicoItem;
use App\Models\NroIngreso;
use App\Models\IngresoBici;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TallerInfoMobileController extends Controller
{
    /**
     * GET /api/mobile/mecanico/cuenta
     * Cuenta pendiente del mecánico autenticado (si es mecánico)
     * o de todos los mecánicos activos (si es admin).
     */
    public function cuentaMecanico(Request $request)
    {
        $user    = $request->user();
        $isAdmin = $user->user_type === 'Admin';

        if ($isAdmin) {
            // Admin ve todos los mecánicos con ítems pendientes
            $mecanicos = Mecanico::where('activo', true)->get();

            $cuentas = $mecanicos->map(function ($mec) {
                $items = MecanicoItem::where('mecanico_id', $mec->id)
                    ->where('pagado', false)
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(fn($i) => [
                        'id'          => $i->id,
                        'descripcion' => $i->descripcion,
                        'monto'       => (int) round((float) $i->monto),
                        'fecha'       => $i->created_at?->format('d/m/Y'),
                    ]);

                return [
                    'mecanico_id'   => $mec->id,
                    'nombre'        => $mec->nombre,
                    'items'         => $items,
                    'total'         => (int) round((float) $items->sum('monto')),
                ];
            })->filter(fn($c) => count($c['items']) > 0)->values();

            // Historial de últimas 5 liquidaciones
            $historial = MecanicoItem::where('pagado', true)
                ->with('mecanico:id,nombre')
                ->orderByDesc('liquidado_en')
                ->get()
                ->groupBy(fn($i) => Carbon::parse($i->liquidado_en)->format('d/m/Y H:i'))
                ->take(5)
                ->map(fn($grupo, $fecha) => [
                    'fecha'     => $fecha,
                    'mecanico'  => $grupo->first()->mecanico?->nombre,
                    'total'     => (int) round((float) $grupo->sum('monto')),
                    'cantidad'  => $grupo->count(),
                ])
                ->values();

            return response()->json([
                'cuentas'  => $cuentas,
                'historial'=> $historial,
            ]);
        }

        // Mecánico: busca su propia cuenta por nombre de usuario
        // (los mecánicos se relacionan por nombre; buscar el Mecanico cuyo nombre coincide)
        $mecanico = Mecanico::where('activo', true)
            ->where('nombre', 'like', '%' . $user->name . '%')
            ->first();

        if (!$mecanico) {
            return response()->json([
                'cuentas'  => [],
                'historial'=> [],
                'sin_cuenta' => true,
            ]);
        }

        $items = MecanicoItem::where('mecanico_id', $mecanico->id)
            ->where('pagado', false)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($i) => [
                'id'          => $i->id,
                'descripcion' => $i->descripcion,
                'monto'       => (int) round((float) $i->monto),
                'fecha'       => $i->created_at?->format('d/m/Y'),
            ]);

        return response()->json([
            'cuentas' => [[
                'mecanico_id' => $mecanico->id,
                'nombre'      => $mecanico->nombre,
                'items'       => $items,
                'total'       => (int) round((float) $items->sum('monto')),
            ]],
            'historial' => [],
        ]);
    }

    /**
     * POST /api/mobile/mecanico/cuenta/{mecanicoId}/cerrar
     * Marca todos los ítems pendientes de un mecánico como pagados.
     * SOLO Admin.
     */
    public function cerrarCuenta(Request $request, $mecanicoId)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos.'], 403);
        }

        MecanicoItem::where('mecanico_id', $mecanicoId)
            ->where('pagado', false)
            ->update(['pagado' => true, 'liquidado_en' => now()]);

        return response()->json(['message' => 'Cuenta cerrada y marcada como pagada.']);
    }

    /**
     * GET /api/mobile/calendario?semana=YYYY-MM-DD
     * Ingresos con fecha de retiro para las 2 semanas a partir de la semana indicada.
     * Excluye los ya entregados.
     */
    public function calendario(Request $request)
    {
        Carbon::setLocale('es');

        $semanaParam = $request->input('semana');
        $inicio = $semanaParam
            ? Carbon::parse($semanaParam)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $fin = $inicio->copy()->addWeeks(2)->endOfWeek(Carbon::SUNDAY);

        $ingresos = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->join('bicis',      'bicis.id',      '=', 'ingreso_bicis.bici_id')
            ->join('clientes',   'clientes.id',   '=', 'bicis.cliente_id')
            ->join('marcas',     'marcas.id',     '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->whereNotNull('nro_ingresos.fecha_retiro')
            ->where('nro_ingresos.estado', '!=', 'Entregado')
            ->whereBetween('nro_ingresos.fecha_retiro', [
                $inicio->format('Y-m-d'),
                $fin->format('Y-m-d'),
            ])
            ->select(
                'nro_ingresos.id as nro_id',
                'nro_ingresos.fecha_retiro',
                'nro_ingresos.estado',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.telefono',
                'marcas.marca',
                'bicis.color',
                'tipo_bikes.tipo',
            )
            ->groupBy(
                'nro_ingresos.id', 'nro_ingresos.fecha_retiro', 'nro_ingresos.estado',
                'clientes.nombre', 'clientes.apellido', 'clientes.telefono',
                'marcas.marca', 'bicis.color', 'tipo_bikes.tipo',
            )
            ->orderBy('nro_ingresos.fecha_retiro')
            ->get();

        // Cargar trabajos de cada ingreso en una sola query
        $nroIds = $ingresos->pluck('nro_id')->toArray();
        $serviciosPorIngreso = collect();
        if (!empty($nroIds)) {
            $serviciosPorIngreso = IngresoBici::join('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
                ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->whereIn('ingreso_bicis.nro_ingreso', $nroIds)
                ->select('ingreso_bicis.nro_ingreso as nro_id', 'articulos.articulo', 'categorias.categoria')
                ->get()
                ->groupBy('nro_id');
        }

        // Armar 2 semanas con días
        $semanas = [];
        foreach ([0, 1] as $offset) {
            $inicioSemana = $inicio->copy()->addWeeks($offset);
            $dias = [];
            for ($i = 0; $i < 7; $i++) {
                $dia = $inicioSemana->copy()->addDays($i);
                $key = $dia->format('Y-m-d');
                $ingresosDelDia = $ingresos
                    ->where('fecha_retiro', $key)
                    ->map(fn($ing) => [
                        'nro_id'   => $ing->nro_id,
                        'estado'   => $ing->estado,
                        'cliente'  => "{$ing->apellido}, {$ing->nombre}",
                        'telefono' => $ing->telefono,
                        'bici'     => implode(' ', array_filter([$ing->marca, $ing->tipo, $ing->color])),
                        'trabajos' => $serviciosPorIngreso->get($ing->nro_id, collect())
                            ->pluck('articulo')->toArray(),
                    ])
                    ->values();

                $dias[] = [
                    'fecha'     => $key,
                    'dia_nombre'=> ucfirst($dia->isoFormat('ddd D')),
                    'es_hoy'    => $dia->isToday(),
                    'es_pasado' => $dia->isPast() && !$dia->isToday(),
                    'ingresos'  => $ingresosDelDia,
                ];
            }
            $semanas[] = [
                'label' => $offset === 0 ? 'Esta semana' : 'Próxima semana',
                'rango' => ucfirst($inicioSemana->isoFormat('D [de] MMMM'))
                         . ' – '
                         . ucfirst($inicioSemana->copy()->endOfWeek()->isoFormat('D [de] MMMM')),
                'dias'  => $dias,
            ];
        }

        return response()->json([
            'semana_inicio' => $inicio->format('Y-m-d'),
            'semanas'       => $semanas,
        ]);
    }
}
