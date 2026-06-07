<?php

namespace App\Livewire\Mayorista;

use Livewire\Component;
use App\Models\ClienteMayorista;
use App\Models\CuentaCorrienteMayorista as CCModel;

class CuentaCorrienteMayorista extends Component
{
    public ?int   $cliente_id = null;
    public string $busquedaCliente = '';
    public array  $resultadosClientes = [];
    public array  $movimientos = [];
    public float  $saldo = 0;

    // Pago
    public bool   $modalPago = false;
    public float  $montoPago = 0;
    public string $observacionesPago = '';

    public function updatedBusquedaCliente(): void
    {
        if (strlen($this->busquedaCliente) < 2) { $this->resultadosClientes = []; return; }
        $this->resultadosClientes = ClienteMayorista::where('activo', true)
            ->where(fn($q) => $q->where('nombre', 'like', "%{$this->busquedaCliente}%")
                                ->orWhere('apellido', 'like', "%{$this->busquedaCliente}%"))
            ->limit(8)->get()
            ->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre . ' ' . $c->apellido])->toArray();
    }

    public function seleccionarCliente(int $id): void
    {
        $this->cliente_id = $id;
        $c = ClienteMayorista::find($id);
        $this->busquedaCliente = $c->nombre . ' ' . $c->apellido;
        $this->resultadosClientes = [];
        $this->cargarMovimientos();
    }

    public function cargarMovimientos(): void
    {
        if (!$this->cliente_id) { $this->movimientos = []; $this->saldo = 0; return; }
        $movs = CCModel::where('cliente_mayorista_id', $this->cliente_id)
            ->with('venta')->orderByDesc('created_at')->get();
        $this->movimientos = $movs->map(fn($m) => [
            'id'          => $m->id,
            'tipo'        => $m->tipo,
            'monto'       => (float)$m->monto,
            'observaciones' => $m->observaciones,
            'venta_id'    => $m->venta_mayorista_id,
            'fecha'       => $m->created_at->format('d/m/Y H:i'),
        ])->toArray();
        // Saldo: ventas suman deuda, pagos restan
        $this->saldo = $movs->sum(fn($m) => $m->tipo === 'venta' ? $m->monto : -$m->monto);
    }

    public function abrirPago(): void
    {
        $this->montoPago = 0;
        $this->observacionesPago = '';
        $this->modalPago = true;
    }

    public function registrarPago(): void
    {
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01',
            'cliente_id' => 'required|integer',
        ]);
        CCModel::create([
            'cliente_mayorista_id' => $this->cliente_id,
            'tipo'        => 'pago',
            'monto'       => $this->montoPago,
            'observaciones' => $this->observacionesPago,
        ]);
        $this->modalPago = false;
        $this->cargarMovimientos();
    }

    public function render()
    {
        return view('livewire.mayorista.cuenta-corriente-mayorista');
    }
}
