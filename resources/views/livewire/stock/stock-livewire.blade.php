<div class="w-full px-2 py-3">

    {{-- ── FILTROS ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3">
        <div class="flex flex-wrap gap-2 items-center">

            {{-- Buscar --}}
            <div class="flex-1 min-w-[180px]">
                <input wire:model.live.debounce.300ms="q" type="search"
                       placeholder="🔍 Buscar artículo, código, detalles..."
                       class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5"/>
            </div>

            {{-- Filtro por categoría --}}
            <div class="min-w-[160px]">
                <select wire:model.live="categoria_id"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-1.5">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->categoria }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Toggle activos --}}
            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                <input type="checkbox" wire:model.live="active" value="1"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm"/>
                Solo activos
            </label>

            {{-- Contador --}}
            <span class="text-xs text-gray-400 ml-auto">
                {{ $articulos->total() }} artículos
                @if($categoria_id)
                    · <span class="text-indigo-500 font-medium">{{ $categorias->firstWhere('id', $categoria_id)?->categoria }}</span>
                @endif
            </span>
        </div>
    </div>

    {{-- ── TABLA ───────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                    <th class="px-3 py-2.5 text-left w-12">
                        <button wire:click="sortby('id')" class="flex items-center gap-1 hover:text-gray-800">
                            ID @if($sortBy==='id') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-left w-24">Código</th>
                    <th class="px-3 py-2.5 text-left">
                        <button wire:click="sortby('articulo')" class="flex items-center gap-1 hover:text-gray-800">
                            Artículo @if($sortBy==='articulo') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-center w-16">
                        <button wire:click="sortby('descuento')" class="hover:text-gray-800">
                            Desc% @if($sortBy==='descuento') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-center w-20">U/Venta</th>
                    <th class="px-3 py-2.5 text-right w-24">
                        <button wire:click="sortby('precioI')" class="hover:text-gray-800">
                            P.Inicial @if($sortBy==='precioI') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-right w-24">
                        <button wire:click="sortby('precioF')" class="hover:text-gray-800">
                            P.Final @if($sortBy==='precioF') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-center w-20">
                        <button wire:click="sortby('stockMinimo')" class="hover:text-gray-800">
                            S.Mín @if($sortBy==='stockMinimo') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    <th class="px-3 py-2.5 text-center w-20">
                        <button wire:click="sortby('stock')" class="hover:text-gray-800">
                            Stock @if($sortBy==='stock') {{ $sortAsc ? '↑' : '↓' }} @endif
                        </button>
                    </th>
                    @admin
                    <th class="px-3 py-2.5 text-center w-20">Acción</th>
                    @endadmin
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($articulos as $articulo)
                @php
                    $stockBajo = $articulo->stock <= $articulo->stockMinimo;
                    $rowClass  = $stockBajo ? 'bg-red-50 dark:bg-red-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40';
                @endphp
                <tr class="{{ $rowClass }} transition">
                    <td class="px-3 py-2 text-gray-400 text-xs">{{ $articulo->id }}</td>
                    <td class="px-3 py-2 font-mono text-xs text-gray-600 dark:text-gray-400">
                        {{ $articulo->codigo_proveedor }}{{ $articulo->codigo ? '-'.$articulo->codigo : '' }}
                    </td>
                    <td class="px-3 py-2 font-medium text-gray-800 dark:text-white">
                        {{ $articulo->articulo }}
                        @if($articulo->detalles && $articulo->detalles !== '')
                            <span class="block text-xs text-gray-400">{{ $articulo->detalles }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $articulo->descuento }}%</td>
                    <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 text-xs">{{ $articulo->unidadVenta }}</td>
                    <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                        ${{ number_format($articulo->precioI, 2, ',', '.') }}
                    </td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-white">
                        ${{ number_format($articulo->precioF, 2, ',', '.') }}
                    </td>
                    <td class="px-3 py-2 text-center text-gray-500 text-xs">{{ $articulo->stockMinimo }}</td>
                    <td class="px-3 py-2 text-center">
                        @if($stockBajo)
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                {{ $articulo->stock }}
                            </span>
                        @elseif($articulo->suelto)
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                {{ $articulo->stock }}
                            </span>
                        @else
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $articulo->stock }}</span>
                        @endif
                    </td>
                    @admin
                    <td class="px-3 py-2">
                        @if($articulo->activo != 1)
                            <button wire:click="ActivarArticuloEdit({{ $articulo->id }})"
                                    class="px-2 py-1 text-xs bg-green-100 hover:bg-green-200 text-green-700 rounded font-semibold transition">
                                Activar
                            </button>
                        @else
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="confirmarArticuloEdit({{ $articulo->id }})"
                                        class="p-1 text-blue-500 hover:bg-blue-50 rounded transition" title="Editar stock">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </button>
                                <button wire:click="confirmarArticuloDeletion({{ $articulo->id }})"
                                        class="p-1 text-red-400 hover:bg-red-50 rounded transition" title="Desactivar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </td>
                    @endadmin
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">📭</div>
                        <p>No se encontraron artículos</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── PAGINACIÓN ──────────────────────────────────────────── --}}
    <div class="mt-3">
        {{ $articulos->links() }}
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL EDITAR STOCK                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <x-dialog-modal wire:model.live="confirmingArticuloEdit" maxWidth="md">
        <x-slot name="title">✏️ Editar Stock</x-slot>
        <x-slot name="content">
            <div class="space-y-3">
                <div class="bg-gray-50 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Artículo</p>
                    <p class="text-gray-800 font-medium mt-0.5">{{ $articulo ?? '' }}</p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">ID: {{ $idArt ?? '' }} · Cód: {{ $codigo ?? '' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Proveedor</label>
                    <select wire:model="proveedor_id" class="mt-1 block w-full text-sm border-gray-300 rounded-lg shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->nombre }} · {{ $prov->localidad }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="proveedor_id" class="mt-1"/>
                </div>
                <div class="flex gap-3">
                    <div class="w-1/2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Mínimo</label>
                        <x-input type="number" wire:model="stockMinimo" class="mt-1 block w-full text-sm"/>
                        <x-input-error for="stockMinimo" class="mt-1"/>
                    </div>
                    <div class="w-1/2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Actual</label>
                        <x-input type="number" wire:model="stock" class="mt-1 block w-full text-sm"/>
                        <x-input-error for="stock" class="mt-1"/>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingArticuloEdit', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-2" wire:click="preguntaCambiarStock({{ $idArt ?? 0 }})">Guardar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL CONFIRMAR CAMBIO STOCK                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <x-dialog-modal wire:model.live="ConfirmarCambioStock" maxWidth="sm">
        <x-slot name="title">⚠️ Confirmar cambio</x-slot>
        <x-slot name="content">
            <p class="text-gray-600">¿Confirma actualizar el stock del artículo <strong>{{ $articulo ?? '' }}</strong>?</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('ConfirmarCambioStock', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-2" wire:click="CambiarStock({{ $ConfirmarCambioStock ?: 0 }})">Actualizar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL DESACTIVAR                                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <x-dialog-modal wire:model.live="confirmingArticuloDeletion" maxWidth="sm">
        <x-slot name="title">🗑️ Desactivar artículo</x-slot>
        <x-slot name="content">
            <p class="text-gray-600">¿Desactivar este artículo? No se eliminará, solo dejará de aparecer en el stock activo.</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingArticuloDeletion', false)">Cancelar</x-secondary-button>
            <x-danger-button class="ms-2" wire:click="deleteArticulo()">Desactivar</x-danger-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL ACTIVAR                                               --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <x-dialog-modal wire:model.live="activarArt" maxWidth="sm">
        <x-slot name="title">✅ Activar artículo</x-slot>
        <x-slot name="content">
            <p class="text-gray-600">¿Activar este artículo nuevamente? Volverá a aparecer en el stock.</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('activarArt', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-2" wire:click="ConfirmarActivar()">Activar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

</div>
