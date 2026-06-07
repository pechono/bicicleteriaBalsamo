<div class="p-4 max-w-5xl mx-auto space-y-4">

    {{-- Cabecera --}}
    <div class="bg-gradient-to-r from-violet-600 to-violet-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">Cuenta Corriente Mayorista</h1>
                <p class="text-violet-100 text-sm">Historial de ventas y pagos por cliente</p>
            </div>
        </div>
    </div>

    {{-- Selector de cliente --}}
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4">
        <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">👤 Seleccionar cliente</h2>
        <div class="relative max-w-md">
            <input wire:model.live.debounce.300ms="busquedaCliente" type="text"
                placeholder="Buscar por nombre..."
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent" />
            @if(count($resultadosClientes) > 0)
            <div class="absolute z-20 top-full left-0 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl mt-1">
                @foreach($resultadosClientes as $c)
                <button wire:click="seleccionarCliente({{ $c['id'] }})"
                    class="w-full text-left px-4 py-2.5 hover:bg-violet-50 dark:hover:bg-violet-900/20 border-b border-gray-100 dark:border-gray-700 last:border-0 text-sm">
                    {{ $c['nombre'] }}
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if($cliente_id)
    {{-- Resumen saldo --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4 border-l-4 {{ $saldo > 0 ? 'border-red-400' : 'border-emerald-400' }}">
            <div class="text-xs text-gray-500 uppercase tracking-wider">Saldo pendiente</div>
            <div class="text-2xl font-extrabold mt-1 {{ $saldo > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                ${{ number_format(abs($saldo), 2, ',', '.') }}
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ $saldo > 0 ? 'A favor del local' : ($saldo < 0 ? 'Saldo a favor del cliente' : 'Sin deuda') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4 border-l-4 border-blue-400">
            <div class="text-xs text-gray-500 uppercase tracking-wider">Movimientos</div>
            <div class="text-2xl font-extrabold mt-1 text-blue-600">{{ count($movimientos) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-4 flex items-center justify-center">
            <button wire:click="abrirPago"
                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar Pago
            </button>
        </div>
    </div>

    {{-- Movimientos --}}
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Historial de movimientos</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($movimientos as $mov)
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $mov['tipo'] === 'venta' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                        {{ $mov['tipo'] === 'venta' ? '📤' : '💰' }}
                    </div>
                    <div>
                        <div class="font-semibold text-sm text-gray-800 dark:text-white capitalize">
                            {{ $mov['tipo'] }}
                            @if($mov['venta_id']) <span class="text-xs text-gray-400">#{{ $mov['venta_id'] }}</span> @endif
                        </div>
                        @if($mov['observaciones'])<div class="text-xs text-gray-400">{{ $mov['observaciones'] }}</div>@endif
                        <div class="text-xs text-gray-400">{{ $mov['fecha'] }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold {{ $mov['tipo'] === 'venta' ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $mov['tipo'] === 'venta' ? '+' : '-' }}${{ number_format($mov['monto'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-gray-400 text-sm">Sin movimientos</div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- Modal pago --}}
    <x-dialog-modal wire:model.live="modalPago" maxWidth="sm">
        <x-slot name="title">💰 Registrar Pago</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Monto *" />
                    <x-input type="number" step="0.01" min="0.01" class="mt-1 block w-full" wire:model="montoPago" />
                    <x-input-error for="montoPago" class="mt-1" />
                </div>
                <div>
                    <x-label value="Observaciones" />
                    <textarea wire:model="observacionesPago" rows="2"
                        class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 resize-none"
                        placeholder="Opcional..."></textarea>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalPago', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-3" wire:click="registrarPago">Registrar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

</div>
