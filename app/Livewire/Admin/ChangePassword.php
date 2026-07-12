<?php

namespace App\Livewire\Admin;


use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePassword extends Component
{
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';
    
    public $showForm = false;
    
    protected $rules = [
        'current_password' => 'required|string|min:6',
        'new_password' => 'required|string|min:6|confirmed',
        'new_password_confirmation' => 'required|string|min:6',
    ];
    
    protected $messages = [
        'current_password.required' => 'La contraseña actual es obligatoria',
        'current_password.min' => 'La contraseña debe tener al menos 6 caracteres',
        'new_password.required' => 'La nueva contraseña es obligatoria',
        'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
        'new_password.confirmed' => 'Las contraseñas no coinciden',
    ];
    
    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }
    
    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }
    
    public function updatePassword()
    {
        $this->validate();
        
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'La contraseña actual es incorrecta');
            return;
        }
        
        // Verificar que la nueva contraseña sea diferente a la actual
        if (Hash::check($this->new_password, Auth::user()->password)) {
            $this->addError('new_password', 'La nueva contraseña debe ser diferente a la actual');
            return;
        }
        
        // Actualizar contraseña
        $user = Auth::user();
        $user->password = Hash::make($this->new_password);
        $user->save();
        
        // Mostrar mensaje de éxito
        session()->flash('password_message', 'Contraseña actualizada exitosamente');
        session()->flash('password_type', 'success');
        
        $this->closeForm();
    }
    
    private function resetForm()
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->resetValidation();
    }
    
    public function render()
    {
        return view('livewire.admin.change-password');
    }
}