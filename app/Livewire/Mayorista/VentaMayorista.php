<?php

namespace App\Livewire\Mayorista;

use Livewire\Component;
use App\Models\Articulo;
use App\Models\ClienteMayorista;
use App\Models\VentaMayorista as VentaMayoristaModel;
use App\Models\VentaMayoristaItem;
use App\Models\CuentaCorrienteMayorista;
use App\Models\Stock;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\Proveedor;

class VentaMayorista extends Component
{
    public string $busqueda = '';
    public array  $resultados = [];
    public array  $carrito = [];
    public ?int   $cliente_id = null;
    public string $busquedaCliente = '';
    public array  $resultadosClientes = [];
    public string $tipo_pago = 'efectivo';
    public string $observaciones = '';
    public bool   $modalConfirmar = false;
    public bool   $modalExito     = false;
    public ?int   $ultimaVentaId  = null;

    public function updatedBusqueda(): void
    {
        if (strlen($this->busqueda) < 2) { $this->resultados = []; return; }
        $this->resultados = Articulo::where('articulos.activo', true)
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where(fn($q) => \App\Support\Busqueda::palabras($q, $this->busqueda, ['articulos.articulo','articulos.codigo','stocks.codigo_proveedor']))
            ->select('articulos.*')
            ->with('categoria')->limit(12)->get()
            ->map(fn($a) => $this->mapArticulo($a))->toArray();
    }

    private function mapArticulo(Articulo $a): array
    {
        $grupoArt    = GruposArticulos::where('articulo_id', $a->id)->first();
        $grupo       = $grupoArt  ? Grupos::find($grupoArt->grupo_id) : null;
        $proveedor   = $grupo     ? Proveedor::find($grupo->proveedor_id) : null;
        $porcentaje  = $a->porcentaje_mayorista ?? ($grupo?->porsentaje ?? 0);
        $ivaIncluido = $proveedor?->iva_incluido ?? false;
        $precioMay   = $a->calcularPrecioMayorista((float)$porcentaje, (bool)$ivaIncluido);
        $stock       = Stock::where('articulo_id', $a->id)->sum('stock');
        return [
            'articulo_id'      => $a->id,
            'nombre'           => $a->articulo . ($a->presentacion ? ' '.$a->presentacion : ''),
            'codigo'           => $a->codigo,
            'categoria'        => $a->categoria?->categoria,
            'precio_costo'     => (float)$a->precioI,
            'precio_final'     => (float)$a->precioF,
            'precio_mayorista' => $precioMay,
            'porcentaje'       => (float)$porcentaje,
            'iva_incluido'     => $ivaIncluido,
            'stock'            => (float)$stock,
            'proveedor'        => $proveedor?->nombre,
            'grupo'            => $grupo?->NombreGrupo,
        ];
    }

    public function agregarAlCarrito(int $articuloId): void
    {
        $a = Articulo::find($articuloId);
        if (!$a) return;
        $data = $this->mapArticulo($a);
        foreach ($this->carrito as &$item) {
            if ($item['articulo_id'] === $articuloId) { $item['cantidad']++; $this->busqueda = ''; $this->resultados = []; return; }
        }
        $data['cantidad'] = 1;
        $this->carrito[] = $data;
        $this->busqueda = ''; $this->resultados = [];
    }

    public function quitarDelCarrito(int $index): void
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito);
    }

    public function actualizarCantidad(int $index, $cantidad): void
    {
        $this->carrito[$index]['cantidad'] = max(0.01, (float)$cantidad);
    }

    public function actualizarPorcentaje(int $index, $porcentaje): void
    {
        $artId = $this->carrito[$index]['articulo_id'];
        $art   = Articulo::find($artId);
        $grupoArt  = GruposArticulos::where('articulo_id', $artId)->first();
        $grupo     = $grupoArt ? Grupos::find($grupoArt->grupo_id) : null;
        $proveedor = $grupo ? Proveedor::find($grupo->proveedor_id) : null;
        $iva       = $proveedor?->iva_incluido ?? false;
        $this->carrito[$index]['porcentaje']       = (float)$porcentaje;
        $this->carrito[$index]['precio_mayorista']  = $art->calcularPrecioMayorista((float)$porcentaje, $iva);
    }

    public function totalCarrito(): float
    {
        return array_sum(array_map(fn($i) => $i['precio_mayorista'] * $i['cantidad'], $this->carrito));
    }

    public function updatedBusquedaCliente(): void
    {
        if (strlen($this->busquedaCliente) < 2) { $this->resultadosClientes = []; return; }
        $this->resultadosClientes = ClienteMayorista::where('activo', true)
            ->where(fn($q) => $q->where('nombre','like',"%{$this->busquedaCliente}%")->orWhere('apellido','like',"%{$this->busquedaCliente}%"))
            ->limit(8)->get()
            ->map(fn($c) => ['id'=>$c->id,'nombre'=>$c->nombre.' '.$c->apellido,'cuit'=>$c->cuit])->toArray();
    }

    public function seleccionarCliente(int $id): void
    {
        $this->cliente_id      = $id;
        $c                     = ClienteMayorista::find($id);
        $this->busquedaCliente = $c->nombre.' '.$c->apellido;
        $this->resultadosClientes = [];
    }

    public function confirmar(): void
    {
        if (empty($this->carrito))  { $this->dispatch('notify','El carrito está vacío','warning'); return; }
        if (!$this->cliente_id)     { $this->dispatch('notify','Seleccioná un cliente','warning'); return; }
        $this->modalConfirmar = true;
    }

    public function procesarVenta(): void
    {
        $total = $this->totalCarrito();
        $venta = VentaMayoristaModel::create([
            'cliente_mayorista_id' => $this->cliente_id,
            'total'     => $total,
            'tipo_pago' => $this->tipo_pago,
            'pagado'    => $this->tipo_pago !== 'cuenta_corriente',
            'observaciones' => $this->observaciones,
        ]);
        foreach ($this->carrito as $item) {
            VentaMayoristaItem::create([
                'venta_mayorista_id'  => $venta->id,
                'articulo_id'         => $item['articulo_id'],
                'cantidad'            => $item['cantidad'],
                'precio_costo'        => $item['precio_costo'],
                'precio_mayorista'    => $item['precio_mayorista'],
                'porcentaje_aplicado' => $item['porcentaje'],
            ]);
            $st = Stock::where('articulo_id', $item['articulo_id'])->first();
            if ($st) $st->decrement('stock', $item['cantidad']);
        }
        if ($this->tipo_pago === 'cuenta_corriente') {
            CuentaCorrienteMayorista::create([
                'cliente_mayorista_id' => $this->cliente_id,
                'tipo'  => 'venta',
                'monto' => $total,
                'venta_mayorista_id' => $venta->id,
            ]);
        }
        $this->ultimaVentaId  = $venta->id;
        $this->modalConfirmar = false;
        $this->modalExito     = true;
        $this->carrito = []; $this->cliente_id = null; $this->busquedaCliente = ''; $this->tipo_pago = 'efectivo'; $this->observaciones = '';
    }

    public function render()
    {
        return view('livewire.mayorista.venta-mayorista');
    }
}
