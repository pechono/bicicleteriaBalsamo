<?php

namespace App\Livewire\Stock;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\ListaArticulo;
use App\Models\Pedido;
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
 * Se ve el costo actualizado y el pedido mínimo (Dal Santo; vtaminima).
 * El carrito puede tener ítems que todavía NO están en stock; el "pasar a stock"
 * se hace AL FINAL (al Realizar Pedido). Una vez todos en stock, se arma el pedido
 * en la tabla `pedidos` y aparece en Pedidos Realizados (mismo flujo/PDF de siempre).
 */
class PedidoCatalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';

    /** Cantidades por fila del catálogo (clave = id de lista_articulos). */
    public $cantidades = [];

    /** Modo "confirmar pedido" (resumen final con los pendientes de pasar a stock). */
    public $confirmando = false;

    // ── Modal "pasar a stock" ──
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

    /* ================== CARRITO (en sesión, admite ítems fuera de stock) ================== */

    private function getCart(): array
    {
        return session()->get('pedcat_cart', []); // [lista_id => cantidad]
    }

    private function setCart(array $c): void
    {
        session()->put('pedcat_cart', $c);
    }

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
        $cart = $this->getCart();
        $cart[$listaId] = $cant;
        $this->setCart($cart);
        $this->dispatch('notify', 'Agregado al pedido ✓', 'success');
    }

    public function quitarCart($listaId)
    {
        $cart = $this->getCart();
        unset($cart[$listaId]);
        $this->setCart($cart);
    }

    public function vaciarCarrito()
    {
        session()->forget('pedcat_cart');
        $this->confirmando = false;
    }

    /* ================== CONFIRMAR / FINALIZAR ================== */

    public function irAConfirmar()
    {
        if (empty($this->getCart())) {
            $this->dispatch('notify', 'El pedido está vacío', 'warning');
            return;
        }
        $this->confirmando = true;
    }

    public function volverACatalogo()
    {
        $this->confirmando = false;
    }

    /** Crea el pedido en la tabla `pedidos` (aparece en Pedidos Realizados). */
    public function confirmarPedido()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            $this->dispatch('notify', 'El pedido está vacío', 'warning');
            return;
        }

        $rows = ListaArticulo::whereIn('id', array_keys($cart))->get();

        // ¿Quedan ítems sin pasar a stock?
        $pendientes = $rows->filter(fn ($r) => !($r->articulo_id && Articulo::whereKey($r->articulo_id)->exists()));
        if ($pendientes->count() > 0) {
            $this->dispatch('notify', "Faltan pasar a stock {$pendientes->count()} ítem(s)", 'warning');
            return;
        }

        // El pedido es de un solo proveedor (se arma filtrando por proveedor).
        $provs = $rows->pluck('proveedor_id')->unique()->values();
        if ($provs->count() > 1) {
            $this->dispatch('notify', 'El pedido debe ser de un solo proveedor. Armá uno por proveedor.', 'warning');
            return;
        }
        $proveedorId = $provs->first();

        $p = Pedido::latest()->first();
        $nro = $p ? $p->pedido + 1 : 1;

        foreach ($rows as $r) {
            Pedido::create([
                'articulo_id'  => $r->articulo_id,
                'cantidad'     => (int) $cart[$r->id],
                'proveedor_id' => $proveedorId,
                'pedido'       => $nro,
            ]);
        }

        session()->forget('pedcat_cart');
        $this->confirmando = false;
        session()->flash('message', "Pedido #{$nro} creado. Está en Pedidos Realizados para enviarlo por WhatsApp.");

        return redirect()->route('stock.pedidoRealizado');
    }

    /* ================== PASAR A STOCK (al final, por ítem pendiente) ================== */

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

        if ($row->articulo_id && Articulo::whereKey($row->articulo_id)->exists()) {
            $this->cerrarPromover();
            $this->dispatch('notify', 'Ese ítem ya estaba en stock', 'success');
            return;
        }

        $categoriaId = (int) $this->pCategoriaId;
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
        $this->dispatch('notify', 'Pasado a stock ✓', 'success');
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

        // Carrito (desde sesión) con datos para mostrar.
        $cart = $this->getCart();
        $cartItems = collect();
        $totalCar = 0;
        $pendientes = 0;
        if (!empty($cart)) {
            $cartItems = ListaArticulo::query()
                ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
                ->whereIn('lista_articulos.id', array_keys($cart))
                ->select('lista_articulos.*', 'proveedors.abreviatura')
                ->orderBy('lista_articulos.articulo')
                ->get();
            foreach ($cartItems as $ci) {
                $ci->cantidad = (int) ($cart[$ci->id] ?? 0);
                $ci->en_stock = (bool) $ci->articulo_id;
                if (!$ci->en_stock) {
                    $pendientes++;
                }
                $totalCar += $ci->cantidad * (int) $ci->precio_costo;
            }
        }

        $proveedores = Proveedor::orderBy('nombre')->get();
        $gruposPromover = $this->promoverProveedorId
            ? Grupos::where('proveedor_id', $this->promoverProveedorId)->orderBy('NombreGrupo')->get()
            : collect();
        $categorias = Categoria::orderBy('categoria')->get();

        return view('livewire.stock.pedido-catalogo', compact(
            'items', 'cartItems', 'totalCar', 'pendientes', 'proveedores', 'gruposPromover', 'categorias'
        ));
    }
}
