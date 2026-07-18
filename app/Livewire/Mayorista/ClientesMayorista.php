<?php

namespace App\Livewire\Mayorista;

use Livewire\Component;
use App\Models\ClienteMayorista;
use App\Livewire\Traits\WithWhatsApp;

class ClientesMayorista extends Component
{
    use WithWhatsApp;

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
    public bool   $cuenta_corriente_habilitada = false;

    protected $rules = [
        'nombre'           => 'required|string|min:2',
        'apellido'         => 'required|string|min:2',
        'telefono'         => 'nullable|string',
        'email'            => 'nullable|email',
        'cuit'             => 'nullable|string',
        'direccion'        => 'nullable|string',
        'porcentaje_extra' => 'required|numeric|min:0',
        'cuenta_corriente_habilitada' => 'boolean',
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
        $this->cuenta_corriente_habilitada = false;
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
        $this->cuenta_corriente_habilitada = (bool)$c->cuenta_corriente_habilitada;
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
            'cuenta_corriente_habilitada' => $this->cuenta_corriente_habilitada,
        ];
        if ($this->editingId) {
            ClienteMayorista::find($this->editingId)->update($data);
        } else {
            ClienteMayorista::create($data);
        }
        $this->modalForm = false;
        $this->cargarClientes();
    }

    /** Envía por WhatsApp el link del portal al cliente. */
    public function enviarAcceso(int $id): void
    {
        $c = ClienteMayorista::findOrFail($id);
        if (empty(trim((string) $c->telefono))) {
            $this->notify('El cliente no tiene teléfono cargado', 'warning');
            return;
        }
        $msg = "¡Hola {$c->nombre}! 👋\n\n"
             . "Este es tu acceso al portal de Bicicletería Bálsamo, donde podés ver los productos disponibles"
             . ($c->cuenta_corriente_habilitada ? ' y el estado de tu cuenta corriente' : '')
             . ":\n\n" . $c->portalUrl() . "\n\n"
             . "El link es personal, guardalo. ¡Gracias!";
        $this->sendWhatsAppMessage($c->telefono, $msg);
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
