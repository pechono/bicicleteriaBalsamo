<?php

namespace App\Livewire\Stock;

use App\Models\ListaArticulo;
use App\Models\PedidoCatalogoItem;
use App\Models\PedidoCatalogoOrden;
use App\Models\Proveedor;
use App\Support\Busqueda;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Armar un pedido a proveedores DESDE EL CATÁLOGO (lista_articulos).
 * Se ve el costo actualizado y el pedido mínimo (Dal Santo; vtaminima).
 * NO toca el stock: el pedido queda guardado y el artículo se da de ingreso a
 * stock cuando LLEGA la mercadería (sección "Recibir Pedidos de Catálogo").
 */
class PedidoCatalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';
    public $cantidades = [];       // clave = id de lista_articulos
    public $confirmando = false;

    protected $queryString = ['q' => ['except' => ''], 'proveedor_id' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }

    /* ================== CARRITO (sesión) ================== */

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

    /* ================== CONFIRMAR ================== */

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

    /** Guarda el pedido (sin tocar stock) y lleva a la sección de recepción. */
    public function confirmarPedido()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            $this->dispatch('notify', 'El pedido está vacío', 'warning');
            return;
        }

        $rows = ListaArticulo::whereIn('id', array_keys($cart))->get();

        // El pedido es de un solo proveedor.
        $provs = $rows->pluck('proveedor_id')->unique()->values();
        if ($provs->count() > 1) {
            $this->dispatch('notify', 'El pedido debe ser de un solo proveedor. Filtrá y armá uno por proveedor.', 'warning');
            return;
        }
        $proveedorId = $provs->first();

        $numero = (int) (PedidoCatalogoOrden::max('numero') ?? 0) + 1;

        $orden = PedidoCatalogoOrden::create([
            'numero'       => $numero,
            'proveedor_id' => $proveedorId,
            'estado'       => 'pendiente',
            'enviado'      => false,
        ]);

        foreach ($rows as $r) {
            PedidoCatalogoItem::create([
                'pedido_catalogo_id' => $orden->id,
                'lista_articulo_id'  => $r->id,
                'cantidad'           => (int) $cart[$r->id],
                'precio_costo'       => (int) $r->precio_costo,
                'recibido'           => false,
                'articulo_id'        => $r->articulo_id, // si ya estaba en stock queda vinculado
            ]);
        }

        session()->forget('pedcat_cart');
        $this->confirmando = false;
        session()->flash('message', "Pedido de catálogo #{$numero} creado. Al llegar la mercadería, dale ingreso desde acá.");

        return redirect()->route('stock.recibirCatalogo');
    }

    public function render()
    {
        $items = ListaArticulo::query()
            ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->when($this->proveedor_id, fn ($qb) => $qb->where('lista_articulos.proveedor_id', $this->proveedor_id))
            ->when(trim($this->q) !== '', fn ($qb) => Busqueda::palabras($qb, $this->q, ['lista_articulos.codigo', 'lista_articulos.articulo']))
            ->select('lista_articulos.*', 'proveedors.abreviatura')
            ->orderBy('lista_articulos.articulo')
            ->paginate(25);

        $cart = $this->getCart();
        $cartItems = collect();
        $totalCar = 0;
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
                $totalCar += $ci->cantidad * (int) $ci->precio_costo;
            }
        }

        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('livewire.stock.pedido-catalogo', compact('items', 'cartItems', 'totalCar', 'proveedores'));
    }
}
