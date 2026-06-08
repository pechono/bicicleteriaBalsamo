<div class="w-full px-2 py-3">

    {{-- ─── Barra de filtros ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3 flex flex-wrap items-center gap-3">

        <div class="flex-1 min-w-[200px]">
            <input wire:model.live.debounce.300ms='q'
                   type="search"
                   placeholder="🔍 Buscar pedido o proveedor…"
                   class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
        </div>

        <span class="text-xs text-gray-400 ml-auto">
            {{ $pedidos->count() }} {{ $pedidos->count() == 1 ? 'pedido' : 'pedidos' }}
        </span>

    </div>

    {{-- ─── Tabla ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                        <button wire:click="sortby('id')" class="hover:text-gray-700 transition">N° Pedido</button>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('apellido')" class="hover:text-gray-700 transition">Proveedor</button>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">
                        <button wire:click="sortby('nombre')" class="hover:text-gray-700 transition">Fecha</button>
                    </th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($pedidos as $op)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">

                    {{-- N° Pedido --}}
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            #{{ $op->pedido }}
                        </span>
                    </td>

                    {{-- Proveedor --}}
                    <td class="px-3 py-2">
                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $op->nombre }}</div>
                        @if($op->localidad)
                            <div class="text-xs text-gray-400">{{ $op->localidad }}</div>
                        @endif
                    </td>

                    {{-- Fecha --}}
                    <td class="px-3 py-2 text-xs text-gray-600 whitespace-nowrap">{{ $op->Fecha }}</td>

                    {{-- Acción --}}
                    <td class="px-3 py-2 text-center">
                        <button wire:click='verPed({{ $op->pedido }})'
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100 transition">
                            👁 Ver
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center text-gray-400 text-sm">
                        No hay pedidos realizados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- ─── Modal: Ver detalle de pedido ─── --}}
    <x-dialog-modal wire:model.live="verPedido" maxWidth="2xl">
        <x-slot name="title">
            @if ($verPedido && $pedido)
                Pedido N° {{ $pedido }}
            @else
                Ver Pedido
            @endif
        </x-slot>

        <x-slot name="content">
            @if ($verPedido)
            <div class="space-y-3 text-sm">

                {{-- Info del proveedor --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 grid grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Empresa</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $proveedor }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Localidad</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $localidad }}</p>
                    </div>
                </div>

                {{-- Tabla de artículos del pedido --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Artículo</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($artPedido as $op)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-3 py-2 font-mono text-xs text-gray-500">
                                    {{ $op->codigo_proveedor }}{{ $op->codigo }}
                                </td>
                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                                    {{ $op->articulo }}
                                    @if($op->presentacion && $op->presentacion !== '-')
                                        <span class="text-gray-400"> — {{ $op->presentacion }}</span>
                                    @endif
                                    <span class="text-gray-400"> {{ $op->unidad }}</span>
                                </td>
                                <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $op->cantidad }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            @if ($pedido)
                <a href="{{ route('pedidoImprimir', ['id' => $pedido]) }}" target="_blank"
                   class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                    🖨️ Imprimir
                </a>
            @endif
            <x-secondary-button wire:click="$toggle('verPedido', false)" wire:loading.attr="disabled">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

</div>
