<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\Mecanico;
use App\Models\MecanicoItem;
use Carbon\Carbon;

class CuentaMecanico extends Component
{
    public $mecanicoId   = '';
    public $itemDesc     = '';
    public $itemMonto    = '';
    public $confirmCierre = false;
    public $mecanicoACerrar = null;

    public function agregarItem(): void
    {
        $this->validate([
            'mecanicoId' => 'required',
            'itemDesc'   => 'required|string|min:2',
            'itemMonto'  => 'required|numeric|min:0.01',
        ], [
            'mecanicoId.required' => 'Seleccioná un mecánico.',
            'itemDesc.required'   => 'La descripción es obligatoria.',
            'itemMonto.required'  => 'El monto es obligatorio.',
        ]);

        MecanicoItem::create([
            'mecanico_id' => $this->mecanicoId,
            'descripcion' => trim($this->itemDesc),
            'monto'       => $this->itemMonto,
            'pagado'      => false,
        ]);

        $this->itemDesc  = '';
        $this->itemMonto = '';
        $this->dispatch('notify', 'Ítem agregado ✓', 'success');
    }

    public function confirmarCierre(int $mecanicoId): void
    {
        $this->mecanicoACerrar = $mecanicoId;
        $this->confirmCierre   = true;
    }

    public function cerrarSemana(): void
    {
        MecanicoItem::where('mecanico_id', $this->mecanicoACerrar)
            ->where('pagado', false)
            ->update([
                'pagado'       => true,
                'liquidado_en' => now(),
            ]);

        $this->confirmCierre   = false;
        $this->mecanicoACerrar = null;
        $this->dispatch('notify', 'Semana cerrada y marcada como pagada ✓', 'success');
    }

    public function cancelarCierre(): void
    {
        $this->confirmCierre   = false;
        $this->mecanicoACerrar = null;
    }

    public function render()
    {
        $mecanicos = Mecanico::where('activo', true)->get();

        // Para cada mecánico, cargar los ítems pendientes de pago
        $cuentas = $mecanicos->map(function ($mecanico) {
            $items = MecanicoItem::where('mecanico_id', $mecanico->id)
                ->where('pagado', false)
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'mecanico' => $mecanico,
                'items'    => $items,
                'total'    => $items->sum('monto'),
            ];
        })->filter(fn($c) => $c['items']->count() > 0 || $c['mecanico']->id == $this->mecanicoId);

        // Historial de liquidaciones (últimas 5 semanas)
        $historial = MecanicoItem::where('pagado', true)
            ->with('mecanico')
            ->orderBy('liquidado_en', 'desc')
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->liquidado_en)->format('d/m/Y H:i'))
            ->take(5);

        return view('livewire.service.cuenta-mecanico', compact('mecanicos', 'cuentas', 'historial'));
    }
}
