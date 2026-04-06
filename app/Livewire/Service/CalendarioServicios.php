<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\IngresoBici;
use Carbon\Carbon;

class CalendarioServicios extends Component
{
    public $dias = [];

    public function mount()
    {
        $this->generarCalendario();
    }

    public function generarCalendario()
    {
        $servicios = IngresoBici::with(['ingreso.bici.cliente', 'articulo'])
            ->get();

        $calendario = [];

        foreach ($servicios as $servicio) {

            if (!$servicio->fecha_inicio) continue;

            $inicio = Carbon::parse($servicio->fecha_inicio);
            $fin = Carbon::parse($servicio->fecha_fin ?? $servicio->fecha_inicio);

            while ($inicio <= $fin) {

                $dia = $inicio->format('Y-m-d');

                $calendario[$dia][] = $servicio;

                $inicio->addDay();
            }
        }

        ksort($calendario);

        $this->dias = $calendario;
    }

    public function render()
    {
        return view('livewire.service.calendario-servicios');
    }
}