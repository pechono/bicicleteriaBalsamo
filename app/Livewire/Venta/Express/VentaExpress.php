<?php

namespace App\Livewire\Venta\Express;

use App\Models\Articulo;
use App\Models\Car;
use App\Models\Cliente;
use App\Models\Ofertas;
use App\Models\Operacion;
use App\Models\Stock;
use App\Models\TipoVenta;
use App\Models\Venta;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\If_;

class VentaExpress extends Component
{
    use WithPagination;

    public $active = 1;
    public $q, $d;

    public $sortBy = 'id';
    public $sortAsc = true;
    public $f;

    public $a;
    public $suel = 0;
    public $cad = 'No';
    public $total = 0;

    // Propiedades para cliente confirmado
    public $clienteConfirmado = false;
    public $clienteSeleccionadoNombre = '';
    public $clienteSeleccionadoApellido = '';
    public $clienteSeleccionadoId = null;

    protected $queryString = [
        'q' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortAsc' => ['except' => true],
    ];

    protected $rules = [
        'apellido' => 'required|string|min:4',
        'nombre' => 'required|string|min:4',
        'telefono' => 'required|string|min:4',
        'dni' => 'required|regex:/^\d{7,9}$/|unique:clientes,dni',
        'activo' => 'boolean',
        'tipo_id' => 'required|integer',
        'cliente_id' => 'required|integer',
        'detalles' => 'required|string|min:4',
        'cuentaCorriente' => 'required|float',
    ];
    
    public $BloquearBoton;
    
    public function cancelarBoton()
    {
        if (Car::where('user_id', auth()->user()->id)->exists()) {
            $this->BloquearBoton = true;
        } else {
            $this->BloquearBoton = false;
        }
    }
    
    public $estaEnCarrito;

