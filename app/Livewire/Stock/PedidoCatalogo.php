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
 * Costo actualizado + pedido mínimo (Dal Santo). Para proveedores cuya lista
 * viene SIN IVA (iva_incluido = false) el costo mostrado es lista × 1,21.
 * NO toca el stock: al "Realizar Pedido" se guarda y se genera el informe, pero
 * el carrito NO se vacía (se borra a mano con el botón). El ingreso a stock se
 * hace al llegar la mercadería (sección "Recibir Pedidos de Catálogo").
 */
class PedidoCatalogo extends Component
{
    use WithPagination;

    public $q = '';
    public $proveedor_id = '';
    public $cantidades = [];
    public $confirmando = false;

    // Último pedido generado (para mostrar el link al informe sin vaciar el carrito).
    public $ultimoPedidoId = null;
    public $ultimoPedidoNumero = null;

    protected $queryString = ['q' => ['except' => ''], 'proveedor_id' => ['except' => '']];

    public function updatingQ() { $this->resetPage(); }
    public function updatingProveedorId() { $this->resetPage(); }

    /** Costo real: si la lista del proveedor no incluye IVA, se le suma 21%. */
    private function costoEfectivo($precioCosto, $ivaIncluido): int
    {
        return $ivaIncluido ? (int) $precioCosto : (int) round($precioCosto * 1.21);
    }

    /* ================== CARRITO (sesión) ================== */

    private function getCart(): array
    {
        return session()->get('pedcat_cart', []);
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

    public function borrarPedido()
    {
        session()->forget('pedcat_cart');
        $this->confirmando = false;
        $this->ultimoPedidoId = null;
        $this->ultimoPedidoNumero = null;
        $this->dispatch('notify', 'Pedido borrado', 'warning');
    }

    /* ================== CONFIRMAR / GENERAR ================== */

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

    /**
     * Guarda el pedido (va a "Recibir") y deja listo el informe. NO vacía el carrito:
     * el usuario sigue eligiendo o borra con el botón.
     */
    public function confirmarPedido()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            $this->dispatch('notify', 'El pedido está vacío', 'warning');
            return;
        }

        $rows = ListaArticulo::leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
            ->whereIn('lista_articulos.id', array_keys($cart))
            ->select('lista_articulos.*', 'proveedors.iva_incluido')
            ->get();

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
                'precio_costo'       => $this->costoEfectivo($r->precio_costo, $r->iva_incluido),
                'recibido'           => false,
                'articulo_id'        => $r->articulo_id,
            ]);
        }

        // NO se vacía el carrito. Se deja el link al informe.
        $this->confirmando = false;
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

        $cart = $this->getCart();
        $cartItems = collect();
        $totalCar = 0;
        if (!empty($cart)) {
            $cartItems = ListaArticulo::query()
                ->leftJoin('proveedors', 'proveedors.id', '=', 'lista_articulos.proveedor_id')
                ->whereIn('lista_articulos.id', array_keys($cart))
                ->select('lista_articulos.*', 'proveedors.abreviatura', 'proveedors.iva_incluido')
                ->orderBy('lista_articulos.articulo')
                ->get();
            foreach ($cartItems as $ci) {
                $ci->cantidad = (int) ($cart[$ci->id] ?? 0);
                $ci->en_stock = (bool) $ci->articulo_id;
                $ci->costo_ef = $this->costoEfectivo($ci->precio_costo, $ci->iva_incluido);
                $totalCar += $ci->cantidad * $ci->costo_ef;
            }
        }

        $proveedores = Proveedor::orderBy('nombre')->get();
        $enCarrito = $cart;

        return view('livewire.stock.pedido-catalogo', compact('items', 'cartItems', 'totalCar', 'proveedores', 'enCarrito'));
    }
}
