<?php

namespace App\Livewire\Stock;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\ListaArticulo;
use App\Models\PedidoCar;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use App\Support\Busqueda;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Pedido a proveedores ARMADO DESDE EL CATÁLOGO (lista_articulos).
 * Ventaja: se ve el precio de costo actualizado y el pedido mínimo (Dal Santo).
 * Alimenta el MISMO carrito (PedidoCar) que usa el pedido normal, así que se
 * confirma y se envía por el flujo de siempre (stock.confirmarPedido).
 * Si un ítem del catálogo NO está en stock, se pregunta y se pasa a stock ahí mismo.
 */
class PedidoCatalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';

    /** Cantidades por fila (clave = id de lista_articulos). */
    public $cantidades = [];

    /** Cantidad que se agregará al carrito una vez pasado a stock. */
    public $cantidadPendiente = 1;

    // ── Modal "pasar a stock" (igual que en Catálogo) ──
    public $promoverId = null;
    public $promoverProveedorId = null;
    public $pNombre = '';
    public $pCodigo = '';
    public $pCosto = 0;
    public $pPublicoLista = 0;
    public $pPrecioVenta = 0;
    public $pStock = 0;
    public $pStockMinimo = 0;
    public $pGrupoId = '';
    public $pCategoriaId = '';
    public $pPorcentaje = 0;
    public $pIva = 0;

    protected $queryString = ['q' => ['except' => ''], 'proveedor_id' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }

    /* ================== CARRITO ================== */

    /** Agrega un ítem del catálogo al pedido. Si no está en stock, abre "pasar a stock". */
    public function agregar($listaId)
    {
        $row = ListaArticulo::find($listaId);
        if (!$row) {
            return;
        }

        $cant = (int) ($this->cantidades[$listaId] ?? $row->pedido_minimo ?? 1);
        if ($cant < 1) {
            $cant = 1;
        }

        if ($row->articulo_id && Articulo::whereKey($row->articulo_id)->exists()) {
            $this->addToCar($row->articulo_id, $cant);
            $this->dispatch('notify', 'Agregado al pedido ✓', 'success');
            return;
        }

        // No está en stock: preguntar y pasar a stock antes de sumarlo al pedido.
        $this->cantidadPendiente = $cant;
        $this->abrirPromover($listaId);
    }

    private function addToCar($articuloId, $cantidad)
    {
        $existing = PedidoCar::where('articulo_id', $articuloId)->first();
        if ($existing) {
            $existing->update(['cantidad' => $cantidad]);
        } else {
            PedidoCar::create(['articulo_id' => $articuloId, 'cantidad' => $cantidad]);
        }
    }

    public function quitarCar($articuloId)
    {
        PedidoCar::where('articulo_id', $articuloId)->delete();
    }

    public function vaciarCarrito()
    {
        PedidoCar::truncate();
    }

    /* ================== PASAR A STOCK ================== */

    public function abrirPromover($id)
    {
        $row = ListaArticulo::findOrFail($id);
        $this->promoverId          = $row->id;
        $this->promoverProveedorId = $row->proveedor_id;
        $this->pNombre       = $row->articulo;
        $this->pCodigo       = $row->codigo;
        $this->pCosto        = $row->precio_costo;
        $this->pPublicoLista = $row->precio_publico ?: $row->precio_costo;
        $this->pPrecioVenta  = $this->pPublicoLista;
        $this->pStock        = 0;
        $this->pStockMinimo  = 0;
        $this->pGrupoId      = '';
        $this->pCategoriaId  = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $this->pPorcentaje   = 0;
        $ivaIncluido = Proveedor::whereKey($row->proveedor_id)->value('iva_incluido');
        $this->pIva  = $ivaIncluido ? 0 : 21;
    }

    private function recalcularVenta(): void
    {
        $conIva = $this->pCosto * (1 + (float) $this->pIva / 100);
        $this->pPrecioVenta = (int) round($conIva * (1 + (float) $this->pPorcentaje / 100));
    }

    public function updatedPGrupoId($value)
    {
        $this->pPorcentaje = $value ? (float) (Grupos::whereKey($value)->value('porsentaje') ?? 0) : 0;
        $this->recalcularVenta();
    }

    public function updatedPPorcentaje() { $this->recalcularVenta(); }
    public function updatedPIva() { $this->recalcularVenta(); }

    public function usarPublico()
    {
        $this->pPrecioVenta = $this->pPublicoLista;
    }

    public function cerrarPromover()
    {
        $this->reset(['promoverId', 'promoverProveedorId', 'pNombre', 'pCodigo', 'pCosto', 'pPublicoLista', 'pPrecioVenta', 'pStock', 'pStockMinimo', 'pGrupoId', 'pCategoriaId', 'pPorcentaje', 'pIva']);
        $this->resetErrorBag();
    }

    public function confirmarPromover()
    {
        $this->validate([
            'pNombre'      => 'required|string|min:2',
            'pGrupoId'     => 'required|exists:grupos,id',
            'pCategoriaId' => 'required|exists:categorias,id',
            'pPrecioVenta' => 'required|numeric|min:1',
            'pStock'       => 'required|numeric|min:0',
            'pStockMinimo' => 'required|numeric|min:0',
        ], [
            'pGrupoId.required'     => 'Elegí el grupo.',
            'pCategoriaId.required' => 'Elegí la categoría.',
            'pPrecioVenta.required' => 'Poné el precio de venta.',
            'pStock.required'       => 'Poné el stock.',
        ]);

        $row = ListaArticulo::findOrFail($this->promoverId);

        // Si ya estaba pasado, no duplicar: solo agregar al pedido.
        if ($row->articulo_id && Articulo::whereKey($row->articulo_id)->exists()) {
            $this->addToCar($row->articulo_id, (int) $this->cantidadPendiente);
            $this->cerrarPromover();
            $this->dispatch('notify', 'Ese ítem ya estaba en stock; lo agregué al pedido', 'success');
            return;
        }

        $categoriaId = (int) $this->pCategoriaId;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        $abreviatura = Proveedor::whereKey($row->proveedor_id)->value('abreviatura');
        $nuevoArticuloId = null;

        DB::transaction(function () use ($row, $categoriaId, $unidadId, $abreviatura, &$nuevoArticuloId) {
            $articulo = Articulo::create([
                'articulo'     => $this->pNombre,
                'codigo'       => $row->codigo,
                'categoria_id' => $categoriaId,
                'presentacion' => '-',
                'unidad_id'    => $unidadId,
                'descuento'    => 0,
                'unidadVenta'  => 'Unidad',
                'precioF'      => (int) round($this->pPrecioVenta),
                'precioI'      => (int) $row->precio_costo,
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
                'stock'            => (int) $this->pStock,
                'stockMinimo'      => (int) $this->pStockMinimo,
            ]);

            HistoriasPrecio::create([
                'articulo_id' => $articulo->id,
                'precioIcial' => (int) $row->precio_costo,
                'precioFinal' => (int) round($this->pPrecioVenta),
            ]);

            GruposArticulos::create(['grupo_id' => $this->pGrupoId, 'articulo_id' => $articulo->id]);

            $row->articulo_id = $articulo->id;
            $row->save();

            $nuevoArticuloId = $articulo->id;
        });

        // Ya está en stock → lo sumo al pedido con la cantidad que venías cargando.
        if ($nuevoArticuloId) {
            $this->addToCar($nuevoArticuloId, (int) $this->cantidadPendiente);
        }

        $this->cerrarPromover();
        $this->dispatch('notify', 'Pasado a stock y agregado al pedido ✓', 'success');
    }

    public function render()
    {
        $items = ListaArticulo::query()
            ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->leftJoin('articulos', 'articulos.id', '=', 'lista_articulos.articulo_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id))
            ->when(trim($this->q) !== '', fn ($qb) => Busqueda::palabras($qb, $this->q, ['lista_articulos.codigo', 'lista_articulos.articulo']))
            ->select('lista_articulos.*', 'proveedors.abreviatura', 'articulos.precioI as costo_actual')
            ->orderBy('lista_articulos.articulo')
            ->paginate(25);

        // Carrito actual (mismo PedidoCar del pedido normal).
        $inTheCar = PedidoCar::select(
            'articulos.id', 'articulos.codigo', 'articulos.articulo',
            'pedido_cars.cantidad', 'articulos.precioI', 'stocks.codigo_proveedor'
        )
            ->join('articulos', 'articulos.id', '=', 'pedido_cars.articulo_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->get();

        $totalCar = 0;
        foreach ($inTheCar as $c) {
            $totalCar += (int) $c->cantidad * (int) $c->precioI;
        }

        $proveedores = Proveedor::orderBy('nombre')->get();
        $gruposPromover = $this->promoverProveedorId
            ? Grupos::where('proveedor_id', $this->promoverProveedorId)->orderBy('NombreGrupo')->get()
            : collect();
        $categorias = Categoria::orderBy('categoria')->get();

        return view('livewire.stock.pedido-catalogo', compact('items', 'inTheCar', 'totalCar', 'proveedores', 'gruposPromover', 'categorias'));
    }
}
