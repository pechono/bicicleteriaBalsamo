<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Cambiar Contraseña</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Actualiza tu contraseña para mantener tu cuenta segura</p>
                </div>
            </div>
            
            @if(!$showForm)
                <button wire:click="openForm" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition duration-200 flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Cambiar Contraseña</span>
                </button>
            @endif
        </div>
        
        <!-- Mensajes -->
        @if(session()->has('password_message'))
            <div class="mb-4 p-4 rounded-lg {{ session('password_type') === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-red-100 text-red-700 border border-red-200' }}">
                <div class="flex items-center">
                    @if(session('password_type') === 'success')
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                    <span>{{ session('password_message') }}</span>
                </div>
            </div>
        @endif
        
        <!-- Formulario -->
        @if($showForm)
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <!-- Contraseña Actual -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contraseña Actual
                        </label>
                        <input type="password" 
                               wire:model="current_password" 
                               @class([
                                   'w-full px-4 py-2 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200',
                                   'border-gray-300 dark:border-gray-600' => !$errors->has('current_password'),
                                   'border-red-500' => $errors->has('current_password')
                               ])
                               placeholder="Ingrese su contraseña actual">
                        @error('current_password') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nueva Contraseña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nueva Contraseña
                        </label>
                        <input type="password" 
                               wire:model="new_password" 
                               @class([
                                   'w-full px-4 py-2 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200',
                                   'border-gray-300 dark:border-gray-600' => !$errors->has('new_password'),
                                   'border-red-500' => $errors->has('new_password')
                               ])
                               placeholder="Ingrese su nueva contraseña">
                        @error('new_password') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirmar Contraseña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirmar Nueva Contraseña
                        </label>
                        <input type="password" 
                               wire:model="new_password_confirmation" 
                               @class([
                                   'w-full px-4 py-2 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200',
                                   'border-gray-300 dark:border-gray-600' => !$errors->has('new_password_confirmation'),
                                   'border-red-500' => $errors->has('new_password_confirmation')
                               ])
                               placeholder="Confirme su nueva contraseña">
                        @error('new_password_confirmation') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" 
                                wire:click="closeForm" 
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition duration-200 flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Actualizar Contraseña</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
        
        <!-- Información de seguridad -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Recomendaciones de seguridad:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Usa al menos 8 caracteres</li>
                        <li>Combina letras mayúsculas, minúsculas, números y símbolos</li>
                        <li>No uses la misma contraseña que en otros sitios</li>
                        <li>Cambia tu contraseña periódicamente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>