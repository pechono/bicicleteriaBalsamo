<?php

namespace App\Livewire\Stock;

use App\Livewire\Traits\WithWhatsApp;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\ListaArticulo;
use App\Models\PedidoCatalogoItem;
use App\Models\PedidoCatalogoOrden;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

/**
 * Recepción de pedidos armados desde el catálogo. Al llegar la mercadería se
 * da ingreso a stock ítem por ítem (parcial): si el artículo ya existe suma
 * stock; si es nuevo, se crea (pasa a stock) con la cantidad recibida.
 */
class RecibirCatalogo extends Component
{
    use WithWhatsApp;

    public $ordenId = null; // orden abierta en el detalle (null = listado)

    // ── Modal "dar ingreso" de un ítem nuevo (pasar a stock) ──
    public $recibiendoItemId = null;
    public $promoverProveedorId = null;
    public $pNombre = '';
    public $pCosto = 0;
    public $pPublicoLista = 0;
    public $pPrecioVenta = 0;
    public $pStock = 0;
    public $pStockMinimo = 0;
    public $pGrupoId = '';
    public $pCategoriaId = '';
    public $pPorcentaje = 0;
    public $pIva = 0;

    public function verOrden($id) { $this->ordenId = $id; }
    public function cerrarOrden() { $this->ordenId = null; }

    /** Recalcula el estado de la orden según los ítems recibidos. */
    private function recomputarEstado($ordenId): void
    {
        $orden = PedidoCatalogoOrden::find($ordenId);
        if (!$orden) {
            return;
        }
        $total = PedidoCatalogoItem::where('pedido_catalogo_id', $ordenId)->count();
        $recib = PedidoCatalogoItem::where('pedido_catalogo_id', $ordenId)->where('recibido', true)->count();
        $orden->estado = $recib === 0 ? 'pendiente' : ($recib < $total ? 'parcial' : 'recibido');
        $orden->save();
    }

