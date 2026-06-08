<div class="w-full px-2 py-3">

    {{-- ─── Barra de filtros ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3 flex flex-wrap items-center gap-3">

        <div class="flex-1 min-w-[200px]">
            <input wire:model.live.debounce.300ms='q'
                   type="search"
                   placeholder="🔍 Buscar artículo, código…"
                   class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer select-none">
            <input type="checkbox" wire:model.live='active' value="1"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            Solo activos
        </label>

        @if ($hasRecords > 0)
            <div class="flex items-center gap-2 ml-auto">
                <span class="text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-0.5">
                    {{ $hasRecords }} {{ $hasRecords == 1 ? 'artículo' : 'artículos' }} en pedido
                </span>
                <button wire:click='borrarCar()'
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-sm font-medium transition">
                    🗑 Borrar Pedido
                </button>
                <a href="{{ route('stock.confirmarPedido') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium transition shadow-sm">
                    ✔ Realizar Pedido
                </a>
            </div>
        @endif

    </div>

    {{-- ─── Tabla ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('codigo')" class="hover:text-gray-700 transition">Código</button>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('articulo')" class="hover:text-gray-700 transition">Artículo</button>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Venta</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('precioI')" class="hover:text-gray-700 transition">Precio I</button>
                    </th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('precioF')" class="hover:text-gray-700 transition">Precio F</button>
                    </th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('stockMinimo')" class="hover:text-gray-700 transition">Mín</button>
                    </th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('stock')" class="hover:text-gray-700 transition">Stock</button>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortby('nombre')" class="hover:text-gray-700 transition">Proveedor</button>
                    </th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Cant.</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($articulos as $articulo)
                @php $car = $inTheCar->firstWhere('articulo_id', $articulo->id); @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition {{ $car ? 'bg-indigo-50/50' : '' }}">

                    {{-- Código --}}
                    <td class="px-3 py-2 font-mono text-xs text-gray-500 whitespace-nowrap">
                        @if($articulo->codigo_proveedor || $articulo->codigo)
                            {{ $articulo->codigo_proveedor }}{{ ($articulo->codigo_proveedor && $articulo->codigo) ? '-' : '' }}{{ $articulo->codigo }}
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Artículo --}}
                    <td class="px-3 py-2 max-w-[180px]">
                        <div class="font-medium text-gray-800 dark:text-gray-200 leading-tight">{{ $articulo->articulo }}</div>
                        @if($articulo->detalles)
                            <div class="text-xs text-gray-400 truncate">{{ $articulo->detalles }}</div>
                        @endif
                    </td>

                    {{-- Unidad Venta --}}
                    <td class="px-3 py-2 text-xs text-gray-600 whitespace-nowrap">{{ $articulo->unidadVenta }}</td>

                    {{-- Precio I --}}
                    <td class="px-3 py-2 text-xs text-gray-500 text-right whitespace-nowrap">
                        ${{ number_format($articulo->precioI, 0, ',', '.') }}
                    </td>

                    {{-- Precio F --}}
                    <td class="px-3 py-2 text-xs font-semibold text-gray-800 dark:text-gray-200 text-right whitespace-nowrap">
                        ${{ number_format($articulo->precioF, 0, ',', '.') }}
                    </td>

                    {{-- Stock Mínimo --}}
                    <td class="px-3 py-2 text-xs text-gray-500 text-right">{{ $articulo->stockMinimo }}</td>

                    {{-- Stock actual --}}
                    <td class="px-3 py-2 text-right">
                        @if ($articulo->stock <= $articulo->stockMinimo)
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                {{ $articulo->stock }}
                            </span>
                        @else
                            <span class="text-xs text-gray-700">{{ $articulo->stock }}</span>
                        @endif
                    </td>

                    {{-- Proveedor --}}
                    <td class="px-3 py-2 text-xs text-gray-600 whitespace-nowrap">{{ $articulo->nombre }}</td>

                    {{-- Cantidad en pedido --}}
                    <td class="px-3 py-2 text-center">
                        @if ($car)
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-1.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                {{ $car->cantidad }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Acción --}}
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        @if ($car)
                            <button wire:click="ModCar({{ $articulo->id }})" wire:loading.attr="disabled"
                                    title="Modificar cantidad"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition mr-1">
                                ✏ Mod.
                            </button>
                            <button wire:click="elimCar({{ $articulo->id }})" wire:loading.attr="disabled"
                                    title="Quitar del pedido"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition">
                                ✕
                            </button>
                        @else
                            <button wire:click="addCar({{ $articulo->id }})" wire:loading.attr="disabled"
                                    title="Agregar al pedido"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition">
                                + Solicitar
                            </button>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-400 text-sm">
                        No hay artículos para mostrar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación (descomentar si se activa) --}}
    {{-- <div class="mt-3">{{ $articulos->links() }}</div> --}}


    {{-- ─── Modal: Quitar del pedido ─── --}}
    <x-dialog-modal wire:model.live="eliminar" maxWidth="lg">
        <x-slot name="title">Quitar del Pedido</x-slot>

        <x-slot name="content">
            <div class="space-y-3 text-sm">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 grid grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Código</p>
                        <p class="font-mono font-medium text-gray-800">{{ $codigo_proveedor }}-{{ $codigo }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Artículo</p>
                        <p class="font-medium text-gray-800">{{ $art }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Stock Mínimo</p>
                        <p class="text-gray-700">{{ $stockMinimo }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Stock Actual</p>
                        <p class="text-gray-700">{{ $stock }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Proveedor</p>
                        <p class="text-gray-700">{{ $proveedor }}</p>
                    </div>
                </div>
                <p class="text-gray-600">¿Confirma que desea quitar este artículo del pedido?</p>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('eliminar', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-danger-button class="ms-3" wire:click="eliminarElementCar({{ $id }})" wire:loading.attr="disabled">
                Quitar del Pedido
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>


    {{-- ─── Modal: Agregar / Modificar cantidad ─── --}}
    <x-dialog-modal wire:model.live="agregarCar" maxWidth="lg">
        <x-slot name="title">{{ $msj }}</x-slot>

        <x-slot name="content">
            <div class="space-y-3 text-sm">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 grid grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Código</p>
                        <p class="font-mono font-medium text-gray-800">{{ $codigo_proveedor }}-{{ $codigo }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Artículo</p>
                        <p class="font-medium text-gray-800">{{ $art }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Stock Mínimo</p>
                        <p class="text-gray-700">{{ $stockMinimo }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Stock Actual</p>
                        <p class="text-gray-700">{{ $stock }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Proveedor</p>
                        <p class="text-gray-700">{{ $proveedor }}</p>
                    </div>
                </div>

                <div>
                    <label for="pedido" class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                        Cantidad a solicitar
                    </label>
                    <input id="pedido" wire:model='pedido' type="number" min="1" placeholder="0"
                           class="text-center text-2xl border-gray-300 rounded-lg shadow-sm w-full py-3 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    <x-input-error for="pedido" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('agregarCar', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            @if ($var == 1)
                <x-button class="ms-3" wire:click="crearPedido({{ $id }})" wire:loading.attr="disabled">
                    Agregar al Pedido
                </x-button>
            @else
                <x-button class="ms-3" wire:click="modPedido({{ $id }})" wire:loading.attr="disabled">
                    Modificar Cantidad
                </x-button>
            @endif
        </x-slot>
    </x-dialog-modal>


    {{-- ─── Modal: Confirmar borrar pedido completo ─── --}}
    <x-dialog-modal wire:model.live="borrar">
        <x-slot name="title">Cancelar Pedido Completo</x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600">
                ¿Está seguro de que desea cancelar el pedido completo? Se eliminarán todos los artículos del carrito. Esta acción no se puede deshacer.
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('borrar', false)" wire:loading.attr="disabled">
                No, mantener
            </x-secondary-button>
            <x-danger-button class="ms-3" wire:click="confirmarElimin()" wire:loading.attr="disabled">
                Sí, borrar pedido
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>

</div>
