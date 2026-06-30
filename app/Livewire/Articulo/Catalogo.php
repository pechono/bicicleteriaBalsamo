<?php

namespace App\Livewire\Articulo;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\ListaArticulo;
use App\Models\Proveedor;
use App\Models\Grupos;
use App\Models\Stock;
use App\Models\Unidad;
use App\Support\Busqueda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class Catalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';
    public $soloPendientes = false; // mostrar solo los que NO se pasaron a artículos

    // Modal "pasar a artículos"
    public $promoverId = null;
    public $promoverProveedorId = null;
    public $pNombre = '';
    public $pCodigo = '';
    public $pCosto = 0;
    public $pPrecioVenta = 0;
    public $pStock = 0;
    public $pStockMinimo = 0;
    public $pGrupoId = '';

    protected $queryString = ['q' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }
    public function updatingSoloPendientes() { $this->resetPage(); }

    public function abrirPromover($id)
    {
        $row = ListaArticulo::findOrFail($id);
        $this->promoverId         = $row->id;
        $this->promoverProveedorId = $row->proveedor_id;
        $this->pNombre      = $row->articulo;
        $this->pCodigo      = $row->codigo;
        $this->pCosto       = $row->precio_costo;
        $this->pPrecioVenta = $row->precio_publico ?: $row->precio_costo;
        $this->pStock       = 0;
        $this->pStockMinimo = 0;
        $this->pGrupoId     = '';
    }

    public function cerrarPromover()
    {
        $this->reset(['promoverId', 'promoverProveedorId', 'pNombre', 'pCodigo', 'pCosto', 'pPrecioVenta', 'pStock', 'pStockMinimo', 'pGrupoId']);
        $this->resetErrorBag();
    }

    public function confirmarPromover()
    {
        $this->validate([
            'pNombre'      => 'required|string|min:2',
            'pGrupoId'     => 'required|exists:grupos,id',
            'pPrecioVenta' => 'required|numeric|min:1',
            'pStock'       => 'required|numeric|min:0',
            'pStockMinimo' => 'required|numeric|min:0',
        ], [
            'pGrupoId.required'     => 'Elegí el grupo.',
            'pPrecioVenta.required' => 'Poné el precio de venta.',
            'pStock.required'       => 'Poné el stock.',
        ]);

        $row = ListaArticulo::findOrFail($this->promoverId);

        // Si ya estaba pasado, no duplicar.
        if ($row->articulo_id && Articulo::whereKey($row->articulo_id)->exists()) {
            $this->dispatch('notify', 'Ese ítem ya está en artículos', 'warning');
            $this->cerrarPromover();
            return;
        }

        $categoriaId = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        $abreviatura = Proveedor::whereKey($row->proveedor_id)->value('abreviatura');

        DB::transaction(function () use ($row, $categoriaId, $unidadId, $abreviatura) {
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

            // QR (igual que en la carga manual de artículos).
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
        });

        $this->cerrarPromover();
        $this->dispatch('notify', 'Artículo creado con stock ✓', 'success');
        session()->flash('message', 'Artículo pasado a stock correctamente.');
    }

    public function render()
    {
        $items = ListaArticulo::query()
            ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id))
            ->when($this->soloPendientes, fn ($qb) => $qb->whereNull('lista_articulos.articulo_id'))
            ->when(trim($this->q) !== '', fn ($qb) => Busqueda::palabras($qb, $this->q, ['lista_articulos.codigo', 'lista_articulos.articulo']))
            ->select('lista_articulos.*', 'proveedors.abreviatura')
            ->orderBy('lista_articulos.articulo')
            ->paginate(25);

        $proveedores = Proveedor::orderBy('nombre')->get();
        // Grupos del proveedor del ítem que se está pasando a artículos.
        $gruposPromover = $this->promoverProveedorId
            ? Grupos::where('proveedor_id', $this->promoverProveedorId)->orderBy('NombreGrupo')->get()
            : collect();

        return view('livewire.articulo.catalogo', compact('items', 'proveedores', 'gruposPromover'));
    }
}