    /**
     * Dar ingreso a un ítem. Si el artículo ya está en stock, suma la cantidad.
     * Si es nuevo, abre el modal para crearlo (pasar a stock) con la cantidad recibida.
     */
    public function darIngreso($itemId)
    {
        $item = PedidoCatalogoItem::find($itemId);
        if (!$item || $item->recibido) {
            return;
        }
        $lista = ListaArticulo::find($item->lista_articulo_id);
        if (!$lista) {
            $this->dispatch('notify', 'No encuentro el artículo del catálogo', 'error');
            return;
        }

        // Ya está en stock → sumar la cantidad recibida.
        if ($lista->articulo_id && Articulo::whereKey($lista->articulo_id)->exists()) {
            Stock::where('articulo_id', $lista->articulo_id)->increment('stock', (int) $item->cantidad);
            $item->update(['recibido' => true, 'articulo_id' => $lista->articulo_id]);
            $this->recomputarEstado($item->pedido_catalogo_id);
            $this->dispatch('notify', 'Ingreso cargado (+' . $item->cantidad . ' al stock) ✓', 'success');
            return;
        }

        // Nuevo → abrir modal para crearlo con la cantidad recibida como stock inicial.
        $this->recibiendoItemId    = $item->id;
        $this->promoverProveedorId = $lista->proveedor_id;
        $this->pNombre       = $lista->articulo;
        $this->pCosto        = $lista->precio_costo;
        $this->pPublicoLista = $lista->precio_publico ?: $lista->precio_costo;
        $this->pPrecioVenta  = $this->pPublicoLista;
        $this->pStock        = (int) $item->cantidad; // lo que llegó
        $this->pStockMinimo  = 0;
        $this->pGrupoId      = '';
        $this->pCategoriaId  = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $this->pPorcentaje   = 0;
        $ivaIncluido = Proveedor::whereKey($lista->proveedor_id)->value('iva_incluido');
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
    public function usarPublico() { $this->pPrecioVenta = $this->pPublicoLista; }

    public function cerrarModal()
    {
        $this->reset(['recibiendoItemId', 'promoverProveedorId', 'pNombre', 'pCosto', 'pPublicoLista', 'pPrecioVenta', 'pStock', 'pStockMinimo', 'pGrupoId', 'pCategoriaId', 'pPorcentaje', 'pIva']);
        $this->resetErrorBag();
    }

    /** Confirma el ingreso de un ítem NUEVO: lo crea en stock con la cantidad recibida. */
    public function confirmarIngreso()
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
        ]);

        $item = PedidoCatalogoItem::find($this->recibiendoItemId);
        if (!$item) {
            $this->cerrarModal();
            return;
        }
        $lista = ListaArticulo::find($item->lista_articulo_id);

        // Si mientras tanto ya se pasó a stock, solo sumar.
        if ($lista->articulo_id && Articulo::whereKey($lista->articulo_id)->exists()) {
            Stock::where('articulo_id', $lista->articulo_id)->increment('stock', (int) $item->cantidad);
            $item->update(['recibido' => true, 'articulo_id' => $lista->articulo_id]);
            $this->recomputarEstado($item->pedido_catalogo_id);
            $this->cerrarModal();
            $this->dispatch('notify', 'Ingreso cargado ✓', 'success');
            return;
        }

        $categoriaId = (int) $this->pCategoriaId;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        $abreviatura = Proveedor::whereKey($lista->proveedor_id)->value('abreviatura');

        DB::transaction(function () use ($item, $lista, $categoriaId, $unidadId, $abreviatura) {
            $articulo = Articulo::create([
                'articulo'     => $this->pNombre,
                'codigo'       => $lista->codigo,
                'categoria_id' => $categoriaId,
                'presentacion' => '-',
                'unidad_id'    => $unidadId,
                'descuento'    => 0,
                'unidadVenta'  => 'Unidad',
                'precioF'      => (int) round($this->pPrecioVenta),
                'precioI'      => (int) $lista->precio_costo,
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
                // el artículo igual queda creado
            }

            Stock::create([
                'articulo_id'      => $articulo->id,
                'proveedor_id'     => $lista->proveedor_id,
                'codigo_proveedor' => $abreviatura,
                'stock'            => (int) $this->pStock,
                'stockMinimo'      => (int) $this->pStockMinimo,
            ]);

            HistoriasPrecio::create([
                'articulo_id' => $articulo->id,
                'precioIcial' => (int) $lista->precio_costo,
                'precioFinal' => (int) round($this->pPrecioVenta),
            ]);

            GruposArticulos::create(['grupo_id' => $this->pGrupoId, 'articulo_id' => $articulo->id]);

            $lista->articulo_id = $articulo->id;
            $lista->save();

            $item->update(['recibido' => true, 'articulo_id' => $articulo->id]);
        });

        $ordenId = $item->pedido_catalogo_id;
        $this->recomputarEstado($ordenId);
        $this->cerrarModal();
        $this->dispatch('notify', 'Artículo creado en stock e ingreso cargado ✓', 'success');
    }

    /** Avisar el pedido al proveedor por WhatsApp (texto con el detalle). */
    public function enviarWhatsApp($ordenId)
    {
        $orden = PedidoCatalogoOrden::find($ordenId);
        if (!$orden) {
            return;
        }
        $prov = Proveedor::find($orden->proveedor_id);
        if (!$prov || empty(trim((string) $prov->telefono))) {
            $this->dispatch('notify', 'El proveedor no tiene teléfono registrado', 'warning');
            return;
        }

        $items = PedidoCatalogoItem::leftJoin('lista_articulos', 'lista_articulos.id', '=', 'pedido_catalogo_items.lista_articulo_id')
            ->where('pedido_catalogo_id', $ordenId)
            ->select('pedido_catalogo_items.cantidad', 'lista_articulos.codigo', 'lista_articulos.articulo')
            ->get();

        $nro = str_pad($orden->numero, 4, '0', STR_PAD_LEFT);
        $lineas = "🚲 *BICICLETERÍA BALSAMO*\nPedido N° {$nro}\n----------------------------\n";
        foreach ($items as $it) {
            $cod = $it->codigo ? "({$it->codigo}) " : '';
            $lineas .= "• {$it->cantidad}x {$cod}{$it->articulo}\n";
        }
        $lineas .= "----------------------------\n¡Gracias!";

        $this->sendWhatsAppMessage($prov->telefono, $lineas);
        $orden->update(['enviado' => true]);
        $this->dispatch('notify', 'Pedido enviado al proveedor ✓', 'success');
    }

    public function render()
    {
        $orden = null;
        $items = collect();
        $gruposModal = collect();
        $categorias = collect();

        if ($this->ordenId) {
            $orden = PedidoCatalogoOrden::leftJoin('proveedors', 'proveedors.id', '=', 'pedido_catalogo_ordenes.proveedor_id')
                ->where('pedido_catalogo_ordenes.id', $this->ordenId)
                ->select('pedido_catalogo_ordenes.*', 'proveedors.nombre as proveedor', 'proveedors.telefono')
                ->first();

            $items = PedidoCatalogoItem::leftJoin('lista_articulos', 'lista_articulos.id', '=', 'pedido_catalogo_items.lista_articulo_id')
                ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
                ->where('pedido_catalogo_items.pedido_catalogo_id', $this->ordenId)
                ->select(
                    'pedido_catalogo_items.*',
                    'lista_articulos.codigo', 'lista_articulos.articulo',
                    'lista_articulos.articulo_id as stock_articulo_id',
                    'proveedors.abreviatura'
                )
                ->orderBy('lista_articulos.articulo')
                ->get();

            $gruposModal = $this->promoverProveedorId
                ? Grupos::where('proveedor_id', $this->promoverProveedorId)->orderBy('NombreGrupo')->get()
                : collect();
            $categorias = Categoria::orderBy('categoria')->get();
        }

        $ordenes = PedidoCatalogoOrden::query()
            ->withCount([
                'items',
                'items as recibidos_count' => fn ($q) => $q->where('recibido', true),
            ])
            ->leftJoin('proveedors', 'proveedors.id', '=', 'pedido_catalogo_ordenes.proveedor_id')
            ->select('pedido_catalogo_ordenes.*', 'proveedors.nombre as proveedor')
            ->orderByDesc('pedido_catalogo_ordenes.numero')
            ->get();

        return view('livewire.stock.recibir-catalogo', compact('ordenes', 'orden', 'items', 'gruposModal', 'categorias'));
    }
}
