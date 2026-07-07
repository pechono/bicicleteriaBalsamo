<?php

namespace App\Livewire\Articulo;

use App\Models\Articulo;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Gestión de categorías de artículos: crear, renombrar y eliminar.
 * "Servicio" (id 1) y "General" están protegidas (no se pueden borrar) porque
 * son las que usa el sistema por defecto (servicios / categoría por defecto).
 */
class Categorias extends Component
{
    public $nueva = '';
    public $editId = null;
    public $editNombre = '';

    /** IDs que no se pueden eliminar. */
    private function protegidas(): array
    {
        $general = Categoria::where('categoria', 'General')->value('id');
        return array_values(array_filter([1, $general]));
    }

    public function crear(): void
    {
        $this->validate(
            ['nueva' => 'required|string|min:2|unique:categorias,categoria'],
            ['nueva.required' => 'Poné un nombre.', 'nueva.unique' => 'Ya existe esa categoría.']
        );
        Categoria::create(['categoria' => trim($this->nueva)]);
        $this->nueva = '';
        $this->dispatch('notify', 'Categoría creada ✓', 'success');
    }

    public function editar($id): void
    {
        $c = Categoria::find($id);
        if (!$c) {
            return;
        }
        $this->editId = $c->id;
        $this->editNombre = $c->categoria;
        $this->resetErrorBag();
    }

    public function guardarEdicion(): void
    {
        $this->validate(
            ['editNombre' => 'required|string|min:2|unique:categorias,categoria,' . $this->editId],
            ['editNombre.required' => 'Poné un nombre.', 'editNombre.unique' => 'Ya existe esa categoría.']
        );
        Categoria::whereKey($this->editId)->update(['categoria' => trim($this->editNombre)]);
        $this->editId = null;
        $this->editNombre = '';
        $this->dispatch('notify', 'Categoría renombrada ✓', 'success');
    }

    public function cancelarEdicion(): void
    {
        $this->editId = null;
        $this->editNombre = '';
        $this->resetErrorBag();
    }

    public function eliminar($id): void
    {
        if (in_array($id, $this->protegidas())) {
            $this->dispatch('notify', 'No se puede eliminar «Servicio» ni «General»', 'warning');
            return;
        }

        $general = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        DB::transaction(function () use ($id, $general) {
            // Los artículos de esa categoría pasan a General (no quedan huérfanos).
            Articulo::where('categoria_id', $id)->update(['categoria_id' => $general]);
            Categoria::whereKey($id)->delete();
        });
        $this->dispatch('notify', 'Categoría eliminada (sus artículos pasaron a General)', 'success');
    }

    public function render()
    {
        $categorias = Categoria::query()
            ->leftJoin('articulos', 'articulos.categoria_id', '=', 'categorias.id')
            ->select('categorias.id', 'categorias.categoria', DB::raw('count(articulos.id) as cantidad'))
            ->groupBy('categorias.id', 'categorias.categoria')
            ->orderBy('categorias.categoria')
            ->get();

        return view('livewire.articulo.categorias', [
            'categorias' => $categorias,
            'protegidas' => $this->protegidas(),
        ]);
    }
}
