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
use Illuminate\Support\Facades\DB;
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

        // Vínculos caja↔suelto: por artículo suelto (su cantidad/caja) y qué cajas ya tienen suelto.
        $vinculos = Suelto::whereNotNull('caja_id')->get();
        $sueltos = $vinculos->keyBy('articulo_id');
        $cajasConSuelto = $vinculos->pluck('caja_id')->all();

        return view('livewire.stock.stock-livewire', compact('articulos', 'categorias', 'proveedores', 'gruposActivar', 'sueltos', 'cajasConSuelto'));
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

    public $editCategoriaId; // categoría del modal de editar (separada del filtro $categoria_id)

    public $confirmingArticuloEdit=false;
    public $ConfirmarCambioStock=false;

    public function confirmarArticuloEdit($artEdit )
    {
        $edit=Articulo::where('activo',$this->active)
        ->select('articulos.id','articulos.codigo', 'articulos.articulo',  'articulos.presentacion',
        'articulos.descuento', 'articulos.unidadVenta', 'articulos.suelto', 'articulos.activo','stocks.proveedor_id',
        'articulos.precioI','articulos.precioF','articulos.detalles',
        'stocks.stock','stocks.stockMinimo','unidads.unidad','articulos.categoria_id','stocks.codigo_proveedor')
        ->join('stocks', 'stocks.articulo_id','=','articulos.id')
        ->join('unidads', 'unidads.id','articulos.unidad_id')
        ->find($artEdit);
            $this->idArt=$edit->id ;
            $this->codigo=$edit->codigo;
            $this->articulo=$edit->articulo;
            $this->editCategoriaId=$edit->categoria_id;
            $this->unidadVenta=$edit->unidadVenta;
            $this->precioI=$edit->precioI;
            $this->precioF=$edit->precioF;
            $this->descuento=$edit->descuento;
            $this->detalles=$edit->detalles;
            $this->stockMinimo=$edit->stockMinimo;
            $this->stock=$edit->stock;
            $this->proveedor_id=$edit->proveedor_id;
            $this->confirmingArticuloEdit=true;

    }
    protected $rules=[
        'articulo'=>'required|string|min:2',
        'stock'=>'required|numeric',
        'stockMinimo'=>'required|numeric',
        'proveedor_id'=>'required|numeric',
        'editCategoriaId'=>'required|exists:categorias,id',
        'precioI'=>'required|numeric|min:0',
        'precioF'=>'required|numeric|min:0',
        'descuento'=>'nullable|numeric|min:0',
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
         Stock::where('articulo_id',$id)->update([
                'stock' => $this->stock,
                'stockMinimo' => $this->stockMinimo,
                'proveedor_id' => $this->proveedor_id
            ]);

         $art = Articulo::find($id);
         $precioCambio = $art && ((int) $art->precioI !== (int) $this->precioI || (int) $art->precioF !== (int) $this->precioF);

         Articulo::whereKey($id)->update([
                'articulo'     => $this->articulo,
                'codigo'       => $this->codigo,
                'categoria_id' => $this->editCategoriaId,
                'precioI'      => (int) $this->precioI,
                'precioF'      => (int) $this->precioF,
                'descuento'    => (int) ($this->descuento ?: 0),
                'detalles'     => $this->detalles,
            ]);

         if ($precioCambio) {
             HistoriasPrecio::create([
                 'articulo_id' => $id,
                 'precioIcial' => (int) $this->precioI,
                 'precioFinal' => (int) $this->precioF,
             ]);
         }

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

     // ── Generar suelto desde una caja cerrada ──────────────────────
     public $sueltoModal = false;
     public $cajaId;
     public $cajaNombre;
     public $cajaCodigo;
     public $cajaPrecioF;
     public $sUnidades;
     public $sPrecioUnit;
     public $sStockInicial = 0;

     public function abrirGenerarSuelto($id)
     {
         $art = Articulo::find($id);
         if (!$art) { return; }
         $this->cajaId        = $art->id;
         $this->cajaNombre    = $art->articulo;
         $this->cajaCodigo    = $art->codigo;
         $this->cajaPrecioF   = (int) $art->precioF;
         $this->sUnidades     = '';
         $this->sPrecioUnit   = '';
         $this->sStockInicial = 0;
         $this->resetErrorBag();
         $this->sueltoModal = true;
     }

     /** Sugiere el precio unitario = precio caja ÷ unidades (redondeado hacia arriba). */
     public function updatedSUnidades($value)
     {
         $n = (int) $value;
         $this->sPrecioUnit = ($n > 0) ? (int) ceil(((int) $this->cajaPrecioF) / $n) : '';
     }

     public function guardarSuelto()
     {
         $this->validate([
             'sUnidades'     => 'required|integer|min:2',
             'sPrecioUnit'   => 'required|numeric|min:1',
             'sStockInicial' => 'required|numeric|min:0',
         ], [
             'sUnidades.required'   => 'Poné cuántas unidades trae la caja.',
             'sUnidades.min'        => 'La caja debe traer al menos 2 unidades.',
             'sPrecioUnit.required' => 'Poné el precio del suelto.',
         ]);

         $caja = Articulo::find($this->cajaId);
         if (!$caja) { return; }
         $stockCaja = Stock::where('articulo_id', $caja->id)->first();

         DB::transaction(function () use ($caja, $stockCaja) {
             $suelto = Articulo::create([
                 'articulo'     => $caja->articulo . ' Suelto',
                 'codigo'       => $caja->codigo ? $caja->codigo . '¬S' : null,
                 'categoria_id' => $caja->categoria_id,
                 'presentacion' => $caja->presentacion ?: '-',
                 'unidad_id'    => $caja->unidad_id,
                 'descuento'    => 0,
                 'unidadVenta'  => 'Unidad',
                 'precioF'      => (int) round($this->sPrecioUnit),
                 'precioI'      => (int) round(((int) $caja->precioI) / max(1, (int) $this->sUnidades)),
                 'caducidad'    => $caja->caducidad ?: 'No',
                 'detalles'     => $caja->detalles ?: '-',
                 'suelto'       => 1,
                 'activo'       => 1,
             ]);

             Stock::create([
                 'articulo_id'      => $suelto->id,
                 'proveedor_id'     => $stockCaja->proveedor_id ?? null,
                 'codigo_proveedor' => $stockCaja->codigo_proveedor ?? null,
                 'stock'            => (int) $this->sStockInicial,
                 'stockMinimo'      => 0,
             ]);

             // Vínculo caja→suelto
             Suelto::create([
                 'articulo_id' => $suelto->id,
                 'caja_id'     => $caja->id,
                 'cantidad'    => (int) $this->sUnidades,
             ]);

             HistoriasPrecio::create([
                 'articulo_id' => $suelto->id,
                 'precioIcial' => (int) $suelto->precioI,
                 'precioFinal' => (int) $suelto->precioF,
             ]);

             // Copia la asociación de grupo de la caja (si tiene).
             $grupo = GruposArticulos::where('articulo_id', $caja->id)->value('grupo_id');
             if ($grupo) {
                 GruposArticulos::firstOrCreate(['grupo_id' => $grupo, 'articulo_id' => $suelto->id]);
             }
         });

         $this->sueltoModal = false;
         $this->dispatch('notify', 'Suelto generado ✓', 'success');
         session()->flash('message', 'Se generó el suelto de «' . $this->cajaNombre . '».');
     }

     /** Abre una caja: descuenta 1 caja y suma las unidades por caja al stock del suelto. */
     public function abrirCaja($sueltoArticuloId)
     {
         $link = Suelto::where('articulo_id', $sueltoArticuloId)->whereNotNull('caja_id')->first();
         if (!$link) {
             $this->dispatch('notify', 'Este suelto no está vinculado a una caja', 'warning');
             return;
         }
         $stockCaja = Stock::where('articulo_id', $link->caja_id)->first();
         if (!$stockCaja || $stockCaja->stock < 1) {
             $this->dispatch('notify', 'No hay cajas en stock para abrir', 'error');
             return;
         }
         DB::transaction(function () use ($link) {
             Stock::where('articulo_id', $link->caja_id)->decrement('stock', 1);
             Stock::where('articulo_id', $link->articulo_id)->increment('stock', (int) $link->cantidad);
         });
         $this->dispatch('notify', 'Caja abierta: +' . $link->cantidad . ' unidades al suelto', 'success');
     }
}
