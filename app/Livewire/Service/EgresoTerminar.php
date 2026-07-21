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
use App\Livewire\Traits\WithWhatsApp;
use App\Models\MecanicoItem;

use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\If_;

class EgresoTerminar extends Component
{
    public $nro, $cantidadArt, $descArt, $NroDatos,$idBici;
    public $esMdO = false;   // el ítem que se agrega/edita es mano de obra (categoría 1)
    public $precioMdO;       // precio a cobrar por esa mano de obra
    use WithWhatsApp;
    public function mount($nro_ingreso)
    {
        $this->nro = $nro_ingreso;
        $this->procesosCargar($this->nro);
                 Car::where('user_id', auth()->user()->id)->delete();//Car::truncate();

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
        'dni' => 'nullable|regex:/^\d{7,9}$/|unique:clientes,dni',
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

    public function cancelarOperacion()
    {
        // Vacía el carrito de este usuario y vuelve al listado de egresos.
        Car::where('user_id', auth()->user()->id)->delete();
        $this->cliente_id = '';
        $this->tipo_id = '';
        return redirect()->route('service.egresoBici');
    }
    public $estaEnCarrito;
    public function render()
    {
        $articulos = collect(); // Colección vacía por defecto



        if ($this->q) {
            $articulos = Articulo::where('activo', $this->active)
                ->where(fn($query) => \App\Support\Busqueda::palabras($query, $this->q, ['articulo', 'codigo', 'detalles', 'categoria', 'codigo_proveedor']))
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
            'articulos.descuento', 'articulos.unidadVenta', DB::raw('COALESCE(cars.precio, articulos.precioF) as precioF'), 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
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
            'articulos.descuento', 'articulos.unidadVenta', DB::raw('COALESCE(cars.precio, articulos.precioF) as precioF'), 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
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

        $this->esMdO = Articulo::where('id', $id)->value('categoria_id') == 1;
        $this->precioMdO = $this->articulosMuestra->precioF;
        if ($this->esMdO) { $this->cantidadArt = 1; }

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

        $this->esMdO = Articulo::where('id', $id)->value('categoria_id') == 1;
        $this->precioMdO = $update->precio ?? $this->articulosMuestra->precioF;
        if ($this->esMdO) { $this->cantidadArt = 1; }

    $this->agregarCant=2;

    }
    public function updateSave($idart,$stockArt){
        $cantidad = $this->esMdO ? 1 : $this->cantidadArt;
        if ($stockArt >= $cantidad) {
            $this->esMdO
                ? $this->validate(['precioMdO' => 'required|numeric|min:1'])
                : $this->validate(['cantidadArt' => 'required|numeric']);

            $data = ['cantidad' => $cantidad];
            if ($this->esMdO) { $data['precio'] = (int) round($this->precioMdO); }

            Car::where('user_id', auth()->user()->id)
               ->where('articulo_id', '=', $idart)
               ->update($data);
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
        $cantidad = $this->esMdO ? 1 : $this->cantidadArt;
        if ($stockArt >= $cantidad){
            $this->esMdO
                ? $this->validate(['precioMdO' => 'required|numeric|min:1'])
                : $this->validate(['cantidadArt'=>'required|numeric']);

            Car::create([
                'articulo_id'=>$idart,
                'cantidad'=>$cantidad,
                'precio'=> $this->esMdO ? (int) round($this->precioMdO) : null,
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
    // public function ConfirmarVenta()
    //  {

    //     $inTheCar = Car::where('user_id', auth()->user()->id)
    //     ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
    //     ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
    //     ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
    //     ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
    //     ->select('articulos.id', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
    //         'articulos.descuento', 'articulos.unidadVenta', 'articulos.precioF', 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
    //         'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'cars.cantidad', 'cars.articulo_id', 'cars.descuento','stocks.codigo_proveedor')
    //     ->get();



    //     $client = Cliente::select('clientes.id')
    //     ->join('bicis', 'bicis.cliente_id', '=', 'clientes.id')
    //     ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
    //     ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
    //     ->where('ingreso_bicis.nro_ingreso', $this->nro)
    //     ->first();
    //     $this->clienteId=$client->id;
    //     $this->cliente_id=$client->id;

    //     // $this->Total();
    //     $this->validate(['mecanicoSelect'=>'required|numeric']);        
    //         $detallesNotas='-';
    //         NroEgreso::create([
    //                          'monto'=>$this->total,
    //             'detalles'=>$detallesNotas,
    //             'mecanico_id'=>$this->mecanicoSelect,
    //         ]);
    //          $nro_egreso=NroEgreso::latest()->first();
    //          $idegreso=$nro_egreso->id;

    //          foreach($inTheCar as $car){
               
    //              EgresoBici::create([
    //                 'ingreso_bici_id'=>$this->idBici,
    //                  'articulo_id'=>$car->articulo_id,
    //                  'cantidad'=>$car->cantidad,
    //                  'precio_inicial'=>$car->precioI,
    //                  'precio_final'=>$car->precioF,
    //                  'nro_egreso'=>$idegreso
    //              ]);

    //              $changeStock=Stock::where('articulo_id',$car->articulo_id)->first();
    //              $changeStock->update([
    //                  'stock'=>$changeStock->stock - $car->cantidad,
    //              ]);

    //          }
         
    //    $nroIngreso = NroIngreso::find($this->nro); // o el ID que corresponda
    //     if ($nroIngreso) {
    //         $nroIngreso->update([
    //             'estado' => 'Terminado'
    //         ]);
    //     }
    //      Car::where('user_id', auth()->user()->id)->delete();//Car::truncate();
    //      $this->cliente_id='';
    //      $this->tipo_id='';
    //      $this->cancelarBoton();
    //      return redirect()->route('service.egresoBici');
    //  }

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

     
     public $confirmarOpVenta = false;

     // Items del mecánico para esta reparación
     public $mecanicoItems = []; // [['descripcion'=>'...','monto'=>0], ...]
     public $itemDesc      = '';
     public $itemMonto     = '';

     public function PreguntaConfirmarVenta(): void
     {
         // Pre-cargar descripción con datos de la bici
         $this->mecanicoItems = [];
         $this->itemDesc      = '';
         $this->itemMonto     = '';
         $this->confirmarOpVenta = true;
     }

     public function agregarItemMecanico(): void
     {
         if (empty(trim($this->itemDesc)) || !is_numeric($this->itemMonto) || $this->itemMonto <= 0) {
             return;
         }
         $this->mecanicoItems[] = [
             'descripcion' => trim($this->itemDesc),
             'monto'       => (float) $this->itemMonto,
         ];
         $this->itemDesc  = '';
         $this->itemMonto = '';
     }

     public function quitarItemMecanico(int $index): void
     {
         array_splice($this->mecanicoItems, $index, 1);
     }

    //  public function cancelarOperacion()
    //  {   $this->cancelarBoton();
    //      Car::truncate();
    //      $this->cliente_id='';
    //      $this->tipo_id='';
    //      return redirect()->route('venta.ventaExpress');
    //  }
     public function Ofeta($id){
        $ofertaArt = Ofertas::where('articulo_id', $id)->exists();
        return $ofertaArt ? true : false;
    }
    public function ConfirmarVenta()
{
    $inTheCar = Car::where('user_id', auth()->user()->id)
        ->join('articulos', 'cars.articulo_id', '=', 'articulos.id')
        ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
        ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
        ->select('articulos.id', 'articulos.articulo', 'categorias.categoria', 'articulos.presentacion', 'unidads.unidad',
            'articulos.descuento', 'articulos.unidadVenta', DB::raw('COALESCE(cars.precio, articulos.precioF) as precioF'), 'articulos.precioI', 'articulos.caducidad', 'articulos.detalles',
            'articulos.suelto', 'articulos.activo', 'stocks.stock', 'stocks.stockMinimo', 'cars.cantidad', 'cars.articulo_id', 'cars.descuento','stocks.codigo_proveedor')
        ->get();

    $client = Cliente::select('clientes.id', 'clientes.nombre', 'clientes.apellido', 'clientes.telefono')
        ->join('bicis', 'bicis.cliente_id', '=', 'clientes.id')
        ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->where('ingreso_bicis.nro_ingreso', $this->nro)
        ->first();
    
    $this->clienteId = $client->id;
    $this->cliente_id = $client->id;

    // ✅ DECLARAR $bicicleta AQUÍ (antes de usarla)
    $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
        ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
        ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
        ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->where('ingreso_bicis.nro_ingreso', $this->nro)
        ->select(
            'clientes.nombre',
            'clientes.apellido',
            'clientes.telefono',
            'marcas.marca',
            'tipo_bikes.tipo',
            'bicis.color',
            'ingreso_bicis.nro_ingreso'
        )
        ->first();

    $this->validate(['mecanicoSelect' => 'required|numeric']);        
    $detallesNotas = '-';
    
    NroEgreso::create([
        'monto' => $this->total,
        'detalles' => $detallesNotas,
        'mecanico_id' => $this->mecanicoSelect,
    ]);
    
    $nro_egreso = NroEgreso::latest()->first();
    $idegreso = $nro_egreso->id;

    // ingreso_bici_id debe ser el id de ingreso_bicis (NO bicis.id que hay en $this->idBici).
    $ingresoBiciId = DB::table('ingreso_bicis')->where('nro_ingreso', $this->nro)->value('id');
    if (!$ingresoBiciId) {
        $this->dispatch('notify', 'No se encontró el ingreso de esta bici', 'error');
        return;
    }

    foreach($inTheCar as $car) {
        EgresoBici::create([
            'ingreso_bici_id' => $ingresoBiciId,
            'articulo_id' => $car->articulo_id,
            'cantidad' => $car->cantidad,
            'precio_inicial' => $car->precioI,
            'precio_final' => $car->precioF,
            'nro_egreso' => $idegreso
        ]);

        $changeStock = Stock::where('articulo_id', $car->articulo_id)->first();
        $changeStock->update([
            'stock' => $changeStock->stock - $car->cantidad,
        ]);
    }

    $nroIngreso = NroIngreso::find($this->nro);
    if ($nroIngreso) {
        $nroIngreso->update([
            'estado' => 'Terminado'
        ]);
    }

    if ($bicicleta && $bicicleta->telefono) {
        $nombre        = $bicicleta->nombre;
        $nroFormateado = str_pad($bicicleta->nro_ingreso, 4, '0', STR_PAD_LEFT);
        $marca         = $bicicleta->marca ?? '';
        $color         = $bicicleta->color ?? '';
        $this->sendWhatsAppMessage(
            $bicicleta->telefono,
            "🔧 *BICICLETERÍA BALSAMO* 🔧\n----------------------------\nHola {$nombre}! 🎉\nTu bicicleta *#{$nroFormateado}* ya está lista\npara retirar en nuestro local.\n\n🚲 {$marca} | {$color}\n----------------------------\n⚠️ *Importante:*\nLa bici puede permanecer en el taller\nhasta *7 días* sin cargo adicional.\nPasado ese plazo se cobrará recargo\npor almacenamiento.\n\nEl local no se responsabiliza por daños\nocasionados por el clima, ni por robo o hurto.\n----------------------------\n¡Te esperamos! 📍"
        );
    }

    // Guardar ítems del mecánico
    foreach ($this->mecanicoItems as $item) {
        MecanicoItem::create([
            'mecanico_id'   => $this->mecanicoSelect,
            'descripcion'   => $item['descripcion'],
            'monto'         => $item['monto'],
            'nro_egreso_id' => $idegreso,
            'pagado'        => false,
        ]);
    }

    Car::where('user_id', auth()->user()->id)->delete();
    $this->cliente_id    = '';
    $this->tipo_id       = '';
    $this->mecanicoItems = [];
    $this->cancelarBoton();

    return redirect()->route('service.egresoBici');
}




    public function stockInsufisinte($id){
        $stock=Stock::where('articulo_id',$id)->first();

        return $stock->stock<=0 ? true : false;
    }
    public function estaEnCarrito ($articulo){
       return $inTheCar = Car::where('user_id', auth()->user()->id)->get()->contains('articulo_id', $articulo);

   }
}
