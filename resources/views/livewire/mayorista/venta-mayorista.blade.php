<div class="p-4 max-w-7xl mx-auto space-y-4"
     x-data="{}"
     x-on:notify.window="
        let msg = $event.detail[0];
        let type = $event.detail[1] || 'success';
        alert(msg)
     ">

    {{-- ── Cabecera ──────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">Venta Mayorista</h1>
                <p class="text-emerald-100 text-sm">Precios calculados sobre costo + IVA + margen del grupo</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ── Col izquierda: búsqueda + carrito ─────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Búsqueda de artículo --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4">
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">🔍 Buscar artículo</h2>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="busqueda" type="text"
                        placeholder="Nombre o código..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />

                    @if(count($resultados) > 0)
                    <div class="absolute z-30 top-full left-0 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl mt-1 max-h-72 overflow-y-auto">
                        @foreach($resultados as $r)
                        <button wire:click="agregarAlCarrito({{ $r['articulo_id'] }})"
                            class="w-full text-left px-4 py-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border-b border-gray-100 dark:border-gray-700 last:border-0 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-white text-sm">{{ $r['nombre'] }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $r['categoria'] }}
                                        @if($r['grupo']) · {{ $r['grupo'] }} @endif
                                        @if($r['proveedor']) · {{ $r['proveedor'] }} @endif
                                        · Stock: {{ $r['stock'] }}
                                    </div>
                                </div>
                                <div class="text-right ml-4 flex-shrink-0">
                                    <div class="text-xs text-gray-400">
                                        @if(!$r['iva_incluido'])
                                            Costo ${{ number_format($r['precio_costo'], 2, ',', '.') }} <span class="text-orange-500">+IVA</span> = ${{ number_format($r['precio_costo'] * 1.21, 2, ',', '.') }}
                                        @else
                                            Costo ${{ number_format($r['precio_costo'], 2, ',', '.') }}
                                        @endif
                                    </div>
                                    <div class="font-bold text-emerald-700 dark:text-emerald-400">${{ number_format($r['precio_mayorista'], 2, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400">{{ $r['porcentaje'] }}% margen</div>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Carrito --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">🛒 Artículos</h2>
                    <span class="text-xs text-gray-400">{{ count($carrito) }} item(s)</span>
                </div>

                @if(empty($carrito))
                <div class="py-12 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Buscá y agregá artículos</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($carrito as $i => $item)
                    <div class="px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 dark:text-white text-sm truncate">{{ $item['nombre'] }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if(!$item['iva_incluido'])
                                        Costo: ${{ number_format($item['precio_costo'], 2, ',', '.') }}
                                        <span class="text-orange-500">+ IVA</span>
                                        = <span class="font-semibold text-gray-600 dark:text-gray-200">${{ number_format($item['precio_costo'] * 1.21, 2, ',', '.') }}</span>
                                    @else
                                        Costo: ${{ number_format($item['precio_costo'], 2, ',', '.') }}
                                    @endif
                                </div>
                            </div>
                            <button wire:click="quitarDelCarrito({{ $i }})" class="text-red-400 hover:text-red-600 transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 mt-2">
                            {{-- Cantidad --}}
                            <div class="flex items-center gap-1">
                                <label class="text-xs text-gray-400">Cant.</label>
                                <input type="number" step="0.01" min="0.01"
                                    value="{{ $item['cantidad'] }}"
                                    wire:change="actualizarCantidad({{ $i }}, $event.target.value)"
                                    class="w-16 border border-gray-200 dark:border-gray-600 rounded px-2 py-1 text-sm text-center dark:bg-gray-700" />
                            </div>
                            {{-- % --}}
                            <div class="flex items-center gap-1">
                                <label class="text-xs text-gray-400">%</label>
                                <input type="number" step="0.01" min="0"
                                    value="{{ $item['porcentaje'] }}"
                                    wire:change="actualizarPorcentaje({{ $i }}, $event.target.value)"
                                    class="w-16 border border-gray-200 dark:border-gray-600 rounded px-2 py-1 text-sm text-center dark:bg-gray-700" />
                            </div>
                            {{-- Precio mayorista --}}
                            <div class="ml-auto text-right">
                                <div class="text-xs text-gray-400">c/u</div>
                                <div class="font-bold text-emerald-700 dark:text-emerald-400">${{ number_format($item['precio_mayorista'], 2, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">= ${{ number_format($item['precio_mayorista'] * $item['cantidad'], 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ── Col derecha: cliente + resumen + pago ──────────── --}}
        <div class="space-y-4">

            {{-- Cliente --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4">
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">👤 Cliente mayorista</h2>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="busquedaCliente" type="text"
                        placeholder="Buscar cliente..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                    @if(count($resultadosClientes) > 0)
                    <div class="absolute z-20 top-full left-0 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl mt-1">
                        @foreach($resultadosClientes as $c)
                        <button wire:click="seleccionarCliente({{ $c['id'] }})"
                            class="w-full text-left px-3 py-2.5 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border-b border-gray-100 dark:border-gray-700 last:border-0 transition text-sm">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $c['nombre'] }}</div>
                            @if($c['cuit'])<div class="text-xs text-gray-400">CUIT: {{ $c['cuit'] }}</div>@endif
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @if($cliente_id)
                    <div class="mt-2 flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ $busquedaCliente }}</span>
                        <button wire:click="$set('cliente_id', null)" class="ml-auto text-gray-400 hover:text-red-500">✕</button>
                    </div>
                @endif
            </div>

            {{-- Forma de pago --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4">
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">💳 Forma de pago</h2>
                <div class="space-y-2">
                    @foreach(['efectivo' => '💵 Efectivo', 'transferencia' => '🏦 Transferencia', 'cuenta_corriente' => '📋 Cuenta Corriente'] as $val => $label)
                    <label class="flex items-center gap-3 p-2.5 rounded-lg cursor-pointer border transition
                        {{ $tipo_pago === $val ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                        <input type="radio" wire:model="tipo_pago" value="{{ $val }}" class="text-emerald-600" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-3">
                    <label class="text-xs text-gray-500 dark:text-gray-400">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2"
                        class="mt-1 w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white resize-none"
                        placeholder="Opcional..."></textarea>
                </div>
            </div>

            {{-- Resumen + Confirmar --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4">
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">📊 Resumen</h2>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500">Items</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ count($carrito) }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-base font-bold text-gray-800 dark:text-white">Total</span>
                    <span class="text-xl font-extrabold text-emerald-700 dark:text-emerald-400">
                        ${{ number_format($this->totalCarrito(), 2, ',', '.') }}
                    </span>
                </div>
                <button wire:click="confirmar"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition text-sm mt-2 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirmar Venta
                </button>
            </div>
        </div>
    </div>

    {{-- ── Modal confirmar ──────────────────────────────────── --}}
    <x-dialog-modal wire:model.live="modalConfirmar" maxWidth="lg">
        <x-slot name="title">Confirmar Venta Mayorista</x-slot>
        <x-slot name="content">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Cliente:</span><span class="font-semibold">{{ $busquedaCliente }}</span></div>
                    <div class="flex justify-between mt-1"><span class="text-gray-500">Forma de pago:</span><span class="font-semibold capitalize">{{ str_replace('_', ' ', $tipo_pago) }}</span></div>
                    <div class="flex justify-between mt-1 text-base"><span class="font-bold">Total:</span><span class="font-extrabold text-emerald-600">${{ number_format($this->totalCarrito(), 2, ',', '.') }}</span></div>
                </div>
                @if($tipo_pago === 'cuenta_corriente')
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                    ⚠️ Esta venta se registrará en la cuenta corriente del cliente.
                </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalConfirmar', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-3" wire:click="procesarVenta" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="procesarVenta">✅ Confirmar</span>
                <span wire:loading wire:target="procesarVenta">Procesando...</span>
            </x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ── Modal éxito ──────────────────────────────────────── --}}
    <x-dialog-modal wire:model.live="modalExito" maxWidth="sm">
        <x-slot name="title">✅ Venta registrada</x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300 text-sm">La venta mayorista #{{ $ultimaVentaId }} fue procesada correctamente y el stock fue descontado.</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalExito', false)">Cerrar</x-secondary-button>
        </x-slot>
    </x-dialog-modal>

</div>
