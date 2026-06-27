<?php

namespace App\Livewire\Stock;
use Illuminate\Database\Eloquent\ModelNotFoundException;


use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\HistoriasPrecio;
use App\Models\PedidoCar;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Suelto;
use App\Models\Unidad;
use Livewire\Component;
use Livewire\Features\SupportNavigate\ThirdPage;
use Livewire\WithPagination;

use function Laravel\Prompts\select;

class PedidoLivewire extends Component
{
    use WithPagination;

    public $active = 1;
    public $q;
    public $categoria_id = '';
    public $proveedor_id_filter = '';
    public $grupo_id = '';

    public $sortBy = 'id';
    public $sortAsc = true;
    public $f;

    public $a;
    public $suel = 0;
    public $cad = 'No';

    protected $queryString = [
        'q'=>['except'=>''],
        'sortBy'=>['except'=>'id'],
        'sortAsc'=>['except'=>true],
    ];
    public function render()
    {
        $this->hasRecords = PedidoCar::count();

        $articulos = Articulo::where('articulos.activo', $this->active)
            ->when($this->q, fn($query) =>
                $query->where(fn($q) => \App\Support\Busqueda::palabras($q, $this->q, ['articulos.articulo','articulos.codigo','stocks.codigo_proveedor','proveedors.nombre','categorias.categoria']))
            )
            ->when($this->categoria_id, fn($q) =>
                $q->where('articulos.categoria_id', $this->categoria_id)
            )
            ->when($this->proveedor_id_filter, fn($q) =>
                $q->where('stocks.proveedor_id', $this->proveedor_id_filter)
            )
            ->when($this->grupo_id, fn($q) =>
                $q->join('grupos_articulos', 'grupos_articulos.articulo_id', '=', 'articulos.id')
                  ->where('grupos_articulos.grupo_id', $this->grupo_id)
            )
            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
            ->select('articulos.id', 'articulos.codigo', 'articulos.articulo', 'categorias.categoria',
                'articulos.categoria_id', 'articulos.presentacion', 'unidads.unidad',
                'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI',
                'articulos.caducidad', 'articulos.detalles', 'articulos.suelto', 'articulos.activo',
                'stocks.stock', 'stocks.stockMinimo', 'stocks.proveedor_id',
                'proveedors.nombre', 'stocks.codigo_proveedor')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->join('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
            ->get();

        $inTheCar   = PedidoCar::all();
        $categorias = Categoria::orderBy('categoria')->get();
        $proveedores = Proveedor::where('activo', 1)->orderBy('nombre')->get();
        $grupos     = $this->proveedor_id_filter
            ? Grupos::where('proveedor_id', $this->proveedor_id_filter)->orderBy('NombreGrupo')->get()
            : collect();

        return view('livewire.stock.pedidolivewire', compact('articulos', 'inTheCar', 'categorias', 'proveedores', 'grupos'));
    }

    public function updatingProveedorIdFilter()
    {
        $this->grupo_id = '';
    }
    public function sortby($field)
    {
        if($field==$this->sortBy)
        {
            $this->sortAsc=!$this->sortAsc;
        }

        $this->sortBy=$field;
    }

    public function updatingActive()
    {
        $this->resetPage();
    }
    public function updatingO()
    {
        $this->resetPage();

    }

    public $agregarCar=false;
    public $id;
    public $codigo;
    public $art;
    public $categoria;
    public $presentacion;
    public $unidad;
    public $pedido;
    public $stockMinimo;
    public $stock;
    public $proveedor;
    public $codigo_proveedor;
    public $var=0;
    public $msj='';
    public $hasRecords;

    public function addCar($id) {
        $this->var=1;
        $this->agregarCar=true;
        $articulo = Articulo::where('articulos.id', $id)
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->join('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
        ->select('articulos.id','articulos.codigo','articulos.articulo','categorias.categoria',
            'articulos.presentacion','unidads.unidad','articulos.descuento',
            'articulos.unidadVenta','articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles',
            'articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','proveedors.nombre','stocks.codigo_proveedor')
        ->first();
        $this->id=$articulo->id;
        $this->codigo=$articulo->codigo;
        $this->art=$articulo->articulo;
        $this->categoria=$articulo->categoria;
        $this->presentacion=$articulo->presentacion;
        $this->unidad=$articulo->unidad;
        $this->stock=$articulo->stock;
        $this->stockMinimo=$articulo->stockMinimo;
        $this->proveedor=$articulo->nombre;
        $this->codigo_proveedor=$articulo->codigo_proveedor;
        $this->msj='Cantidad a Solicitar';

    }
    protected $rules=['pedido'=>'required|numeric' ];
    public function crearPedido()
    {
         $this->validate();
        PedidoCar::create([
            'articulo_id'=>$this->id,
            'cantidad'=>$this->pedido
        ]);
        $this->agregarCar=false;
        $this->var=0;
    }
    public function ModCar($id)
    {
        $this->agregarCar=true;
        $this->var=2;
        $articulo = Articulo::where('articulos.id', $id)
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->join('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
        ->select('articulos.id', 'articulos.codigo','articulos.articulo','categorias.categoria',
            'articulos.presentacion','unidads.unidad','articulos.descuento',
            'articulos.unidadVenta','articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles',
            'articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','proveedors.nombre','stocks.codigo_proveedor')
        ->first();
        $this->id=$articulo->id;
        $this->codigo=$articulo->codigo;
        $this->art=$articulo->articulo;
        $this->categoria=$articulo->categoria;
        $this->presentacion=$articulo->presentacion;
        $this->unidad=$articulo->unidad;
        $this->stock=$articulo->stock;
        $this->stockMinimo=$articulo->stockMinimo;
        $this->proveedor=$articulo->nombre;
        $this->codigo_proveedor=$articulo->codigo_proveedor;
        try {
            $p = PedidoCar::where('articulo_id', $id)->firstOrFail();
            $this->pedido = $p->cantidad;
        } catch (ModelNotFoundException $e) {
            $this->pedido = 0;
        }
        $this->msj='Modificar la Cantidad Solicitada';
    }
    public function modPedido($id){
        $car=PedidoCar::first()->where('articulo_id',$id);
        $car->update([
            'cantidad'=>$this->pedido
        ]);
        $this->agregarCar=false;
        $this->var=0;

    }
    public $eliminar=false;
    public function elimCar($id)
    {
        $this->eliminar=true;
        $articulo = Articulo::where('articulos.id', $id)
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->join('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
        ->select('articulos.id','articulos.codigo','articulos.articulo','categorias.categoria',
            'articulos.presentacion','unidads.unidad','articulos.descuento',
            'articulos.unidadVenta','articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles',
            'articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','proveedors.nombre','stocks.codigo_proveedor')
        ->first();
        $this->id=$articulo->id;
        $this->codigo=$articulo->codigo;
        $this->art=$articulo->articulo;
        $this->categoria=$articulo->categoria;
        $this->presentacion=$articulo->presentacion;
        $this->unidad=$articulo->unidad;
        $this->stock=$articulo->stock;
        $this->stockMinimo=$articulo->stockMinimo;
        $this->proveedor=$articulo->nombre;
        $this->codigo_proveedor=$articulo->codigo_proveedor;

        try {
            $p = PedidoCar::where('articulo_id', $id)->firstOrFail();
            $this->pedido = $p->cantidad;
        } catch (ModelNotFoundException $e) {
            $this->pedido = 0;
        }
    }
    public function eliminarElementCar($id) {
        $artElimin=PedidoCar::where('articulo_id',$id)->delete();
        $this->eliminar=false;
    }
    public $borrar=false;
    public function borrarCar(){
        $this->borrar=true;
    }
    public function confirmarElimin(){
        PedidoCar::truncate();
        $this->borrar=false;

    }

}


