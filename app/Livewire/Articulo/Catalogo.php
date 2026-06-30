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

    public $nuevaCotizacion = ''; // recalcular precios en USD

    // Modal "pasar a artículos"
    public $promoverId = null;
    public $promoverProveedorId = null;
    public $pNombre = '';
    public $pCodigo = '';
    public $pCosto = 0;
    public $pPublicoLista = 0; // público que trae la lista (referencia)
    public $pPrecioVenta = 0;
    public $pStock = 0;
    public $pStockMinimo = 0;
    public $pGrupoId = '';
    public $pPorcentaje = 0; // % del grupo elegido (informativo)

    protected $queryString = ['q' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }
    public function updatingSoloPendientes() { $this->resetPage(); }

    public function abrirPromover($id)
    {
        $row = ListaArticulo::findOrFail($id);
        $this->promoverId         = $row->id;
        $this->promoverProveedorId = $row->proveedor_id;
        $this->pNombre       = $row->articulo;
        $this->pCodigo       = $row->codigo;
        $this->pCosto        = $row->precio_costo;
        $this->pPublicoLista = $row->precio_publico ?: $row->precio_costo;
        $this->pPrecioVenta  = $this->pPublicoLista;
        $this->pStock        = 0;
        $this->pStockMinimo  = 0;
        $this->pGrupoId      = '';
        $this->pPorcentaje   = 0;
    }

    /** Al elegir el grupo, sugiere el precio de venta = costo + % del grupo (estilo Dal Santo). */
    public function updatedPGrupoId($value)
    {
        if (!$value) {
            $this->pPorcentaje = 0;
            return;
        }
        $this->pPorcentaje = (float) (Grupos::whereKey($value)->value('porsentaje') ?? 0);
        $this->pPrecioVenta = (int) round($this->pCosto * (1 + $this->pPorcentaje / 100));
    }

    /** Si editás el porcentaje a mano, recalcula el precio de venta = costo + %. */
    public function updatedPPorcentaje($value)
    {
        $this->pPrecioVenta = (int) round($this->pCosto * (1 + (float) $value / 100));
    }

    /** Botón "usar público": toma el precio público que vino en la lista. */
    public function usarPublico()
    {
        $this->pPrecioVenta = $this->pPublicoLista;
    }

    public function cerrarPromover()
    {
        $this->reset(['promoverId', 'promoverProveedorId', 'pNombre', 'pCodigo', 'pCosto', 'pPublicoLista', 'pPrecioVenta', 'pStock', 'pStockMinimo', 'pGrupoId', 'pPorcentaje']);
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

    /**
     * Recalcula los precios en pesos de los ítems en USD usando una nueva cotización.
     * Exacto: usa el USD original guardado por fila. Afecta al proveedor filtrado (o a todos).
     */
    public function recalcularCotizacion()
    {
        $this->validate(
            ['nuevaCotizacion' => 'required|numeric|min:1'],
            ['nuevaCotizacion.required' => 'Poné la nueva cotización del dólar.']
        );

        $rate = (float) $this->nuevaCotizacion;

        $q = ListaArticulo::where('moneda', 'USD')->whereNotNull('costo_usd')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('proveedor_id', $this->proveedor_id));

        $afectados = (clone $q)->count();
        $q->update([
            'precio_costo'   => DB::raw("ROUND(costo_usd * {$rate})"),
            'precio_publico' => DB::raw("ROUND(COALESCE(publico_usd, costo_usd) * {$rate})"),
            'cotizacion'     => $rate,
            'updated_at'     => now(),
        ]);

        $this->nuevaCotizacion = '';
        $this->dispatch('notify', "Cotización actualizada en {$afectados} ítems", 'success');
        session()->flash('message', "Recalculé {$afectados} ítem(s) en dólares con cotización \${$rate}.");
    }

    /**
     * Actualiza el costo (precioI) de los artículos en stock con el costo nuevo del catálogo,
     * y ajusta el precio de venta (precioF) manteniendo el MISMO margen que tenían.
     * Afecta al proveedor filtrado (o a todos). Solo toca los que cambiaron de precio.
     */
    public function actualizarPrecios()
    {
        $rows = ListaArticulo::query()
            ->whereNotNull('articulo_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('proveedor_id', $this->proveedor_id))
            ->get();

        $cambiados = 0;

        DB::transaction(function () use ($rows, &$cambiados) {
            foreach ($rows as $r) {
                $art = Articulo::find($r->articulo_id);
                if (!$art) {
                    continue;
                }
                $costoViejo = (int) $art->precioI;
                $costoNuevo = (int) $r->precio_costo;
                if ($costoViejo === $costoNuevo) {
                    continue;
                }

                // Mantener el margen: precioF nuevo = precioF viejo * (costo nuevo / costo viejo).
                $ventaNueva = $costoViejo > 0
                    ? (int) round($art->precioF * ($costoNuevo / $costoViejo))
                    : (int) $art->precioF;

                $art->precioI = $costoNuevo;
                $art->precioF = $ventaNueva;
                $art->save();

                HistoriasPrecio::create([
                    'articulo_id' => $art->id,
                    'precioIcial' => $costoNuevo,
                    'precioFinal' => $ventaNueva,
                ]);
                $cambiados++;
            }
        });

        $this->dispatch('notify', "Actualicé {$cambiados} artículo(s) en stock", 'success');
        session()->flash('message', "Precios actualizados: {$cambiados} artículo(s) en stock (manteniendo el margen).");
    }

    public function render()
    {
        // Info de ítems en USD (para el recuadro de recalcular).
        $usdQuery = ListaArticulo::where('moneda', 'USD')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('proveedor_id', $this->proveedor_id));
        $usdCount = (clone $usdQuery)->count();
        $usdCotiz = (clone $usdQuery)->max('cotizacion');

        $items = ListaArticulo::query()
            ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->leftJoin('articulos', 'articulos.id', '=', 'lista_articulos.articulo_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id))
            ->when($this->soloPendientes, fn ($qb) => $qb->whereNull('lista_articulos.articulo_id'))
            ->when(trim($this->q) !== '', fn ($qb) => Busqueda::palabras($qb, $this->q, ['lista_articulos.codigo', 'lista_articulos.articulo']))
            ->select('lista_articulos.*', 'proveedors.abreviatura', 'articulos.precioI as costo_actual')
            ->orderBy('lista_articulos.articulo')
            ->paginate(25);

        // Resumen de variación de precios para los que YA están en stock (del filtro actual).
        $cmp = ListaArticulo::query()
            ->join('articulos', 'articulos.id', '=', 'lista_articulos.articulo_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id));
        $conAumento = (clone $cmp)->whereColumn('lista_articulos.precio_costo', '>', 'articulos.precioI')->count();
        $conBaja    = (clone $cmp)->whereColumn('lista_articulos.precio_costo', '<', 'articulos.precioI')->count();

        $proveedores = Proveedor::orderBy('nombre')->get();
        // Grupos del proveedor del ítem que se está pasando a artículos.
        $gruposPromover = $this->promoverProveedorId
            ? Grupos::where('proveedor_id', $this->promoverProveedorId)->orderBy('NombreGrupo')->get()
            : collect();

        return view('livewire.articulo.catalogo', compact('items', 'proveedores', 'gruposPromover', 'usdCount', 'usdCotiz', 'conAumento', 'conBaja'));
    }
}
