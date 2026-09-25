<?php

namespace App\Livewire\Stock;

use App\Models\ListaArticulo;
use App\Models\PedidoCatalogoCar;
use App\Models\PedidoCatalogoItem;
use App\Models\PedidoCatalogoOrden;
use App\Models\Proveedor;
use App\Support\Busqueda;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Armar pedidos a proveedores DESDE EL CATÁLOGO (lista_articulos).
 * Carrito en tabla (pedido_catalogo_car): admite VARIOS proveedores a la vez;
 * al filtrar por uno se ve solo lo suyo. Realizar/Borrar es POR proveedor.
 * Costo con IVA (×1,21) si la lista viene neta (iva_incluido=false).
 * "Realizar" guarda el pedido + genera informe; NO vacía el carrito (se borra
 * con el botón). El ingreso a stock se hace al recibir la mercadería.
 */
class PedidoCatalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';
    public $cantidades = [];

    public $ultimoPedidoId = null;
    public $ultimoPedidoNumero = null;

    protected $queryString = ['q' => ['except' => ''], 'proveedor_id' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }

    private function costoEfectivo($precioCosto, $ivaIncluido): int
    {
        return $ivaIncluido ? (int) $precioCosto : (int) round($precioCosto * 1.21);
    }

    /* ================== CARRITO (tabla) ================== */

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
        PedidoCatalogoCar::updateOrCreate(
            ['lista_articulo_id' => $row->id],
            ['proveedor_id' => $row->proveedor_id, 'cantidad' => $cant]
        );
        $this->dispatch('notify', 'Agregado al pedido ✓', 'success');
    }

    public function quitarCart($listaId)
    {
        PedidoCatalogoCar::where('lista_articulo_id', $listaId)->delete();
    }

    public function borrarProveedor($proveedorId)
    {
        PedidoCatalogoCar::where('proveedor_id', $proveedorId)->delete();
        if ($this->ultimoPedidoNumero) {
            $this->ultimoPedidoId = null;
            $this->ultimoPedidoNumero = null;
        }
        $this->dispatch('notify', 'Pedido borrado', 'warning');
    }

    /**
     * Genera el pedido de UN proveedor (guarda + deja el informe). No vacía el
     * carrito de ese proveedor (se borra con el botón).
     */
    public function realizarProveedor($proveedorId)
    {
        $carRows = PedidoCatalogoCar::where('proveedor_id', $proveedorId)->get();
        if ($carRows->isEmpty()) {
            $this->dispatch('notify', 'No hay ítems de ese proveedor', 'warning');
            return;
        }

        $rows = ListaArticulo::leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->whereIn('lista_articulos.id', $carRows->pluck('lista_articulo_id')->all())
            ->select('lista_articulos.*', 'proveedors.iva_incluido')
            ->get()->keyBy('id');

        $numero = (int) (PedidoCatalogoOrden::max('numero') ?? 0) + 1;
        $orden = PedidoCatalogoOrden::create([
            'numero'       => $numero,
            'proveedor_id' => $proveedorId,
            'estado'       => 'pendiente',
            'enviado'      => false,
        ]);

        foreach ($carRows as $cr) {
            $r = $rows[$cr->lista_articulo_id] ?? null;
            if (!$r) {
                continue;
            }
            PedidoCatalogoItem::create([
                'pedido_catalogo_id' => $orden->id,
                'lista_articulo_id'  => $r->id,
                'cantidad'           => (int) $cr->cantidad,
                'precio_costo'       => $this->costoEfectivo($r->precio_costo, $r->iva_incluido),
                'recibido'           => false,
                'articulo_id'        => $r->articulo_id,
            ]);
        }

        $this->ultimoPedidoId = $orden->id;
        $this->ultimoPedidoNumero = $numero;
        $this->dispatch('notify', "Pedido #{$numero} generado ✓ (mirá el informe; el carrito queda)", 'success');
    }

    public function render()
    {
        $items = ListaArticulo::query()
            ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id))
            ->when(trim($this->q) !== '', fn ($qb) => Busqueda::palabras($qb, $this->q, ['lista_articulos.codigo', 'lista_articulos.articulo']))
            ->select('lista_articulos.*', 'proveedors.abreviatura', 'proveedors.iva_incluido')
            ->orderBy('lista_articulos.articulo')
            ->paginate(25);

        // Carrito (tabla), agrupado por proveedor. Si hay proveedor filtrado, solo ese.
        $cartRows = PedidoCatalogoCar::query()
            ->join('lista_articulos', 'lista_articulos.id', '=', 'pedido_catalogo_car.lista_articulo_id')
            ->leftJoin('proveedors', 'proveedors.id', '=', 'pedido_catalogo_car.proveedor_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('pedido_catalogo_car.proveedor_id', $this->proveedor_id))
            ->select(
                'pedido_catalogo_car.cantidad',
                'lista_articulos.id as lista_id', 'lista_articulos.articulo', 'lista_articulos.precio_costo',
                'lista_articulos.articulo_id',
                'proveedors.iva_incluido', 'proveedors.id as prov_id', 'proveedors.nombre as prov_nombre'
            )
            ->orderBy('proveedors.nombre')->orderBy('lista_articulos.articulo')
            ->get();

        $cartGroups = [];
        foreach ($cartRows as $ci) {
            $ci->costo_ef = $this->costoEfectivo($ci->precio_costo, $ci->iva_incluido);
            $ci->subtotal = $ci->cantidad * $ci->costo_ef;
            $pid = $ci->prov_id;
            if (!isset($cartGroups[$pid])) {
                $cartGroups[$pid] = ['proveedor' => $ci->prov_nombre, 'proveedor_id' => $pid, 'items' => [], 'subtotal' => 0];
            }
            $cartGroups[$pid]['items'][] = $ci;
            $cartGroups[$pid]['subtotal'] += $ci->subtotal;
        }
        $cartCount = $cartRows->count();

        $enCarrito = PedidoCatalogoCar::pluck('cantidad', 'lista_articulo_id')->all();

        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('livewire.stock.pedido-catalogo', compact('items', 'cartGroups', 'cartCount', 'enCarrito', 'proveedores'));
    }
}
