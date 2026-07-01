<?php

namespace App\Livewire\Articulo;

use App\Models\Articulo;
use App\Models\Grupos;
use App\Models\GruposArticulos;
use App\Models\Stock;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Gestión de Mano de Obra / Servicios (articulos con categoria_id = 1), separada de los productos.
 * Estos artículos existen en la tabla `articulos` pero se administran acá, no en la lista general.
 */
class ManoDeObra extends Component
{
    public const CATEGORIA_SERVICIOS = 1;
    public const PROVEEDOR_MDO = 2;

    public $mostrarModal = false;
    public $editId = null;
    public $nombre = '';
    public $precio = 0;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|min:2',
            'precio' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'nombre.required' => 'Poné el nombre del servicio.',
        'precio.required' => 'Poné el precio.',
    ];

    public function nuevo()
    {
        $this->reset(['editId', 'nombre', 'precio']);
        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function editar($id)
    {
        $art = Articulo::where('categoria_id', self::CATEGORIA_SERVICIOS)->findOrFail($id);
        $this->editId = $art->id;
        $this->nombre = $art->articulo;
        $this->precio = $art->precioF;
        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function guardar()
    {
        $this->validate();
        $precio = (int) round($this->precio);

        if ($this->editId) {
            Articulo::where('id', $this->editId)->update([
                'articulo' => $this->nombre,
                'precioF'  => $precio,
                'precioI'  => $precio,
            ]);
        } else {
            $grupo = Grupos::where('NombreGrupo', 'Mano de Obra')->first();
            $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'unidad'])->id;

            DB::transaction(function () use ($precio, $grupo, $unidadId) {
                $art = Articulo::create([
                    'articulo' => $this->nombre, 'codigo' => null, 'categoria_id' => self::CATEGORIA_SERVICIOS,
                    'presentacion' => ' - ', 'unidad_id' => $unidadId, 'descuento' => 0, 'unidadVenta' => 'unidad',
                    'precioF' => $precio, 'precioI' => $precio, 'caducidad' => 'No', 'detalles' => ' - ',
                    'suelto' => 0, 'activo' => 1,
                ]);
                Stock::create([
                    'articulo_id' => $art->id, 'proveedor_id' => self::PROVEEDOR_MDO, 'codigo_proveedor' => 'MdO',
                    'stockMinimo' => 1, 'stock' => 10000,
                ]);
                if ($grupo) {
                    GruposArticulos::create(['grupo_id' => $grupo->id, 'articulo_id' => $art->id]);
                }
            });
        }

        $this->mostrarModal = false;
        $this->reset(['editId', 'nombre', 'precio']);
        $this->dispatch('notify', 'Servicio guardado ✓', 'success');
    }

    public function toggleActivo($id)
    {
        $art = Articulo::where('categoria_id', self::CATEGORIA_SERVICIOS)->find($id);
        if ($art) {
            $art->activo = $art->activo ? 0 : 1;
            $art->save();
        }
    }

    public function render()
    {
        $servicios = Articulo::where('categoria_id', self::CATEGORIA_SERVICIOS)
            ->orderBy('id')
            ->get(['id', 'articulo', 'precioF', 'activo']);

        return view('livewire.articulo.mano-de-obra', compact('servicios'));
    }
}
