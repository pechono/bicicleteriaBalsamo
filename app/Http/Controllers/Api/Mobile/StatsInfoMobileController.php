<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsInfoMobileController extends Controller
{
    /**
     * GET /api/mobile/stock-bajo
     * Artículos activos con stock <= stockMinimo, agrupados por proveedor.
     * Excluye categoría 1 (Servicios/MdO).
     */
    public function stockBajo(Request $request)
    {
        $items = Articulo::where('articulos.activo', 1)
            ->where('articulos.categoria_id', '!=', 1)
            ->whereColumn('stocks.stock', '<=', 'stocks.stockMinimo')
            ->join('stocks',      'stocks.articulo_id',  '=', 'articulos.id')
            ->join('proveedors',  'proveedors.id',        '=', 'stocks.proveedor_id')
            ->join('categorias',  'categorias.id',        '=', 'articulos.categoria_id')
            ->select(
                'articulos.id',
                'articulos.articulo',
                'articulos.codigo',
                'stocks.codigo_proveedor',
                'stocks.stock',
                'stocks.stockMinimo',
                'proveedors.id as proveedor_id',
                'proveedors.nombre as proveedor',
                'proveedors.abreviatura',
                'categorias.categoria',
            )
            ->orderBy('proveedors.nombre')
            ->orderBy('articulos.articulo')
            ->get();

        // Agrupar por proveedor
        $porProveedor = $items->groupBy('proveedor_id')->map(function ($grupo) {
            $prov = $grupo->first();
            return [
                'proveedor_id'  => $prov->proveedor_id,
                'proveedor'     => $prov->proveedor,
                'abreviatura'   => $prov->abreviatura,
                'total'         => $grupo->count(),
                'articulos'     => $grupo->map(fn($a) => [
                    'id'               => $a->id,
                    'articulo'         => $a->articulo,
                    'codigo'           => $a->codigo,
                    'codigo_proveedor' => $a->codigo_proveedor,
                    'stock'            => (int) $a->stock,
                    'stockMinimo'      => (int) $a->stockMinimo,
                    'faltante'         => max(0, (int) $a->stockMinimo - (int) $a->stock),
                    'categoria'        => $a->categoria,
                ])->values(),
            ];
        })->values();

        return response()->json([
            'total'         => $items->count(),
            'por_proveedor' => $porProveedor,
        ]);
    }

    /**
     * GET /api/mobile/mas-vendidos?periodo=semana|mes|anio
     * Top 10 artículos vendidos + métricas del período.
     * Solo Admin.
     */
    public function masVendidos(Request $request)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos.'], 403);
        }

        $periodo = $request->input('periodo', 'mes');

        [$inicio, $fin] = match ($periodo) {
            'semana' => [Carbon::now()->startOfWeek(),  Carbon::now()->endOfWeek()],
            'anio'   => [Carbon::now()->startOfYear(),  Carbon::now()->endOfYear()],
            default  => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        // Período anterior para tendencia
        $dias = $inicio->diffInDays($fin) + 1;
        $inicioPrev = $inicio->copy()->subDays($dias);
        $finPrev    = $inicio->copy()->subDay();

        $base = fn($desde, $hasta) =>
            Venta::whereBetween('ventas.created_at', [
                $desde->format('Y-m-d') . ' 00:00:00',
                $hasta->format('Y-m-d') . ' 23:59:59',
            ]);

        // Métricas del período actual
        $q = $base($inicio, $fin);
        $totalIngresos  = (int) round((float) $q->sum(DB::raw('cantidad * precioF')));
        $totalVentas    = $q->distinct('operacion')->count('operacion');
        $totalProductos = (int) $q->sum('cantidad');
        $ticketPromedio = $totalVentas > 0 ? (int) round($totalIngresos / $totalVentas) : 0;

        // Tendencia vs período anterior
        $ingresosPrev = (int) round((float) $base($inicioPrev, $finPrev)->sum(DB::raw('cantidad * precioF')));
        $tendencia = $ingresosPrev > 0
            ? round((($totalIngresos - $ingresosPrev) / $ingresosPrev) * 100, 1)
            : ($totalIngresos > 0 ? 100 : 0);

        // Top 10 artículos
        $top = Venta::select(
                'articulos.id',
                'articulos.articulo',
                'categorias.categoria',
                DB::raw('SUM(ventas.cantidad) as total_cantidad'),
                DB::raw('SUM(ventas.cantidad * ventas.precioF) as total_ingresos'),
            )
            ->join('articulos',  'ventas.articulo_id',       '=', 'articulos.id')
            ->join('categorias', 'articulos.categoria_id',   '=', 'categorias.id')
            ->whereBetween('ventas.created_at', [
                $inicio->format('Y-m-d') . ' 00:00:00',
                $fin->format('Y-m-d')    . ' 23:59:59',
            ])
            ->groupBy('articulos.id', 'articulos.articulo', 'categorias.categoria')
            ->orderByDesc('total_ingresos')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'articulo'       => $r->articulo,
                'categoria'      => $r->categoria,
                'total_cantidad' => (int) $r->total_cantidad,
                'total_ingresos' => (int) round((float) $r->total_ingresos),
            ]);

        return response()->json([
            'periodo'        => $periodo,
            'desde'          => $inicio->format('d/m/Y'),
            'hasta'          => $fin->format('d/m/Y'),
            'total_ingresos' => $totalIngresos,
            'total_ventas'   => $totalVentas,
            'total_productos'=> $totalProductos,
            'ticket_promedio'=> $ticketPromedio,
            'tendencia'      => $tendencia,
            'top'            => $top,
        ]);
    }
}
