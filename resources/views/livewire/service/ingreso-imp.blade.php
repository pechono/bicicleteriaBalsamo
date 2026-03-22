
    <div class="pt-20 max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            
            {{-- ================= CABECERA ================= --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-4 text-white mb-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Detalle del Ingreso</h1>
                            <p class="text-blue-100">N° de ingreso: <span class="font-mono font-bold text-xl">In-{{ $nro_ingreso }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= TARJETAS CLIENTE Y BICICLETA ================= --}}
            @if($bicicleta)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Tarjeta Cliente --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500">
                    <div class="bg-gradient-to-r from-blue-50 to-white px-4 py-3 border-b">
                        <h2 class="font-semibold text-blue-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Datos del Cliente
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Nombre completo</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->apellido }} {{ $bicicleta->nombre }}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">DNI</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->dni }}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Teléfono</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->telefono }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta Bicicleta --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
                    <div class="bg-gradient-to-r from-green-50 to-white px-4 py-3 border-b">
                        <h2 class="font-semibold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Datos de la Bicicleta
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Marca</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->marca }}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Tipo</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->tipo }}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Color</span>
                                <p class="font-semibold text-gray-800">{{ $bicicleta->color }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= NOTA GENERAL ================= --}}
            @if($bicicleta->detalles)
            <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-indigo-500">
                <div class="bg-gradient-to-r from-indigo-50 to-white px-4 py-3 border-b">
                    <h2 class="font-semibold text-indigo-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Nota General
                    </h2>
                </div>
                <div class="p-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 leading-relaxed">{{ $bicicleta->detalles }}</p>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- ================= FECHA ESTIMADA ================= --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <h3 class="font-semibold text-lg mb-3 text-blue-800">📅 Fecha estimada de entrega</h3>
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                    <input 
                        type="date" 
                        wire:model="fecha_retiro" 
                        wire:change="actualizarFechaRetiro"
                        class="border rounded-lg px-4 py-2 w-full md:w-auto focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        min="{{ date('Y-m-d') }}"
                    >
                    <span class="text-sm text-gray-600">
                        (Seleccioná la fecha y se guarda automáticamente)
                    </span>
                </div>
            </div>

            {{-- ================= PROCESOS ================= --}}
            @if($procesos && count($procesos) > 0)
            <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
                <div class="bg-gradient-to-r from-purple-50 to-white px-4 py-3 border-b">
                    <h2 class="font-semibold text-purple-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        Procesos a Realizar
                    </h2>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach($procesos as $item)
                            <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $item->articulo }} {{ $item->presentacion }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ================= BOTONES ================= --}}
            <div class="flex flex-col md:flex-row justify-end gap-4 mt-6 pt-4 border-t">
                @if(!$botonSalir)
                    {{-- Botón WhatsApp --}}
                    <button 
                        wire:click="enviarWhatsApp" 
                        wire:loading.attr="disabled"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="enviarWhatsApp">
                            📲 Enviar WhatsApp al Cliente
                        </span>
                        <span wire:loading wire:target="enviarWhatsApp">
                            ⏳ Enviando...
                        </span>
                    </button>

                    {{-- ENLACE DIRECTO PARA IMPRIMIR (COMO ANTES Y SIN ERROR) --}}
                    <a href="{{ route('imprimirIngreso', ['nro_ingreso' => $nro_ingreso]) }}" 
                       target="_blank"
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                        🖨️ Imprimir Comprobante
                    </a>
                    
                    {{-- Botón Volver --}}
                    <button 
                        wire:click="ver" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </button>
                @else
                    {{-- Botón Salir --}}
                    <a 
                        href="{{ route('service.ingresarBike') }}" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Salir
                    </a>
                @endif
            </div>

            {{-- Notificaciones toast --}}
            <div x-data="{ show: false, message: '', type: 'success' }" 
                 x-on:notify.window="show = true; message = $event.detail[0]; type = $event.detail[1] || 'success'; setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition
                 class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50"
                 :class="{ 'bg-green-500 text-white': type === 'success', 'bg-yellow-500 text-white': type === 'warning', 'bg-red-500 text-white': type === 'error' }"
                 x-cloak>
                <p x-text="message"></p>
            </div>
        </div>
    </div>
