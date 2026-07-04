
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-4">

            {{-- ================= CABECERA ================= --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-3 text-white mb-4 flex items-center gap-3">
                <div class="bg-white/20 p-1.5 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Detalle del Ingreso</h1>
                    <p class="text-blue-100 text-sm">N° de ingreso: <span class="font-mono font-bold">In-{{ $nro_ingreso }}</span></p>
                </div>
            </div>

            {{-- ================= CLIENTE Y BICICLETA ================= --}}
            @if($bicicleta)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                {{-- Cliente --}}
                <div class="rounded-lg border border-gray-200 border-l-4 border-l-blue-500 overflow-hidden">
                    <div class="bg-blue-50 px-3 py-1.5 border-b text-sm font-semibold text-blue-800">👤 Cliente</div>
                    <div class="p-3 text-sm space-y-1">
                        <div class="flex justify-between"><span class="text-gray-500">Nombre</span><span class="font-semibold text-gray-800 text-right">{{ $bicicleta->apellido }} {{ $bicicleta->nombre }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">DNI</span><span class="font-semibold text-gray-800">{{ $bicicleta->dni ?: '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Teléfono</span><span class="font-semibold text-gray-800">{{ $bicicleta->telefono ?: '-' }}</span></div>
                    </div>
                </div>

                {{-- Bicicleta --}}
                <div class="rounded-lg border border-gray-200 border-l-4 border-l-green-500 overflow-hidden">
                    <div class="bg-green-50 px-3 py-1.5 border-b text-sm font-semibold text-green-800">🚲 Bicicleta</div>
                    <div class="p-3 text-sm space-y-1">
                        <div class="flex justify-between"><span class="text-gray-500">Marca</span><span class="font-semibold text-gray-800">{{ $bicicleta->marca }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tipo</span><span class="font-semibold text-gray-800">{{ $bicicleta->tipo }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Color</span><span class="font-semibold text-gray-800">{{ $bicicleta->color }}</span></div>
                    </div>
                </div>
            </div>

            {{-- ================= NOTA GENERAL ================= --}}
            @if($bicicleta->detalles)
            <div class="mb-3 rounded-lg border border-gray-200 border-l-4 border-l-indigo-500 p-3">
                <span class="text-xs font-semibold text-indigo-700 uppercase">📝 Nota general</span>
                <p class="text-sm text-gray-700 mt-1">{{ $bicicleta->detalles }}</p>
            </div>
            @endif
            @endif

            {{-- ================= FECHA ESTIMADA ================= --}}
            <div class="mb-3 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500 flex flex-col sm:flex-row sm:items-center gap-2">
                <span class="font-semibold text-blue-800 text-sm">📅 Fecha estimada de entrega:</span>
                <input
                    type="date"
                    wire:model="fecha_retiro"
                    wire:change="actualizarFechaRetiro"
                    class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    min="{{ date('Y-m-d') }}"
                >
                <span class="text-xs text-gray-500">(se guarda automáticamente)</span>
            </div>

            {{-- ================= PROCESOS ================= --}}
            @if($procesos && count($procesos) > 0)
            <div class="mb-3 rounded-lg border border-gray-200 border-l-4 border-l-purple-500 p-3">
                <span class="text-xs font-semibold text-purple-700 uppercase">🔧 Procesos a realizar</span>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($procesos as $item)
                        <span class="bg-purple-100 text-purple-800 px-2.5 py-0.5 rounded-full text-xs font-medium">
                            {{ $item->articulo }} {{ $item->presentacion }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ================= BOTONES ================= --}}
            <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4 pt-3 border-t">
                @if(!$botonSalir)
                    <button wire:click="enviarWhatsApp" wire:loading.attr="disabled"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                        <span wire:loading.remove wire:target="enviarWhatsApp">📲 Enviar WhatsApp</span>
                        <span wire:loading wire:target="enviarWhatsApp">⏳ Enviando...</span>
                    </button>

                    <a href="{{ route('imprimirIngreso', ['nro_ingreso' => $nro_ingreso]) }}" target="_blank"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                        🖨️ Imprimir
                    </a>

                    <button wire:click="ver"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                        ← Volver
                    </button>
                @else
                    <a href="{{ route('service.ingresarBike') }}"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                        Salir →
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
