<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\NroIngreso;
use App\Models\IngresoBici;
use Carbon\Carbon;

class CalendarioServicios extends Component
{
    public string $semanaInicio;
    public bool   $modalAbierto = false;
    public array  $seleccionado = [];

    public function mount(): void
    {
        Carbon::setLocale('es');
        $this->semanaInicio = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }

    public function semanaAnterior(): void
    {
        $this->semanaInicio = Carbon::parse($this->semanaInicio)->subWeek()->format('Y-m-d');
    }

    public function semanaSiguiente(): void
    {
        $this->semanaInicio = Carbon::parse($this->semanaInicio)->addWeek()->format('Y-m-d');
    }

    public function abrirModal(int $nroId): void
    {
        $ingreso = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->join('bicis',      'bicis.id',      '=', 'ingreso_bicis.bici_id')
            ->join('clientes',   'clientes.id',   '=', 'bicis.cliente_id')
            ->join('marcas',     'marcas.id',     '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->where('nro_ingresos.id', $nroId)
            ->select(
                'nro_ingresos.id as nro_id',
                'nro_ingresos.fecha_retiro',
                'nro_ingresos.estado',
                'nro_ingresos.detalles',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.telefono',
                'marcas.marca',
                'bicis.color',
                'tipo_bikes.tipo',
            )
            ->first();

        if (!$ingreso) return;

        $servicios = IngresoBici::join('articulos',  'articulos.id',  '=', 'ingreso_bicis.articulo_id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->where('ingreso_bicis.nro_ingreso', $nroId)
            ->select('articulos.articulo', 'categorias.categoria')
            ->get()
            ->toArray();

        $this->seleccionado = $ingreso->toArray();
        $this->seleccionado['servicios'] = $servicios;
        $this->modalAbierto = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->seleccionado = [];
    }

    public function render()
    {
        Carbon::setLocale('es');

        $inicio = Carbon::parse($this->semanaInicio)->startOfWeek(Carbon::MONDAY);
        $fin    = $inicio->copy()->endOfWeek(Carbon::SUNDAY);

        $ingresos = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->join('bicis',      'bicis.id',      '=', 'ingreso_bicis.bici_id')
            ->join('clientes',   'clientes.id',   '=', 'bicis.cliente_id')
            ->join('marcas',     'marcas.id',     '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->whereNotNull('nro_ingresos.fecha_retiro')
            ->where('nro_ingresos.estado', '!=', 'Terminado')
            ->whereBetween('nro_ingresos.fecha_retiro', [
                $inicio->format('Y-m-d'),
                $fin->copy()->addWeek()->format('Y-m-d'),
            ])
            ->select(
                'nro_ingresos.id as nro_id',
                'nro_ingresos.fecha_retiro',
                'nro_ingresos.estado',
                'nro_ingresos.detalles',
                'clientes.nombre',
                'clientes.apellido',
                'marcas.marca',
                'bicis.color',
                'tipo_bikes.tipo',
            )
            ->groupBy(
                'nro_ingresos.id',
                'nro_ingresos.fecha_retiro',
                'nro_ingresos.estado',
                'nro_ingresos.detalles',
                'clientes.nombre',
                'clientes.apellido',
                'marcas.marca',
                'bicis.color',
                'tipo_bikes.tipo',
            )
            ->orderBy('nro_ingresos.fecha_retiro')
            ->get();

        // Cargar todos los servicios en una sola query (evita N+1)
        $nroIds = $ingresos->pluck('nro_id')->toArray();
        $todosServicios = collect();

        if (!empty($nroIds)) {
            $todosServicios = IngresoBici::join('articulos',  'articulos.id',  '=', 'ingreso_bicis.articulo_id')
                ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->whereIn('ingreso_bicis.nro_ingreso', $nroIds)
                ->select('ingreso_bicis.nro_ingreso as nro_id', 'articulos.articulo', 'categorias.categoria')
                ->get()
                ->groupBy('nro_id');
        }

        foreach ($ingresos as $ingreso) {
            $ingreso->servicios_grilla = $todosServicios->get($ingreso->nro_id, collect());
        }

        $ingresos = $ingresos->groupBy('fecha_retiro');

        $semanas = [];
        foreach ([0, 1] as $semanaOffset) {
            $inicioSemana = $inicio->copy()->addWeeks($semanaOffset);
            $dias = [];
            for ($i = 0; $i < 7; $i++) {
                $dia = $inicioSemana->copy()->addDays($i);
                $key = $dia->format('Y-m-d');
                $dias[] = [
                    'fecha'    => $key,
                    'diaNombre'=> ucfirst($dia->isoFormat('ddd')),
                    'diaNum'   => $dia->format('j'),
                    'esHoy'    => $dia->isToday(),
                    'esPasado' => $dia->isPast() && !$dia->isToday(),
                    'ingresos' => $ingresos->get($key, collect()),
                ];
            }
            $semanas[] = [
                'label' => $semanaOffset === 0 ? 'Esta semana' : 'Próxima semana',
                'rango' => ucfirst($inicioSemana->isoFormat('D [de] MMMM'))
                         . ' — '
                         . ucfirst($inicioSemana->copy()->endOfWeek()->isoFormat('D [de] MMMM [de] YYYY')),
                'dias'  => $dias,
            ];
        }

        return view('livewire.service.calendario-servicios', compact('semanas'));
    }
}
