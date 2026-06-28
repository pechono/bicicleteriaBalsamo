<?php

namespace App\Livewire\Stock;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Suelto;
use App\Models\Unidad;
use Livewire\Component;
use Livewire\WithPagination;

class StockLivewire extends Component
{
    use WithPagination;

    public $active   = true;   // checkbox booleano: tildado = solo activos
    public $q        = '';
    public $categoria_id = '';
    public $sortBy   = 'articulo';
    public $sortAsc  = true;

    protected $queryString = [
        'q'          => ['except' => ''],
        'categoria_id'=> ['except' => ''],
        'sortBy'     => ['except' => 'articulo'],
        'sortAsc'    => ['except' => true],
    ];

    public function updatingQ()           { $this->resetPage(); }
    public function updatingCategoriaId() { $this->resetPage(); }

    public function render()
    {
        $articulos = Articulo::where('articulos.activo', $this->active ? 1 : 0)
            ->when(trim($this->q), fn($query) => \App\Support\Busqueda::palabras(
                $query, $this->q,
                ['articulos.articulo', 'articulos.detalles', 'articulos.codigo', 'stocks.codigo_proveedor']
            ))
            ->when($this->categoria_id, fn($q) =>
                $q->where('articulos.categoria_id', $this->categoria_id)
            )
            // Por defecto ocultamos los servicios (categoria_id = 1 = MDO); aparecen
            // solo si el usuario elige esa categoría en el filtro.
            ->when(!$this->categoria_id, fn($q) =>
                $q->where('articulos.categoria_id', '<>', 1)
            )
            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
            ->select(
                'articulos.id', 'articulos.codigo', 'articulos.articulo',
                'categorias.categoria', 'articulos.categoria_id',
                'articulos.descuento', 'articulos.unidadVenta',
                'articulos.precioF', 'articulos.precioI',
                'articulos.detalles', 'articulos.suelto', 'articulos.activo',
                'stocks.stock', 'stocks.stockMinimo', 'stocks.codigo_proveedor',
                'proveedors.iva_incluido'
            )
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('unidads',    'unidads.id',    '=', 'articulos.unidad_id')
            ->join('stocks',     'stocks.articulo_id', '=', 'articulos.id')
            ->leftJoin('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
            ->paginate(20);

        $categorias = Categoria::orderBy('categoria')->get();
        $proveedores = Proveedor::where('activo', 1)->orderBy('nombre')->get();

        // Grupos del proveedor del artículo que se está activando (para el modal).
        $gruposActivar = ($this->activarArt && $this->proveedor_id)
            ? Grupos::where('proveedor_id', $this->proveedor_id)->orderBy('NombreGrupo')->get()
            : collect();

        return view('livewire.stock.stock-livewire', compact('articulos', 'categorias', 'proveedores', 'gruposActivar'));
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
    public $idArt, $codigo, $articulo, $presentacion, $unidad_id, $descuento, $unidadVenta,
            $precioF, $precioI, $caducidad, $detalles, $suelto, $porcentaje, $proveedor_id, $stock, $stockMinimo;

    public $confirmingArticuloEdit=false;
    public $ConfirmarCambioStock=false;

    public function confirmarArticuloEdit($artEdit )
    {
        $edit=Articulo::where('activo',$this->active)
        ->select('articulos.id','articulos.codigo', 'articulos.articulo',  'articulos.presentacion',
        'articulos.descuento', 'articulos.unidadVenta', 'articulos.suelto', 'articulos.activo','stocks.proveedor_id',
        'stocks.stock','stocks.stockMinimo','unidads.unidad','articulos.categoria_id','stocks.codigo_proveedor')
        ->join('stocks', 'stocks.articulo_id','=','articulos.id')
        ->join('unidads', 'unidads.id','articulos.unidad_id')
        ->find($artEdit);
            $this->idArt=$edit->id ;
            $this->codigo=$edit->codigo;
            $this->articulo=$edit->articulo.'   '.$edit->presentacion .'- '.$edit->unidad;
            $this->categoria_id=$edit->categoria_id;
            $this->unidadVenta=$edit->unidadVenta;
            $this->stockMinimo=$edit->stockMinimo;
            $this->stock=$edit->stock;
            $this->proveedor_id=$edit->proveedor_id;
            $this->confirmingArticuloEdit=true;

    }
    protected $rules=[
        'stock'=>'required|numeric',
        'stockMinimo'=>'required|numeric',
        'proveedor_id'=>'required|numeric'
    ];


    public function preguntaCambiarStock($id)
    {
        $this->ConfirmarCambioStock=$id;
    }
    public $confirmingArticuloDeletion=false;
    public $idArtM;
    public function confirmarArticuloDeletion($id)
    {
        $this->confirmingArticuloDeletion=true;
        $this->idArtM=$id;
    }
     public function CambiarStock($id)
     {
         $this->validate();
         $stock = Stock::where('articulo_id',$id);
            $stock->update([
                'stock' => $this->stock,
                'stockMinimo' => $this->stockMinimo,
                'proveedor_id' => $this->proveedor_id
            ]);

         $this->ConfirmarCambioStock=false;
         $this->confirmingArticuloEdit=false;
     }
     public function deleteArticulo()
     {
        $art=Articulo::find($this->idArtM);
        $art->update([
            'activo'=>0,
         ]);
         $this->articulo = $this->categoria_id = $this->presentacion = $this->unidad_id = $this->descuento =$this->codigo = null;
         $this->unidadVenta = $this->precioF = $this->precioI = $this->caducidad = $this->detalles = $this->suelto = $this->stockMinimo = $this->stock = $this->proveedor_id = null;


         $this->confirmingArticuloDeletion=false;
     }
     public $activarArt=false;
     public $articuloId;
     public $iva_incluido;
     public $nombreActivar;   // nombre para el modal (evita colision con el $articulo del foreach)
     public $grupoActivar = '';   // grupo a asociar (opcional)
     public $margenActivar = '';  // % de margen para calcular el precio
     public function ActivarArticuloEdit($id ){
         // Traemos costo, precio, stock e IVA para activarlo seteando todo en un paso.
         $art = Articulo::join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->leftJoin('proveedors', 'proveedors.id', '=', 'stocks.proveedor_id')
            ->where('articulos.id', $id)
            ->select('articulos.id', 'articulos.articulo', 'articulos.precioI', 'articulos.precioF',
                     'stocks.stock', 'stocks.stockMinimo', 'stocks.proveedor_id', 'proveedors.iva_incluido')
            ->first();

         $this->articuloId    = $art->id;
         $this->nombreActivar = $art->articulo;
         $this->precioI       = $art->precioI;      // costo (referencia)
         $this->precioF       = $art->precioF;      // precio de venta (editable)
         $this->stock         = $art->stock;
         $this->stockMinimo   = $art->stockMinimo;
         $this->proveedor_id  = $art->proveedor_id;
         $this->iva_incluido  = $art->iva_incluido;
         $this->grupoActivar  = '';
         $this->margenActivar = '';
         $this->activarArt    = true;
     }

     /** Calcula el precio de venta = costo (+IVA si el proveedor discrimina) × (1 + margen%). */
     private function calcularPrecioActivar(): void
     {
         if ($this->margenActivar === '' || !is_numeric($this->margenActivar)) {
             return;
         }
         $base = (float) $this->precioI;
         if (!$this->iva_incluido) {
             $base *= 1.21; // el costo es sin IVA -> lo sumamos
         }
         $this->precioF = (int) round($base * (1 + ((float) $this->margenActivar) / 100));
     }

     public function updatedMargenActivar(): void
     {
         $this->calcularPrecioActivar();
     }

     public function updatedGrupoActivar(): void
     {
         if ($this->grupoActivar && $g = Grupos::find($this->grupoActivar)) {
             $this->margenActivar = $g->porsentaje;   // el grupo trae su % de margen
             $this->calcularPrecioActivar();
         }
     }
     public function ConfirmarActivar(){
         $this->validate([
             'precioF'     => 'required|numeric|min:1',
             'stock'       => 'required|numeric|min:0',
             'stockMinimo' => 'required|numeric|min:0',
         ], [
             'precioF.required' => 'Ingresá el precio de venta.',
             'stock.required'   => 'Ingresá el stock.',
         ]);

         Articulo::where('id', $this->articuloId)->update([
             'precioF' => $this->precioF,
             'activo'  => 1,
         ]);
         Stock::where('articulo_id', $this->articuloId)->update([
             'stock'       => $this->stock,
             'stockMinimo' => $this->stockMinimo,
         ]);

         // Si eligió un grupo, lo asociamos.
         if ($this->grupoActivar) {
             GruposArticulos::firstOrCreate([
                 'grupo_id'    => $this->grupoActivar,
                 'articulo_id' => $this->articuloId,
             ]);
         }

         $this->activarArt = false;
         $this->reset(['articuloId', 'nombreActivar', 'precioI', 'precioF', 'stock', 'stockMinimo',
                       'iva_incluido', 'grupoActivar', 'margenActivar']);
     }
}