    public function render()
    {
        // 1. Buscar artículos solo si hay término de búsqueda
        $articulos = collect();
        if (!empty($this->q)) {
            $articulos = Articulo::where('activo', $this->active)
                ->where(function ($query) {
                    $query->where('articulo', 'like', '%' . $this->q . '%')
                        ->orWhere('detalles', 'like', '%' . $this->q . '%')
                        ->orWhere('categoria', 'like', '%' . $this->q . '%')
                        ->orWhere('codigo_proveedor', 'like', '%' . $this->q . '%');
                })
                ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
                ->select(
                    'articulos.id',
                    'articulos.codigo',
                    'articulos.articulo',
                    'categorias.categoria',
                    'articulos.presentacion',
                    'unidads.unidad',
                    'articulos.descuento',
                    'articulos.unidadVenta',
                    'articulos.precioF',
                    'articulos.precioI',
                    'articulos.caducidad',
                    'articulos.detalles',
                    'articulos.suelto',
                    'articulos.activo',
                    'stocks.stock',
                    'stocks.stockMinimo',
                    'stocks.codigo_proveedor'
                )
                ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
                ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
                ->get();
        }

        // 2. Obtener artículos en el carrito
        $inTheCar = Car::where('user_id', auth()->user()->id)
            ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->select(
                'articulos.id',
                'articulos.codigo',
                'articulos.articulo',
                'categorias.categoria',
                'articulos.presentacion',
                'unidads.unidad',
                'articulos.descuento',
                'articulos.unidadVenta',
                'articulos.precioF',
                'articulos.precioI',
                'articulos.caducidad',
                'articulos.detalles',
                'articulos.suelto',
                'articulos.activo',
                'stocks.stock',
                'stocks.stockMinimo',
                'cars.cantidad',
                'cars.articulo_id',
                'cars.descuento',
                'stocks.codigo_proveedor'
            )
            ->get();

        // 3. Contar elementos en el carrito
        $countCar = Car::where('user_id', auth()->user()->id)->count();

        // 4. Obtener tipos de venta
        $tipoVentas = TipoVenta::all();

        // 5. MOSTRAR TODOS LOS CLIENTES ACTIVOS INICIALMENTE
        // Y filtrar SOLO cuando hay término de búsqueda
        $clientes = Cliente::where('activo', $this->active)
            ->when(!empty($this->d), function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('nombre', 'like', '%' . $this->d . '%')
                        ->orWhere('apellido', 'like', '%' . $this->d . '%')
                        ->orWhere('dni', 'like', '%' . $this->d . '%')
                        ->orWhere('telefono', 'like', '%' . $this->d . '%');
                });
            })
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        // 6. Calcular total
        $total = 0;
        foreach ($inTheCar as $item) {
            $subtotal = ($item->cantidad * $item->precioF) - ($item->cantidad * $item->precioF * ($item->descuento ?? 0) / 100);
            $total += $subtotal;
        }
        $this->total = $total;

        // 7. Llamar al método cancelarBoton
        $this->cancelarBoton();

        return view('livewire.venta.express.venta-express', compact(
            'inTheCar',
            'articulos',
            'countCar',
            'tipoVentas',
            'clientes',
            'total'
        ));
    }

    public function seleccionarCliente($id)
    {
        $this->cliente_id = $id;
        $cliente = Cliente::find($id);
        if ($cliente) {
            $this->clienteSeleccionadoNombre = $cliente->nombre;
            $this->clienteSeleccionadoApellido = $cliente->apellido;
            $this->clienteSeleccionadoId = $id;
        }
        // Limpiar búsqueda para que desaparezca la lista
        $this->d = '';
    }

    public function limpiarCliente()
    {
        $this->cliente_id = '';
        $this->clienteConfirmado = false;
        $this->clienteSeleccionadoNombre = '';
        $this->clienteSeleccionadoApellido = '';
        $this->clienteSeleccionadoId = null;
        $this->d = '';
    }

    public function confirmarClienteAdd()
    {
        if (empty($this->cliente_id)) {
            session()->flash('error', 'Debe seleccionar un cliente');
            return;
        }
        
        $cliente = Cliente::find($this->cliente_id);
        if (!$cliente) {
            session()->flash('error', 'Cliente no encontrado');
            return;
        }
        
        $this->clienteConfirmado = true;
        $this->clienteSeleccionadoNombre = $cliente->nombre;
        $this->clienteSeleccionadoApellido = $cliente->apellido;
        $this->clienteSeleccionadoId = $cliente->id;
        
        // Limpiar búsqueda
        $this->d = '';
        
        session()->flash('cliente_confirmado', "Cliente {$cliente->nombre} {$cliente->apellido} confirmado correctamente");
    }

    public function Total()
    {
        $inTheCar = Car::where('user_id', auth()->user()->id)
            ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->select(
                'articulos.id',
                'articulos.articulo',
                'categorias.categoria',
                'articulos.presentacion',
                'unidads.unidad',
                'articulos.descuento',
                'articulos.unidadVenta',
                'articulos.precioF',
                'articulos.precioI',
                'articulos.caducidad',
                'articulos.detalles',
                'articulos.suelto',
                'articulos.activo',
                'stocks.stock',
                'stocks.stockMinimo',
                'cars.cantidad',
                'cars.articulo_id',
                'cars.descuento',
                'stocks.codigo_proveedor'
            )
            ->get();

        $this->total = 0;
        foreach ($inTheCar as $car) {
            $this->total += ($car->cantidad * $car->precioF) - ($car->cantidad * $car->precioF) * $car->descuento / 100;
        }
    }
    
    public $id;
    public $art;
    public $categoria;
    public $presentacion;
    public $unidad;
    public $descuento;
    public $unidadVenta;
    public $precioF;
    public $precioI;
    public $caducidad;
    public $detalles;
    public $suelto;
    public $porcentaje;
    public $msj;
    public $descArt = 0;
    public $cantidadArt;
    public $proveedor_id;
    public $stock;
    public $stockMinimo;

    public $agregarCant = false;
    public $articulosMuestra = [];
    
    public function addCar($id)
    {
        $this->articulosMuestra = Articulo::select(
            'articulos.id',
            'articulos.articulo',
            'categorias.categoria',
            'articulos.presentacion',
            'unidads.unidad',
            'articulos.descuento',
            'articulos.unidadVenta',
            'articulos.precioF',
            'articulos.precioI',
            'articulos.caducidad',
            'articulos.detalles',
            'articulos.suelto',
            'articulos.activo',
            'stocks.stock',
            'stocks.stockMinimo',
            'stocks.codigo_proveedor'
        )
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where('articulos.activo', $this->active)
            ->find($id);

        $this->agregarCant = 1;
    }
    
    public function modCar($id)
    {
        $this->articulosMuestra = Articulo::select(
            'articulos.id',
            'articulos.articulo',
            'categorias.categoria',
            'articulos.presentacion',
            'unidads.unidad',
            'articulos.descuento',
            'articulos.unidadVenta',
            'articulos.precioF',
            'articulos.precioI',
            'articulos.caducidad',
            'articulos.detalles',
            'articulos.suelto',
            'articulos.activo',
            'stocks.stock',
            'stocks.stockMinimo',
            'stocks.codigo_proveedor'
        )
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where('articulos.activo', $this->active)
            ->find($id);

        $update = Car::where('user_id', auth()->user()->id)->where('articulo_id', '=', $id)->first();
        $this->cantidadArt = $update->cantidad;

        $this->agregarCant = 2;
    }
    
    public $majStock = '--';
    
    public function updateSave($idart, $stockArt)
    {
        if ($stockArt >= $this->cantidadArt) {
            $this->validate(['cantidadArt' => 'required|numeric']);
            Car::where('user_id', auth()->user()->id)
                ->where('articulo_id', '=', $idart)
                ->update(['cantidad' => $this->cantidadArt]);
            $this->agregarCant = false;
            $this->Total();
            $this->q = '';
        } else {
            $this->modCar($idart);
            $this->majStock = "Stock Insuficiente para realizar esta operacion";
        }
    }
    
    public function save($idart, $stockArt)
    {
        $this->addCar($idart);
        if ($stockArt >= $this->cantidadArt) {
            $this->validate(['cantidadArt' => 'required|numeric']);
            $this->addCar($idart);
            Car::create([
                'articulo_id' => $idart,
                'cantidad' => $this->cantidadArt,
                'user_id' => auth()->user()->id,
                'operacionCar' => 100
            ]);
            $this->agregarCant = false;
            $this->Total();
            $this->q = '';
        } else {
            $this->addCar($idart);
            $this->majStock = "Stock Insuficiente para realizar esta operacion";
        }
    }
    
    public function deletCar($id)
    {
        Car::where('articulo_id', '=', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $this->cancelarBoton();
        $this->Total();
        $this->render();
    }
    
    public $cDescuento = false;
    
    public function descuentoArt($id)
    {
        $articulos = Articulo::select(
            'articulos.id',
            'articulos.articulo',
            'categorias.categoria',
            'articulos.presentacion',
            'unidads.unidad',
            'articulos.descuento',
            'articulos.unidadVenta',
            'articulos.precioF',
            'articulos.precioI',
            'articulos.caducidad',
            'articulos.detalles',
            'articulos.suelto',
            'articulos.activo',
            'stocks.stock',
            'stocks.stockMinimo',
            'stocks.codigo_proveedor'
        )
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where('articulos.activo', $this->active)
            ->find($id);

        $this->id = $articulos->id;
        $this->art = $articulos->articulo;
        $this->categoria = $articulos->categoria;
        $this->presentacion = $articulos->presentacion;
        $this->unidad = $articulos->unidad;
        $this->descuento = $articulos->descuento;
        $this->unidadVenta = $articulos->unidadVenta;
        $this->precioF = $articulos->precioF;
        $this->precioI = $articulos->precioI;
        $this->caducidad = $articulos->caducidad;
        $this->detalles = $articulos->detalles;
        $this->suelto = $articulos->suelto;
        $this->stockMinimo = $articulos->stockMinimo;
        $this->stock = $articulos->stock;
        $this->proveedor_id = $articulos->proveedor_id;

        $this->cDescuento = true;
    }
    
    public function saveDescuento($idart)
    {
        $this->validate(['descArt' => 'required|numeric']);
        Car::where('user_id', auth()->user()->id)->where('articulo_id', $idart)->update([
            'descuento' => $this->descArt
        ]);
        $this->cDescuento = false;
        $this->Total();
    }
    
    public $confirmingArticuloOperacion = false;
    public $totalV = 0;
    public $cuentaCorriente = 0;
    public $tipo_id;
    public $cliente_id;
    public $ac = 'display:none';
    public $operacion;
    
    // -----------op
    public function tipoVenta()
    {
        if ($this->tipo_id == 5) {
            $this->ac = '';
        } else {
            $this->cuentaCorriente = 0;
            $this->ac = 'display:none';
        }
    }
    
    public function ConfirmarVenta()
    {
        $inTheCar = Car::where('user_id', auth()->user()->id)
            ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->select(
                'articulos.id',
                'articulos.articulo',
                'categorias.categoria',
                'articulos.presentacion',
                'unidads.unidad',
                'articulos.descuento',
                'articulos.unidadVenta',
                'articulos.precioF',
                'articulos.precioI',
                'articulos.caducidad',
                'articulos.detalles',
                'articulos.suelto',
                'articulos.activo',
                'stocks.stock',
                'stocks.stockMinimo',
                'cars.cantidad',
                'cars.articulo_id',
                'cars.descuento',
                'stocks.codigo_proveedor'
            )
            ->get();

        $this->validate(['tipo_id' => 'required|numeric', 'cliente_id' => 'required|numeric']);

        if ($this->tipo_id == 5) {
            Operacion::create([
                'usuario_id' => auth()->user()->id,
                'tipoVenta_id' => $this->tipo_id,
                'cliente_id' => $this->cliente_id,
                'detalles' => '-',
                'venta' => 0,
            ]);
            $operacion = Operacion::latest()->first();
            $id = $operacion->id;
            foreach ($inTheCar as $car) {
                Venta::create([
                    'articulo_id' => $car->articulo_id,
                    'cantidad' => $car->cantidad,
                    'precioI' => 0,
                    'precioF' => 0,
                    'descuento' => $car->descuento,
                    'operacion' => $operacion->id,
                ]);

                $changeStock = Stock::where('articulo_id', $car->articulo_id)->first();
                $changeStock->update([
                    'stock' => $changeStock->stock - $car->cantidad,
                ]);
            }
        } else {
            Operacion::create([
                'usuario_id' => auth()->user()->id,
                'tipoVenta_id' => $this->tipo_id,
                'cliente_id' => $this->cliente_id,
                'detalles' => '-',
                'venta' => $this->total,
            ]);
            $operacion = Operacion::latest()->first();
            $id = $operacion->id;
            foreach ($inTheCar as $car) {
                Venta::create([
                    'articulo_id' => $car->articulo_id,
                    'cantidad' => $car->cantidad,
                    'precioI' => $car->precioI,
                    'precioF' => $car->precioF,
                    'descuento' => $car->descuento,
                    'operacion' => $operacion->id,
                ]);
                $changeStock = Stock::where('articulo_id', $car->articulo_id)->first();
                $changeStock->update([
                    'stock' => $changeStock->stock - $car->cantidad,
                ]);
            }
        }

        Car::where('user_id', auth()->user()->id)->delete();
        $this->cliente_id = '';
        $this->tipo_id = '';
        $this->clienteConfirmado = false;
        $this->cancelarBoton();
        return redirect()->route('venta.reporte', ['operacion' => $operacion, 'volver' => 'venta.ventaExpress']);
    }
    
    public $apellido;
    public $nombre;
    public $dni;
    public $telefono;
    public $activo = 1;
    public $confirmingClienteAdd = false;
    
    public function saveCliente()
    {
        $this->validate([
            'apellido' => 'required|string|min:2|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre' => 'required|string|min:2|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'dni' => 'required|string|unique:clientes,dni|min:1|max:15|regex:/^[0-9]+$/',
            'telefono' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
        ]);

        Cliente::create([
            'apellido' => $this->apellido,
            'nombre' => $this->nombre,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'activo' => 1
        ]);

        $this->apellido = '';
        $this->nombre = '';
        $this->dni = '';
        $this->telefono = '';
        $this->confirmingClienteAdd = false;
    }
    
    public function abrirModalCliente()
    {
        $this->reset(['apellido', 'nombre', 'dni', 'telefono']);
        $this->confirmingClienteAdd = true;
    }

    public $confirmarOpVenta = false;
    
    public function PreguntaConfirmarVenta()
    {
        $this->confirmarOpVenta = true;
    }
    
    public function cancelarOperacion()
    {
        $this->cancelarBoton();
        Car::where('user_id', auth()->user()->id)->delete();
        $this->cliente_id = '';
        $this->tipo_id = '';
        $this->clienteConfirmado = false;
        return redirect()->route('venta.ventaExpress');
    }
    
    public function Ofeta($id)
    {
        $ofertaArt = Ofertas::where('articulo_id', $id)->exists();
        return $ofertaArt ? true : false;
    }
    
    public function stockInsufisinte($id)
    {
        $stock = Stock::where('articulo_id', $id)->first();
        return $stock->stock <= 0 ? true : false;
    }
    
    public function estaEnCarrito($articulo)
    {
        $inTheCar = Car::where('user_id', auth()->user()->id)->get()->contains('articulo_id', $articulo);
        return $inTheCar;
    }
}