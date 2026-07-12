<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class GestionUser extends Component
{
    public $modalAddUser = false;
    public $modalEditUser = false;
    public $nombre;
    public $email;
    public $password;
    public $password_confirmation;
    public $tipoUser;
    public $userId;
    public $users;
    public $usertipo = [
        [1, 'Admin', 'bg-red-100', 'text-red-800', '👑'],
        [2, 'Operador', 'bg-blue-100', 'text-blue-800', '🔧'],
        [3, 'Usuario', 'bg-gray-100', 'text-gray-800', '👤']
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'tipoUser' => 'required|in:1,2,3',
    ];

    protected $editRules = [
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,',
        'password' => 'nullable|min:6|confirmed',
        'tipoUser' => 'required|in:1,2,3',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'email.required' => 'El email es obligatorio',
        'email.email' => 'Ingrese un email válido',
        'email.unique' => 'Este email ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
        'tipoUser.required' => 'Seleccione un tipo de usuario',
    ];

    public function render()
    {
        $this->users = User::orderBy('id', 'desc')->get();
        return view('livewire.admin.gestion-user', [
            'users' => $this->users,
            'usertipo' => $this->usertipo,
        ]);
    }

    public function ingresarUsuario()
    {
        $this->resetValidation();
        $this->reset(['nombre', 'email', 'password', 'password_confirmation', 'tipoUser']);
        $this->modalAddUser = true;
    }

    public function addUser()
    {
        $this->validate($this->rules);

        $tipoMap = [
            1 => 'Admin',
            2 => 'Operator',
            3 => 'Usuario'
        ];

        User::create([
            'name' => $this->nombre,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'user_type' => $tipoMap[$this->tipoUser],
            'is_active' => true,
        ]);

        $this->modalAddUser = false;
        $this->reset(['nombre', 'email', 'password', 'password_confirmation', 'tipoUser']);
        session()->flash('message', 'Usuario creado exitosamente');
    }

    public function editarUsuario($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->nombre = $user->name;
        $this->email = $user->email;
        
        // Mapear user_type a id
        $tipoMapReverse = [
            'Admin' => 1,
            'Operator' => 2,
            'Usuario' => 3
        ];
        $this->tipoUser = $tipoMapReverse[$user->user_type];
        
        $this->resetValidation();
        $this->modalEditUser = true;
    }

    public function updateUser()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'tipoUser' => 'required|in:1,2,3',
        ];
        
        if (!empty($this->password)) {
            $rules['password'] = 'required|min:6|confirmed';
        }
        
        $this->validate($rules);
        
        $tipoMap = [
            1 => 'Admin',
            2 => 'Operator',
            3 => 'Usuario'
        ];
        
        $data = [
            'name' => $this->nombre,
            'email' => $this->email,
            'user_type' => $tipoMap[$this->tipoUser],
        ];
        
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }
        
        User::find($this->userId)->update($data);
        
        $this->modalEditUser = false;
        $this->reset(['nombre', 'email', 'password', 'password_confirmation', 'tipoUser', 'userId']);
        session()->flash('message', 'Usuario actualizado exitosamente');
    }

    public function closeModal()
    {
        $this->modalAddUser = false;
        $this->modalEditUser = false;
        $this->resetValidation();
        $this->reset(['nombre', 'email', 'password', 'password_confirmation', 'tipoUser', 'userId']);
    }

    public function toggleUserStatus($id)
    {
        $user = User::find($id);
        
        if ($user && $user->id !== auth()->id()) {
            $user->is_active = !$user->is_active;
            $user->save();
            
            $estado = $user->is_active ? 'activado' : 'desactivado';
            session()->flash('message', "Usuario {$estado} exitosamente");
        } else {
            session()->flash('error', 'No puedes modificar tu propio estado');
        }
    }

}