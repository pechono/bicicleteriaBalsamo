<x-app-layout>
       <div class="min-h-[calc(100vh-4rem)] flex mt-10 justify-center">
        <div class="w-8/12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Información del usuario -->
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <strong>Mi Perfil</strong>
                    <div class="flex items-center space-x-4">
                        
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-lg font-medium mt-2 {{ Auth::user()->user_type === 'Admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                {{ Auth::user()->user_type === 'Admin' ? 'Administrador' : 'Operador' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Componente para cambiar contraseña -->
            @livewire('admin.change-password')
        </div>
    </div>
</x-app-layout>