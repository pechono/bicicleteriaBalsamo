<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NroIngreso;
use App\Models\Operacion;
use App\Models\NroEgreso;
use App\Models\Stock;
use App\Models\MecanicoItem;
use App\Models\Mecanico;
use App\Models\Bici;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardPanel extends Component
{
    public function render()
    {
        Carbon::setLocale('es');
        $hoy = Carbon::today();

        // ── TARJETAS ─────────────────────────────────────────
        $bikesPendientes = NroIngreso::where('estado', 'Pendiente')->count();

        $bikesTerminadas = NroIngreso::where('estado', 'Terminado')->count();

        $bikesVencidas = NroIngreso::whereNotIn('estado', ['Entregado'])
            ->whereNotNull('fecha_retiro')
            ->where('fecha_retiro', '<', $hoy)
            ->count();

        $ventasHoy = Operacion::whereDate('created_at', $hoy)->sum('venta')
                   + NroEgreso::whereDate('created_at', $hoy)->sum('monto');

        // ── BICIS A RETIRAR HOY Y MAÑANA ─────────────────────
        $bikesHoy = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->join('bicis',      'bicis.id',      '=', 'ingreso_bicis.bici_id')
            ->join('clientes',   'clientes.id',   '=', 'bicis.cliente_id')
            ->join('marcas',     'marcas.id',     '=', 'bicis.marca_id')
            ->whereDate('nro_ingresos.fecha_retiro', $hoy)
            ->whereNotIn('nro_ingresos.estado', ['Entregado'])
            ->select(
                'nro_ingresos.id as nro_id',
                'nro_ingresos.estado',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.telefono',
                'marcas.marca',
                'bicis.color',
            )
            ->groupBy('nro_ingresos.id','nro_ingresos.estado','clientes.nombre','clientes.apellido','clientes.telefono','marcas.marca','bicis.color')
            ->get();

        $bikesMañana = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->join('bicis',      'bicis.id',      '=', 'ingreso_bicis.bici_id')
            ->join('clientes',   'clientes.id',   '=', 'bicis.cliente_id')
            ->join('marcas',     'marcas.id',     '=', 'bicis.marca_id')
            ->whereDate('nro_ingresos.fecha_retiro', $hoy->copy()->addDay())
            ->whereNotIn('nro_ingresos.estado', ['Entregado'])
            ->select(
                'nro_ingresos.id as nro_id',
                'nro_ingresos.estado',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.telefono',
                'marcas.marca',
                'bicis.color',
            )
            ->groupBy('nro_ingresos.id','nro_ingresos.estado','clientes.nombre','clientes.apellido','clientes.telefono','marcas.marca','bicis.color')
            ->get();

        // ── ALERTAS STOCK BAJO ────────────────────────────────
        $stockBajo = Stock::join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
            ->whereRaw('stocks.stock <= stocks.stockMinimo')
            ->where('articulos.activo', 1)
            ->select('articulos.articulo', 'stocks.stock', 'stocks.stockMinimo')
            ->orderBy('stocks.stock')
            ->limit(8)
            ->get();

        // ── CUENTA MECÁNICO (semana actual) ───────────────────
        $cuentaMecanico = Mecanico::where('activo', true)
            ->get()
            ->map(function ($mec) {
                $total = MecanicoItem::where('mecanico_id', $mec->id)
                    ->where('pagado', false)
                    ->sum('monto');
                return ['nombre' => $mec->nombre, 'total' => $total];
            })
            ->filter(fn($m) => $m['total'] > 0);

        return view('livewire.dashboard-panel', compact(
            'bikesPendientes', 'bikesTerminadas', 'bikesVencidas', 'ventasHoy',
            'bikesHoy', 'bikesMañana', 'stockBajo', 'cuentaMecanico'
        ));
    }
}
