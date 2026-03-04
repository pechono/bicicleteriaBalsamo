<?php

namespace App\Livewire\Service;

use App\Models\Articulo;
use App\Models\Car;
use App\Models\Cliente;
use App\Models\Ofertas;
use App\Models\Operacion;
use App\Models\Stock;
use App\Models\TipoVenta;
use App\Models\Venta;
use App\Models\Mecanico;
use App\Models\NroIngreso;
use App\Models\Bici;
use App\Models\EgresoBici;
use App\Models\NroEgreso;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\If_;

class EgresoTerminar extends Component
{
    public $nro, $cantidadArt, $descArt, $NroDatos,$idBici;

    public function mount($nro_ingreso)
    {
        $this->nro = $nro_ingreso;
        $this->procesosCargar($this->nro);
    }

// A
    public $active=1;
    public $q;

    public $sortBy='id';
    public $sortAsc=true;
    public $f;

    public $a;
    public $suel=0;
    public $cad='No';
    public $total=0;
    // public $inTheCar=[];

    protected $queryString = [
        'q'=>['except'=>''],
        'sortBy'=>['except'=>'id'],
        'sortAsc'=>['except'=>true],
    ];

    protected $rules=[
        'apellido'=>'required|string|min:4',
        'nombre'=>'required|string|min:4',
        'telefono'=>'required|string|min:4',
        'dni' => 'required|regex:/^\d{7,9}$/|unique:clientes,dni',
        'activo'=>'boolean',
        'tipo_id'=>'required|integer',
        'cliente_id'=>'required|integer',
        'detalles'=>'required|string|min:4',
        'cuentaCorriente'=>'required|float',
         ];
    public $BloquearBoton;
    public function cancelarBoton(){
        if (Car::exists()) {
              $this->BloquearBoton=true;
        } else {
            $this->BloquearBoton=false;
        }
    }
    public $estaEnCarrito;
    public function render()
    {
        $articulos = collect(); // Colección vacía por defecto



        if ($this->q) {
            $articulos = Articulo::where('activo', $this->active)
                ->where(function ($query) {
                    $query->where('articulo', 'like', '%' . $this->q . '%')
                        ->orWhere('detalles', 'like', '%' . $this->q . '%')
                        ->orWhere('categoria', 'like', '%' . $this->q . '%');
                })
                ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
                ->select('articulos.id','articulos.codigo', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
                    'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
                    'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'stocks.codigo_proveedor')
                ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
                ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
                ->get();
                
        }
        $this->procesosCargar($this->nro);
        $inTheCar = Car::where('user_id', auth()->user()->id)
        ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->select('articulos.id','articulos.codigo', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
            'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
            'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'cars.cantidad', 'cars.articulo_id', 'cars.descuento','stocks.codigo_proveedor')
        ->get();
  
        $countCar = Car::count();
        $tipoVentas=TipoVenta::all();
        $clientes=Cliente::all();
        $mecanicos=Mecanico::all();
        $this->cancelarBoton();
        
        //    Cliente asociado al ingreso de bicicleta (puede ser null)
        $clientesBici = Cliente::select(
        'ingreso_bicis.nro_ingreso',
        'clientes.nombre',
        'clientes.apellido',
        'clientes.dni',
        'clientes.telefono',
        'bicis.color',
        'bicis.id',
        'marcas.marca',
        'tipo_bikes.tipo',
        'nro_ingresos.estado'
        )
        ->join('bicis', 'bicis.cliente_id', '=', 'clientes.id')
        ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
        ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
        ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->where('ingreso_bicis.nro_ingreso', $this->nro)
        ->first();

        $this->idBici=$clientesBici->id;


        // $nDetalles = NroIngreso::find($this->nro);
            $nDetalles ='';
        
        return view('livewire.service.egreso-terminar', compact(
        'inTheCar', 
        'articulos', 
        'countCar',
        'tipoVentas', 
        'clientes', 
        'nDetalles', 
        'clientesBici',
        'mecanicos'
     ));
    }
    public $procesos = [],$mecanicoSelect;
    public function procesosCargar($nro_ingresoP)
{
   
    $this->procesos = Bici::join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->join('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->where('ingreso_bicis.nro_ingreso', $nro_ingresoP)
        ->select(
            'articulos.id',
            'stocks.codigo_proveedor',
            'articulos.codigo',
            'articulos.articulo'
        )->get();
}
    

    public function Total(){
        $inTheCar = Car::where('user_id', auth()->user()->id)
        ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->select('articulos.id', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
            'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
            'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'cars.cantidad', 'cars.articulo_id', 'cars.descuento', 'stocks.codigo_proveedor')
        ->get();

        
        $this->total=0;
        foreach($inTheCar as $car){
            $this->total+= ($car->cantidad*$car->precioF)-($car->cantidad*$car->precioF)*$car->descuento/100;
        }
    }
    public $id, $art, $categoria, $presentacion, $unidad, $descuento, $unidadVenta, $precioF, $precioI, $caducidad, $detalles, $suelto, $stockMinimo, $stock, $proveedor_id;
    public $agregarCant=false;
    public $articulosMuestra=[];
    public function addCar($id)
    {
        $this->articulosMuestra = Articulo::select('articulos.id','articulos.articulo','categorias.categoria','articulos.presentacion','unidads.unidad','articulos.descuento','articulos.unidadVenta',
            'articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles','articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','stocks.codigo_proveedor')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->where('articulos.activo', $this->active)
        ->find($id);


        $this->agregarCant=1;
    }
    public function modCar($id){
        $this->articulosMuestra = Articulo::select('articulos.id','articulos.articulo','categorias.categoria','articulos.presentacion','unidads.unidad','articulos.descuento','articulos.unidadVenta',
        'articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles','articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','stocks.codigo_proveedor')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->where('articulos.activo', $this->active)
        ->find($id);

        $update=Car::where('user_id', auth()->user()->id)->where('articulo_id','=',$id)->first();
        $this->cantidadArt=$update->cantidad;

    $this->agregarCant=2;

    }
    public function updateSave($idart,$stockArt){
       
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
            $this->majStock = $this->cantidadArt;//"Stock Insuficiente para realizar esta operacion";
        }
    }

    public $majStock='--';
    public function save($idart,$stockArt){
        $this->addCar($idart);
        if ($stockArt >= $this->cantidadArt){
            $this->validate(['cantidadArt'=>'required|numeric']);
            $this->addCar($idart);
            Car::create([
                'articulo_id'=>$idart,
                'cantidad'=>$this->cantidadArt,
                'user_id'=>auth()->user()->id,
                
                'operacionCar'=>100
            ]);
            $this->agregarCant=false;
            $this->Total();
            $this->q='';

        }else{
            $this->addCar($idart);
            $this->majStock="Stock Insuficiente para realizar esta operacion";
        }
    }

    public function deletCar($id){
        Car::where('articulo_id', '=', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();
    
        $this->cancelarBoton();
        $this->Total();
        $this->render();
    }

    public $cDescuento=false;
    public function descuentoArt($id){

        $articulos=Articulo::select('articulos.id','articulos.articulo','categorias.categoria','articulos.presentacion','unidads.unidad','articulos.descuento','articulos.unidadVenta',
                                    'articulos.precioF','articulos.precioI','articulos.caducidad','articulos.detalles','articulos.suelto','articulos.activo','stocks.stock','stocks.stockMinimo','stocks.codigo_proveedor')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where('articulos.activo', $this->active)
            ->find($id);

            $this->id=$articulos->id;
            $this->art=$articulos->articulo;
            $this->categoria=$articulos->categoria;
            $this->presentacion=$articulos->presentacion;
            $this->unidad=$articulos->unidad;
            $this->descuento=$articulos->descuento;
            $this->unidadVenta=$articulos->unidadVenta;
            $this->precioF=$articulos->precioF;
            $this->precioI=$articulos->precioI;
            $this->caducidad=$articulos->caducidad;
            $this->detalles=$articulos->detalles;
            $this->suelto=$articulos->suelto;
            $this->stockMinimo=$articulos->stockMinimo;
            $this->stock=$articulos->stock;
            $this->proveedor_id=$articulos->proveedor_id;

            $this->cDescuento=true;
            // $this->Total();
    }

    public function saveDescuento($idart){
        $this->validate(['descArt'=>'required|numeric']);
        Car::where('user_id', auth()->user()->id)->where('articulo_id',$idart)->update([
            'descuento'=>$this->descArt
        ]);
        $this->cDescuento=false;
        $this->Total();
    }
    public $confirmingArticuloOperacion=false;

    public $totalV=0;
    public $cuentaCorriente=0;
    public $tipo_id;
    public $cliente_id;
    public $ac='display:none';
    public $operacion;
    // -----------op
   
    public $clienteId;
    public function ConfirmarVenta()
     {

        $inTheCar = Car::where('user_id', auth()->user()->id)
        ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->select('articulos.id', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
            'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
            'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'cars.cantidad', 'cars.articulo_id', 'cars.descuento','stocks.codigo_proveedor')
        ->get();



        $client = Cliente::select('clientes.id')
        ->join('bicis', 'bicis.cliente_id', '=', 'clientes.id')
        ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->where('ingreso_bicis.nro_ingreso', $this->nro)
        ->first();
        $this->clienteId=$client->id;
        $this->cliente_id=$client->id;

        // $this->Total();
        $this->validate(['mecanicoSelect'=>'required|numeric']);        
            $detallesNotas='-';
            NroEgreso::create([
                'numeroEgreso'=>'-',
                'monto'=>$this->total,
                'detalles'=>$detallesNotas,
                'mecanico_id'=>$this->mecanicoSelect,
            ]);
             $nro_egreso=NroEgreso::latest()->first();
             $idegreso=$nro_egreso->id;

             foreach($inTheCar as $car){
               
                 EgresoBici::create([
                    'ingreso_bici_id'=>$this->idBici,
                     'articulo_id'=>$car->articulo_id,
                     'cantidad'=>$car->cantidad,
                     'precio_inicial'=>$car->precioI,
                     'precio_final'=>$car->precioF,
                     'nro_egreso'=>$idegreso
                 ]);

                 $changeStock=Stock::where('articulo_id',$car->articulo_id)->first();
                 $changeStock->update([
                     'stock'=>$changeStock->stock - $car->cantidad,
                 ]);

             }
         
       $nroIngreso = NroIngreso::find($this->nro); // o el ID que corresponda
        if ($nroIngreso) {
            $nroIngreso->update([
                'estado' => 'completado'
            ]);
        }
         Car::where('user_id', auth()->user()->id)->delete();//Car::truncate();
         $this->cliente_id='';
         $this->tipo_id='';
         $this->cancelarBoton();
         return redirect()->route('service.egresoBici');
     }

     public $apellido;
     public $nombre;
     public $biciId;
     public $dni;
     public $telefono;
     public $activo=1;

     public $confirmingClienteAdd=false;
     public function confirmarClienteAdd()
     {

         $this->confirmingClienteAdd=true;
     }

     
     public $confirmarOpVenta=false;

     public function PreguntaConfirmarVenta(){
        $this->confirmarOpVenta=true;
      }

     public function cancelarOperacion()
     {   $this->cancelarBoton();
         Car::truncate();
         $this->cliente_id='';
         $this->tipo_id='';
         return redirect()->route('venta.ventaExpress');
     }
     public function Ofeta($id){
        $ofertaArt = Ofertas::where('articulo_id', $id)->exists();
        return $ofertaArt ? true : false;
    }
    public function stockInsufisinte($id){
        $stock=Stock::where('articulo_id',$id)->first();

        return $stock->stock<=0 ? true : false;
    }
    public function estaEnCarrito ($articulo){
       return $inTheCar = Car::where('user_id', auth()->user()->id)->get()->contains('articulo_id', $articulo);

   }
}
