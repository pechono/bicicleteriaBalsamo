<div class="p-6">
    <!-- Mensajes flash -->
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
            <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Header con botón -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h2>
        <button wire:click="ingresarUsuario" 
                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Usuario</span>
        </button>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150 {{ !$user->is_active ? 'bg-gray-50 opacity-75' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium {{ !$user->is_active ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$user->is_active ? 'text-gray-400' : 'text-gray-500' }}">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $tipoInfo = match($user->user_type) {
                                    'admin', 'Admin' => ['bg-red-100', 'text-red-800', '👑', 'Administrador'],
                                    'employee', 'Operator', 'operador' => ['bg-blue-100', 'text-blue-800', '🔧', 'Operador'],
                                    default => ['bg-gray-100', 'text-gray-800', '👤', 'Usuario']
                                };
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tipoInfo[0] }} {{ $tipoInfo[1] }}">
                                {{ $tipoInfo[2] }} {{ $tipoInfo[3] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? '🟢 Activo' : '🔴 Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- Botón Editar -->
                                <button wire:click="editarUsuario({{ $user->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                
                                <!-- Botón Activar/Desactivar -->
                                @if($user->id !== auth()->id())
                                <button wire:click="toggleUserStatus({{ $user->id }})" 
                                        wire:confirm="¿Estás seguro de que deseas {{ $user->is_active ? 'desactivar' : 'activar' }} a {{ $user->name }}?"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $user->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 focus:ring-yellow-500' : 'bg-green-100 text-green-700 hover:bg-green-200 focus:ring-green-500' }}">
                                    @if($user->is_active)
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Desactivar
                                    @else
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Activar
                                    @endif
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No hay usuarios registrados</p>
                                <button wire:click="ingresarUsuario" 
                                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear primer usuario
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(method_exists($users, 'links'))
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal para crear usuario -->
    @if($modalAddUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center" 
         x-data="{ show: true }" 
         x-show="show"
         x-transition.opacity>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Agregar Nuevo Usuario</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nombre" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-300' }}"
                           placeholder="Ingrese el nombre completo">
                    @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}"
                           placeholder="usuario@ejemplo.com">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña <span class="text-red-500">*</span></label>
                    <input type="password" wire:model="password" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}"
                           placeholder="Mínimo 8 caracteres">
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña <span class="text-red-500">*</span></label>
                    <input type="password" wire:model="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="Repita la contraseña">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de usuario <span class="text-red-500">*</span></label>
                    <select wire:model="tipoUser" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('tipoUser') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Seleccione un tipo</option>
                        @foreach($usertipo as $tipo)
                            <option value="{{ $tipo[0] }}">{{ $tipo[1] }}</option>
                        @endforeach
                    </select>
                    @error('tipoUser') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button wire:click="closeModal" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button wire:click="addUser" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span wire:loading.remove wire:target="addUser">Crear Usuario</span>
                    <span wire:loading wire:target="addUser">Creando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para editar usuario -->
    @if($modalEditUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Editar Usuario</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nombre" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña <span class="text-gray-400 text-xs">(opcional)</span></label>
                    <input type="password" wire:model="password" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}"
                           placeholder="Dejar en blanco para mantener la actual">
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                    <input type="password" wire:model="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de usuario <span class="text-red-500">*</span></label>
                    <select wire:model="tipoUser" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $errors->has('tipoUser') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Seleccione un tipo</option>
                        @foreach($usertipo as $tipo)
                            <option value="{{ $tipo[0] }}">{{ $tipo[1] }}</option>
                        @endforeach
                    </select>
                    @error('tipoUser') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button wire:click="closeModal" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button wire:click="updateUser" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-lg shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span wire:loading.remove wire:target="updateUser">Actualizar Usuario</span>
                    <span wire:loading wire:target="updateUser">Actualizando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
    
    /* Scrollbar personalizada para la tabla */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    /* Transiciones suaves */
    button, a {
        transition: all 0.2s ease-in-out;
    }
    
    /* Focus visible para accesibilidad */
    button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
</style>
</div>

