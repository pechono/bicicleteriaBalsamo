<?php

namespace App\Livewire\Proveedor;

use App\Models\Articulo;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GrupoArticulos extends Component
{
    /** Proveedor seleccionado (filtra grupos y artículos disponibles). */
    public $proveedorId = '';

    /** Grupo seleccionado para gestionar. */
    public $grupoId = null;

    /** Buscador de la lista de disponibles. */
    public $buscar = '';

    /** Modal de creación de grupo. */
    public $crearGrupoModal = false;
    public $nombreGrupo = '';
    public $porsentaje = '';

    /** Máximo de disponibles a mostrar (para no traer miles). */
    public const LIMITE_DISPONIBLES = 50;

    protected $rules = [
        'nombreGrupo' => 'required|string|min:3',
        'porsentaje'  => 'required|numeric',
    ];

    protected $messages = [
        'nombreGrupo.required' => 'Poné un nombre de grupo.',
        'porsentaje.required'  => 'Poné el porcentaje de margen.',
        'porsentaje.numeric'   => 'El porcentaje debe ser un número.',
    ];

    /** Al cambiar de proveedor, reseteamos grupo y búsqueda. */
    public function updatedProveedorId(): void
    {
        $this->grupoId = null;
        $this->buscar = '';
    }

    public function seleccionarGrupo($id): void
    {
        $this->grupoId = $id;
        $this->buscar = '';
    }

    public function abrirCrearGrupo(): void
    {
        $this->reset(['nombreGrupo', 'porsentaje']);
        $this->resetErrorBag();
        $this->crearGrupoModal = true;
    }

    public function crearGrupo(): void
    {
        $this->validate();

        $grupo = Grupos::create([
            'proveedor_id' => $this->proveedorId,
            'NombreGrupo'  => $this->nombreGrupo,
            'porsentaje'   => $this->porsentaje,
        ]);

        $this->crearGrupoModal = false;
        $this->reset(['nombreGrupo', 'porsentaje']);
        $this->grupoId = $grupo->id; // lo dejamos seleccionado
    }

    public function agregar($articuloId): void
    {
        if (!$this->grupoId) {
            return;
        }
        GruposArticulos::firstOrCreate([
            'grupo_id'    => $this->grupoId,
            'articulo_id' => $articuloId,
        ]);
    }

    public function quitar($articuloId): void
    {
        // Scoping por grupo (el código viejo borraba de TODOS los grupos).
        GruposArticulos::where('grupo_id', $this->grupoId)
            ->where('articulo_id', $articuloId)
            ->delete();
    }

    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();

        // Grupos del proveedor + cantidad de artículos.
        $grupos = collect();
        if ($this->proveedorId) {
            $grupos = Grupos::where('proveedor_id', $this->proveedorId)
                ->orderBy('NombreGrupo')
                ->get()
                ->each(function ($g) {
                    $g->cantidad = GruposArticulos::where('grupo_id', $g->id)->count();
                });
        }

        $grupo = $this->grupoId ? Grupos::find($this->grupoId) : null;

        // Artículos que YA están en el grupo.
        $enGrupo = collect();
        if ($grupo) {
            $enGrupo = Articulo::query()
                ->join('grupos_articulos', 'grupos_articulos.articulo_id', '=', 'articulos.id')
                ->leftJoin('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->where('grupos_articulos.grupo_id', $grupo->id)
                ->select('articulos.id', 'articulos.codigo', 'articulos.articulo', 'articulos.precioI', 'articulos.precioF', 'categorias.categoria')
                ->orderBy('articulos.articulo')
                ->get();
        }

        // Disponibles: del proveedor elegido, que NO estén en ningún grupo, con buscador.
        $disponibles = collect();
        $totalDisponibles = 0;
        if ($grupo) {
            $base = Articulo::query()
                ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
                ->leftJoin('grupos_articulos', 'grupos_articulos.articulo_id', '=', 'articulos.id')
                ->leftJoin('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->where('stocks.proveedor_id', $this->proveedorId)
                ->whereNull('grupos_articulos.articulo_id')
                ->when(trim($this->buscar), fn($q) => \App\Support\Busqueda::palabras(
                    $q, $this->buscar,
                    ['articulos.articulo', 'articulos.codigo', 'stocks.codigo_proveedor']
                ));

            $totalDisponibles = (clone $base)->count('articulos.id');

            $disponibles = $base
                ->select('articulos.id', 'articulos.codigo', 'articulos.articulo', 'articulos.precioI', 'categorias.categoria')
                ->orderBy('articulos.articulo')
                ->limit(self::LIMITE_DISPONIBLES)
                ->get();
        }

        return view('livewire.proveedor.grupo-articulos', compact(
            'proveedores', 'grupos', 'grupo', 'enGrupo', 'disponibles', 'totalDisponibles'
        ));
    }
}
