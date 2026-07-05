<?php

namespace App\Livewire\Stock;

use App\Models\Articulo;
use App\Models\HistoriasPrecio;
use App\Models\Proveedor;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Actualización manual al recibir una factura en papel: se elige la empresa (proveedor)
 * y el código, y se suma lo recibido al stock y/o se actualiza el precio.
 */
class ActualizarFactura extends Component
{
    public $proveedor_id = '';
    public $codigo = '';

    // Datos del artículo encontrado
    public $encontrado = false;
    public $artId;
    public $nombre;
    public $stockActual = 0;
    public $costoActual = 0;
    public $ventaActual = 0;

    // Campos a actualizar
    public $cantidadRecibida = 0;   // se SUMA al stock
    public $nuevoCosto = 0;
    public $ajustarVenta = true;    // recalcular precio de venta manteniendo el margen

    public function updatedProveedorId() { $this->limpiarBusqueda(); }
    public function updatedCodigo() { $this->limpiarBusqueda(); }

    private function limpiarBusqueda(): void
    {
        $this->encontrado = false;
        $this->artId = $this->nombre = null;
        $this->resetErrorBag();
    }

    public function buscar()
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'codigo'       => 'required|string',
        ], [
            'proveedor_id.required' => 'Elegí la empresa/proveedor.',
            'codigo.required'       => 'Poné el código.',
        ]);

        $row = DB::table('stocks')
            ->join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
            ->where('stocks.proveedor_id', $this->proveedor_id)
            ->where('articulos.codigo', trim($this->codigo))
            ->select('articulos.id', 'articulos.articulo', 'articulos.precioI', 'articulos.precioF', 'stocks.stock')
            ->first();

        if (!$row) {
            $this->encontrado = false;
            $this->addError('codigo', 'No se encontró ese código para ese proveedor.');
            return;
        }

        $this->encontrado      = true;
        $this->artId           = $row->id;
        $this->nombre          = $row->articulo;
        $this->stockActual     = (int) $row->stock;
        $this->costoActual     = (int) $row->precioI;
        $this->ventaActual     = (int) $row->precioF;
        $this->cantidadRecibida = 0;
        $this->nuevoCosto      = (int) $row->precioI;
    }

    public function guardar()
    {
        if (!$this->encontrado || !$this->artId) {
            $this->addError('codigo', 'Primero buscá un artículo.');
            return;
        }

        $this->validate([
            'cantidadRecibida' => 'required|numeric|min:0',
            'nuevoCosto'       => 'required|numeric|min:0',
        ], [
            'cantidadRecibida.required' => 'Poné la cantidad recibida (0 si no sumás stock).',
            'nuevoCosto.required'       => 'Poné el costo.',
        ]);

        $costoNuevo = (int) round($this->nuevoCosto);
        $ventaNueva = $this->ventaActual;
        if ($costoNuevo !== $this->costoActual && $this->ajustarVenta && $this->costoActual > 0) {
            // Mantener el margen: venta nueva = venta vieja × (costo nuevo / costo viejo)
            $ventaNueva = (int) round($this->ventaActual * ($costoNuevo / $this->costoActual));
        }

        DB::transaction(function () use ($costoNuevo, $ventaNueva) {
            if ($this->cantidadRecibida > 0) {
                Stock::where('articulo_id', $this->artId)
                    ->where('proveedor_id', $this->proveedor_id)
                    ->increment('stock', (int) $this->cantidadRecibida);
            }

            if ($costoNuevo !== $this->costoActual) {
                Articulo::where('id', $this->artId)->update([
                    'precioI' => $costoNuevo,
                    'precioF' => $ventaNueva,
                ]);
                HistoriasPrecio::create([
                    'articulo_id' => $this->artId,
                    'precioIcial' => $costoNuevo,
                    'precioFinal' => $ventaNueva,
                ]);
            }
        });

        $msg = [];
        if ($this->cantidadRecibida > 0) $msg[] = "+{$this->cantidadRecibida} al stock";
        if ($costoNuevo !== $this->costoActual) $msg[] = "costo \${$this->costoActual} → \${$costoNuevo}";
        $this->dispatch('notify', 'Actualizado: ' . (implode(' · ', $msg) ?: 'sin cambios'), 'success');
        session()->flash('message', "«{$this->nombre}» actualizado.");

        // Reset para el siguiente
        $this->reset(['codigo', 'encontrado', 'artId', 'nombre', 'stockActual', 'costoActual', 'ventaActual', 'cantidadRecibida', 'nuevoCosto']);
    }

    public function render()
    {
        return view('livewire.stock.actualizar-factura', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }
}
