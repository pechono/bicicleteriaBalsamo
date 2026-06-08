<?php

namespace App\Livewire\Articulo;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proveedor;
use App\Models\Grupos;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\GruposArticulos;
use App\Models\HistoriasPrecio;
use App\Models\Stock;
use App\Models\Suelto;
use App\Models\Unidad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class ArticuloGrupo extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    
    public $paginacionPorDefecto = 10;
    
    public $proveedor;
    public $grupo;
    public $proveedores = [];
    public $grupos = [];
    public $categorias = [];
    public $unidades = [];
    public $articulo, $categoria_id, $presentacion, $unidad_id,
            $descuento, $unidadVenta='Unidad', $precioF, $precioI, $caducidad=false,
            $detalles, $suelto, $porcentaje, $idArtitul, $proveedor_id, $stock, $stockMinimo, $codigo,
            $codigo_proveedor;
    
    public $mensajeError = '-';
    public $cargando = false;

    // — Modales —
    public bool $modalProveedor = false;
    public bool $modalGrupo     = false;
    public bool $modalCategoria = false;

    // Proveedor nuevo
    public $np_nombre = '', $np_telefono = '', $np_rubro = '',
           $np_direccion = '', $np_localidad = '', $np_mail = '', $np_activo = true;

    // Grupo nuevo
    public $ng_nombre = '', $ng_porcentaje = 0;

    // Categoria nueva
    public $nc_nombre = '';

    public function mount()
    {
        $this->proveedores = Proveedor::all();
        $this->categorias = Categoria::all();
        $this->unidades = Unidad::all();
    }
    
    public function render()
    {
        return view('livewire.articulo.articulo-grupo', [
            'articulosGrupo' => $this->grupo ? $this->obtenerArticulosGrupo() : collect()
        ]);
    }
    
    private function obtenerArticulosGrupo()
    {
        if (!$this->grupo) {
            return collect();
        }
        
        try {
            $articulos = DB::table('grupos_articulos')
                ->where('grupos_articulos.grupo_id', $this->grupo)
                ->join('articulos', 'articulos.id', '=', 'grupos_articulos.articulo_id')
                ->leftJoin('stocks', function($join) {
                    $join->on('stocks.articulo_id', '=', 'articulos.id')
                         ->where('stocks.proveedor_id', '=', $this->proveedor_id);
                })
                ->select(
                    'articulos.id',
                    'articulos.articulo',
                    'articulos.codigo',
                    'articulos.presentacion',
                    'articulos.unidadVenta',
                    'stocks.codigo_proveedor'
                )
                ->orderBy('articulos.id')
                ->paginate($this->paginacionPorDefecto);
            
            return $articulos;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar artículos: ' . $e->getMessage());
            return collect();
        }
    }
    
    // ── PROVEEDOR ──────────────────────────────────────────
    public function crearProveedor(): void
    {
        $this->np_nombre = $this->np_telefono = $this->np_rubro =
        $this->np_direccion = $this->np_localidad = $this->np_mail = '';
        $this->np_activo = true;
        $this->modalProveedor = true;
    }

    public function guardarProveedor(): void
    {
        $this->validate([
            'np_nombre'    => 'required|string|min:2',
            'np_telefono'  => 'nullable|string',
            'np_rubro'     => 'nullable|string',
            'np_direccion' => 'nullable|string',
            'np_localidad' => 'nullable|string',
            'np_mail'      => 'nullable|email',
        ], [
            'np_nombre.required' => 'El nombre es obligatorio.',
            'np_mail.email'      => 'El mail no es válido.',
        ]);

        Proveedor::create([
            'nombre'    => $this->np_nombre,
            'telefono'  => $this->np_telefono  ?? '',
            'rubro'     => $this->np_rubro     ?? '',
            'direccion' => $this->np_direccion ?? '',
            'localidad' => $this->np_localidad ?? '',
            'mail'      => $this->np_mail      ?? '',
            'activo'    => $this->np_activo,
        ]);

        $this->proveedores   = Proveedor::all();
        $this->modalProveedor = false;
        session()->flash('message', 'Proveedor creado correctamente.');
    }

    // ── GRUPO ──────────────────────────────────────────────
    public function crearGrupo(): void
    {
        $this->ng_nombre = '';
        $this->ng_porcentaje = 0;
        $this->modalGrupo = true;
    }

    public function guardarGrupo(): void
    {
        $this->validate([
            'proveedor_id'  => 'required',
            'ng_nombre'     => 'required|string|min:2',
            'ng_porcentaje' => 'required|numeric|min:0',
        ], [
            'proveedor_id.required' => 'Primero seleccioná un proveedor.',
            'ng_nombre.required'    => 'El nombre del grupo es obligatorio.',
        ]);

        Grupos::create([
            'proveedor_id' => $this->proveedor_id,
            'NombreGrupo'  => $this->ng_nombre,
            'porsentaje'   => $this->ng_porcentaje,
        ]);

        $this->grupos    = Grupos::where('proveedor_id', $this->proveedor_id)->get();
        $this->modalGrupo = false;
        session()->flash('message', 'Grupo creado correctamente.');
    }

    // ── CATEGORÍA ──────────────────────────────────────────
    public function crearCategoria(): void
    {
        $this->nc_nombre = '';
        $this->modalCategoria = true;
    }

    public function guardarCategoria(): void
    {
        $this->validate([
            'nc_nombre' => 'required|string|min:2',
        ], [
            'nc_nombre.required' => 'El nombre de la categoría es obligatorio.',
        ]);

        Categoria::create(['categoria' => $this->nc_nombre]);

        $this->categorias    = Categoria::all();
        $this->modalCategoria = false;
        session()->flash('message', 'Categoría creada correctamente.');
    }

    public function cerrarModales(): void
    {
        $this->modalProveedor = $this->modalGrupo = $this->modalCategoria = false;
    }

    public function calcular()
    {
        $this->precioF = (($this->precioI * $this->porcentaje) / 100) + $this->precioI;
    }
    
    public function mostrarGrupo()
    {
        $this->grupos = Grupos::where("proveedor_id", $this->proveedor_id)->get();
        $this->grupo = '';
        $this->resetPage();
    }
    
    public function cargarArticulo()
    {
        
        
        $this->validate([
            'articulo'    => 'required|string|min:4',
            'unidad_id'   => 'required',
            'descuento'   => 'required|numeric',
            'unidadVenta' => 'required|string|min:1',
            'precioI'     => 'required|numeric|min:1',
            'precioF'     => 'required|numeric|min:1',
            'detalles'    => 'nullable|string',
            'stock'       => 'required|numeric|min:1',
            'stockMinimo' => 'required|integer|min:1',
            'categoria_id'=> 'required',
            'grupo'       => 'required',
            'proveedor_id'=> 'required',
            'codigo'      => [
                'nullable',
                'unique:articulos,codigo',
                'regex:/^[A-Za-z0-9\-\/\.]+$/'
            ],
        ], [
            'articulo.min'           => 'El artículo debe tener al menos 4 caracteres.',
            'categoria_id.required'  => 'Debe seleccionar una categoría.',
            'grupo.required'         => 'Debe seleccionar un grupo en la parte superior.',
            'proveedor_id.required'  => 'Debe seleccionar un proveedor en la parte superior.',
            'precioI.min'            => 'El precio inicial debe ser mayor a 0.',
            'precioF.min'            => 'El precio final debe ser mayor a 0.',
        ]);

        DB::beginTransaction();
        
        try {
            $articulo = Articulo::create([
                'articulo' => $this->articulo,
                'codigo' => $this->codigo ?: null,
                'categoria_id' => $this->categoria_id,
                'presentacion' => '-',
                'unidad_id'   => $this->unidad_id,
                'descuento'   => $this->descuento,
                'unidadVenta' => $this->unidadVenta,
                'precioF'     => $this->precioF,
                'precioI'     => $this->precioI,
                'caducidad'   => 'No',
                'detalles'    => $this->detalles ?? '',
                'suelto'      => 0,
                'activo'      => 1
            ]);

            $qrData = (string) $articulo->id;
            $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
            $qr = new Writer($renderer);
            $qrImage = $qr->writeString($qrData);
            $fileName = 'qrcodes/articulo_' . $articulo->id . '.svg';
            Storage::disk('public')->put($fileName, $qrImage);
            
            $articulo->qr_code = $fileName;
            $articulo->save();

            $this->codigo_proveedor = Proveedor::find($this->proveedor_id)?->abreviatura ?? null;

            Stock::create([
                'articulo_id' => $articulo->id,
                'stockMinimo' => $this->stockMinimo,
                'stock' => $this->stock,
                'proveedor_id' => $this->proveedor_id,
                'codigo_proveedor' => $this->codigo_proveedor
            ]);

            if ($this->suelto == 1) {
                Suelto::create(['articulo_id' => $articulo->id]);
            }

            HistoriasPrecio::create([
                'articulo_id' => $articulo->id,
                'precioFinal' => $this->precioF,
                'precioIcial' => $this->precioI
            ]);
            
            GruposArticulos::create([
                'grupo_id' => $this->grupo,
                'articulo_id' => $articulo->id
            ]);
            
            DB::commit();
            
            session()->flash('message', 'Artículo creado exitosamente.');
            $this->borrarCampos();
            $this->resetPage();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear artículo: ' . $e->getMessage());
        }
    }
    
    public function articulosGrupos()
    {
        $this->resetPage();
    }
    
    public function borrarCampos()
    {
        $this->articulo = '';
        $this->codigo = null;
        $this->presentacion = '';
        $this->unidad_id = '';
        $this->descuento = '';
        $this->unidadVenta = 'Unidad';
        $this->precioF = '';
        $this->precioI = '';
        $this->caducidad = '';
        $this->detalles = '';
        $this->suelto = '';
        $this->stockMinimo = '';
        $this->stock = '';
    }
    
    public function comprobarCodigo()
    {
        if (empty($this->codigo) || empty($this->proveedor_id)) {
            $this->mensajeError = '-';
            return;
        }

        $existe = DB::table('stocks')
            ->where('proveedor_id', $this->proveedor_id)
            ->join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
            ->where('articulos.codigo', $this->codigo)
            ->exists();
        
        if ($existe) {
            $this->mensajeError = '❌ El código ' . $this->codigo . ' NO está disponible.';
        } else {
            $this->mensajeError = '✅ El código ' . $this->codigo . ' SÍ está disponible.';
        }
    }
    
    public function actualizarPaginacion($cantidad)
    {
        $this->paginacionPorDefecto = $cantidad;
        $this->resetPage();
    }
}