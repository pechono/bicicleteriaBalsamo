<?php

namespace App\Livewire\Stock;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\ListaArticulo;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

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

    // Alta desde catálogo (cuando no está en stock activo pero sí en lista_articulos)
    public $enCatalogo = false;
    public $listaId = null;
    public $aNombre = '';
    public $aCosto = 0;
    public $aPublicoLista = 0;
    public $aStock = 0;
    public $aStockMinimo = 0;
    public $aGrupoId = '';
    public $aCategoriaId = ''; // categoría (default General; editable para ordenar)
    public $aPorcentaje = 0; // % de ganancia del grupo
    public $aIva = 0;        // 21 si el proveedor discrimina IVA, 0 si ya lo incluye
    public $aPrecioVenta = 0;

    public function updatedProveedorId() { $this->limpiarBusqueda(); }
    public function updatedCodigo() { $this->limpiarBusqueda(); }

    private function limpiarBusqueda(): void
    {
        $this->encontrado = false;
        $this->enCatalogo = false;
        $this->artId = $this->nombre = $this->listaId = null;
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
            // No está activo: ¿está en el catálogo (lista importada)? Ofrecer darlo de alta.
            $cat = DB::table('lista_articulos')
                ->where('proveedor_id', $this->proveedor_id)
                ->where('codigo', trim($this->codigo))
                ->first();

            if ($cat) {
                $this->encontrado    = false;
                $this->enCatalogo    = true;
                $this->listaId       = $cat->id;
                $this->aNombre       = $cat->articulo;
                $this->aCosto        = (int) $cat->precio_costo;
                $this->aPublicoLista = (int) ($cat->precio_publico ?: $cat->precio_costo);
                $this->aStock        = 0;
                $this->aStockMinimo  = 0;
                $this->aGrupoId      = '';
                $this->aCategoriaId  = Categoria::firstOrCreate(['categoria' => 'General'])->id;
                $this->aPorcentaje   = 0;
                $ivaIncluido = Proveedor::whereKey($this->proveedor_id)->value('iva_incluido');
                $this->aIva  = $ivaIncluido ? 0 : 21;
                $this->recalcularAltaVenta();
                return;
            }

            $this->encontrado = false;
            $this->enCatalogo = false;
            $this->addError('codigo', 'No se encontró ese código para ese proveedor (ni en stock ni en el catálogo).');
            return;
        }

        $this->encontrado      = true;
        $this->enCatalogo      = false;
        $this->artId           = $row->id;
        $this->nombre          = $row->articulo;
        $this->stockActual     = (int) $row->stock;
        $this->costoActual     = (int) $row->precioI;
        $this->ventaActual     = (int) $row->precioF;
        $this->cantidadRecibida = 0;
        $this->nuevoCosto      = (int) $row->precioI;
    }

    /** Precio de venta = (costo + IVA) + % de ganancia del grupo. */
    private function recalcularAltaVenta(): void
    {
        $conIva = $this->aCosto * (1 + (float) $this->aIva / 100);
        $this->aPrecioVenta = (int) round($conIva * (1 + (float) $this->aPorcentaje / 100));
    }

    public function updatedAGrupoId($value)
    {
        $this->aPorcentaje = $value ? (float) (Grupos::whereKey($value)->value('porsentaje') ?? 0) : 0;
        $this->recalcularAltaVenta();
    }

    public function updatedAPorcentaje() { $this->recalcularAltaVenta(); }
    public function updatedAIva() { $this->recalcularAltaVenta(); }
    public function updatedACosto() { $this->recalcularAltaVenta(); }

    /** Da de alta el ítem del catálogo como artículo activo con stock (promueve). */
    public function darDeAlta()
    {
        if (!$this->enCatalogo || !$this->listaId) {
            $this->addError('codigo', 'Primero buscá un artículo.');
            return;
        }

        $this->validate([
            'aNombre'      => 'required|string|min:2',
            'aCosto'       => 'required|numeric|min:0',
            'aGrupoId'     => 'required|exists:grupos,id',
            'aCategoriaId' => 'required|exists:categorias,id',
            'aPrecioVenta' => 'required|numeric|min:1',
            'aStock'       => 'required|numeric|min:0',
            'aStockMinimo' => 'required|numeric|min:0',
        ], [
            'aNombre.required'      => 'Poné el nombre.',
            'aGrupoId.required'     => 'Elegí el grupo (define la ganancia).',
            'aCategoriaId.required' => 'Elegí la categoría.',
            'aPrecioVenta.required' => 'Poné el precio de venta.',
            'aStock.required'       => 'Poné la cantidad recibida.',
            'aStockMinimo.required' => 'Poné el stock mínimo.',
        ]);

        $row = ListaArticulo::findOrFail($this->listaId);
        $categoriaId = (int) $this->aCategoriaId;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        $abreviatura = Proveedor::whereKey($row->proveedor_id)->value('abreviatura');

        DB::transaction(function () use ($row, $categoriaId, $unidadId, $abreviatura) {
            $articulo = Articulo::create([
                'articulo'     => $this->aNombre,
                'codigo'       => $row->codigo,
                'categoria_id' => $categoriaId,
                'presentacion' => '-',
                'unidad_id'    => $unidadId,
                'descuento'    => 0,
                'unidadVenta'  => 'Unidad',
                'precioF'      => (int) round($this->aPrecioVenta),
                'precioI'      => (int) round($this->aCosto),
                'caducidad'    => 'No',
                'detalles'     => '-',
                'suelto'       => 0,
                'activo'       => 1,
            ]);

            try {
                $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
                $qrImage = (new Writer($renderer))->writeString((string) $articulo->id);
                $fileName = 'qrcodes/articulo_' . $articulo->id . '.svg';
                Storage::disk('public')->put($fileName, $qrImage);
                $articulo->qr_code = $fileName;
                $articulo->save();
            } catch (\Throwable $e) {
                // si falla el QR, el artículo igual queda creado
            }

            Stock::create([
                'articulo_id'      => $articulo->id,
                'proveedor_id'     => $row->proveedor_id,
                'codigo_proveedor' => $abreviatura,
                'stock'            => (int) $this->aStock,
                'stockMinimo'      => (int) $this->aStockMinimo,
            ]);

            HistoriasPrecio::create([
                'articulo_id' => $articulo->id,
                'precioIcial' => (int) round($this->aCosto),
                'precioFinal' => (int) round($this->aPrecioVenta),
            ]);

            GruposArticulos::create(['grupo_id' => $this->aGrupoId, 'articulo_id' => $articulo->id]);

            // Relinkea el catálogo al artículo nuevo (corrige articulo_id previo si estaba mal).
            $row->articulo_id = $articulo->id;
            $row->save();
        });

        $nombre = $this->aNombre;
        $this->reset(['codigo', 'encontrado', 'enCatalogo', 'listaId', 'aNombre', 'aCosto', 'aPublicoLista', 'aStock', 'aStockMinimo', 'aGrupoId', 'aCategoriaId', 'aPorcentaje', 'aIva', 'aPrecioVenta']);
        $this->dispatch('notify', 'Artículo dado de alta con stock ✓', 'success');
        session()->flash('message', "«{$nombre}» dado de alta en stock.");
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
            'grupos' => $this->proveedor_id
                ? Grupos::where('proveedor_id', $this->proveedor_id)->orderBy('NombreGrupo')->get()
                : collect(),
            'categorias' => Categoria::orderBy('categoria')->get(),
        ]);
    }
}
