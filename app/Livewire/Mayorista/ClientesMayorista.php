<?php

namespace App\Livewire\Mayorista;

use Livewire\Component;
use App\Models\ClienteMayorista;

class ClientesMayorista extends Component
{
    public array  $clientes = [];
    public bool   $modalForm = false;
    public bool   $modalDelete = false;
    public ?int   $editingId = null;
    public string $busqueda = '';

    // Form fields
    public string $nombre = '';
    public string $apellido = '';
    public string $telefono = '';
    public string $email = '';
    public string $cuit = '';
    public string $direccion = '';
    public float  $porcentaje_extra = 0;
    public bool   $activo = true;

    protected $rules = [
        'nombre'           => 'required|string|min:2',
        'apellido'         => 'required|string|min:2',
        'telefono'         => 'nullable|string',
        'email'            => 'nullable|email',
        'cuit'             => 'nullable|string',
        'direccion'        => 'nullable|string',
        'porcentaje_extra' => 'required|numeric|min:0',
    ];

    public function mount(): void
    {
        $this->cargarClientes();
    }

    public function cargarClientes(): void
    {
        $this->clientes = ClienteMayorista::when($this->busqueda, fn($q) =>
            $q->where('nombre', 'like', "%{$this->busqueda}%")
              ->orWhere('apellido', 'like', "%{$this->busqueda}%")
              ->orWhere('cuit', 'like', "%{$this->busqueda}%")
        )->orderBy('apellido')->get()->toArray();
    }

    public function updatedBusqueda(): void
    {
        $this->cargarClientes();
    }

    public function nuevo(): void
    {
        $this->reset(['editingId','nombre','apellido','telefono','email','cuit','direccion','porcentaje_extra']);
        $this->activo = true;
        $this->modalForm = true;
    }

    public function editar(int $id): void
    {
        $c = ClienteMayorista::findOrFail($id);
        $this->editingId       = $id;
        $this->nombre          = $c->nombre;
        $this->apellido        = $c->apellido;
        $this->telefono        = $c->telefono ?? '';
        $this->email           = $c->email ?? '';
        $this->cuit            = $c->cuit ?? '';
        $this->direccion       = $c->direccion ?? '';
        $this->porcentaje_extra = (float)$c->porcentaje_extra;
        $this->activo          = (bool)$c->activo;
        $this->modalForm       = true;
    }

    public function guardar(): void
    {
        $this->validate();
        $data = [
            'nombre'           => $this->nombre,
            'apellido'         => $this->apellido,
            'telefono'         => $this->telefono,
            'email'            => $this->email,
            'cuit'             => $this->cuit,
            'direccion'        => $this->direccion,
            'porcentaje_extra' => $this->porcentaje_extra,
            'activo'           => $this->activo,
        ];
        if ($this->editingId) {
            ClienteMayorista::find($this->editingId)->update($data);
        } else {
            ClienteMayorista::create($data);
        }
        $this->modalForm = false;
        $this->cargarClientes();
    }

    public function confirmarEliminar(int $id): void
    {
        $this->editingId = $id;
        $this->modalDelete = true;
    }

    public function eliminar(): void
    {
        ClienteMayorista::find($this->editingId)?->update(['activo' => false]);
        $this->modalDelete = false;
        $this->cargarClientes();
    }

    public function render()
    {
        return view('livewire.mayorista.clientes-mayorista');
    }
}
