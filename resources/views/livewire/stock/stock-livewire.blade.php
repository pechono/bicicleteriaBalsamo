<div class="w-full px-2 pb-3">

    {{-- ── FILTROS ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3">
        <div class="flex flex-wrap gap-2 items-center">

            {{-- Buscar --}}
            <div class="flex-1 min-w-[180px]">
                <input wire:model.live.debounce.300ms="q" type="search"
                       placeholder="🔍 Buscar artículo, código, detalles..."
                       class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-brand-500 focus:border-brand-500 py-1.5"/>
            </div>

            {{-- Filtro por categoría --}}
            <div class="min-w-[160px]">
                <select wire:model.live="categoria_id"
                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-brand-500 py-1.5">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->categoria }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Toggle activos --}}
            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                <input type="checkbox" wire:model.live="active" value="1"
                       class="rounded border-gray-300 text-brand-600 shadow-sm"/>
                Solo activos
            </label>

            {{-- Contador --}}
            <span class="text-xs text-gray-400 ml-auto">
                {{ $articulos->total() }} artículos
                @if($categoria_id)
                    · <span class="text-brand-500 font-medium">{{ $categorias->firstWhere('id', $categoria_id)?->categoria }}</span>
                @endif
            </span>
        </div>
    </div>

    {{-- ── TABLA (desktop) ─────────────────────────────────────── --}}
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('id')">Id</button>
                            <x-sort-icon sortField='id' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('codigo')">Código</button>
                            <x-sort-icon sortField='codigo' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('articulo')">Artículo</button>
                            <x-sort-icon sortField='articulo' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('descuento')">Desc%</button>
                            <x-sort-icon sortField='descuento' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            U/Venta
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('precioI')">Precio Inicial</button>
                            <x-sort-icon sortField='precioI' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('precioF')">Precio Final</button>
                            <x-sort-icon sortField='precioF' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('stockMinimo')">Stock Minimo</button>
                            <x-sort-icon sortField='stockMinimo' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortby('stock')">Stock</button>
                            <x-sort-icon sortField='stock' :sortBy="$sortBy" :sortAsc="$sortAsc"/>
                        </div>
                    </td>
                    @admin
                    <td class="px-4 py-2">Acción</td>
                    @endadmin
                </tr>
            </thead>
            <tbody>
                @forelse($articulos as $articulo)
                @php $stockBajo = $articulo->stock <= $articulo->stockMinimo; @endphp
                <tr class="{{ $stockBajo ? 'bg-red-50' : '' }}">
                    <td class="rounder border px-4 py-2">{{ $articulo->id }}</td>
                    <td class="rounder border px-4 py-2">{{ $articulo->codigo_proveedor }}{{ $articulo->codigo ? '-'.$articulo->codigo : '' }}</td>
                    <td class="rounder border px-4 py-2">
                        {{ $articulo->articulo }}
                        @if($articulo->detalles && $articulo->detalles !== '')
                            <span class="block text-sm text-gray-400">{{ $articulo->detalles }}</span>
                        @endif
                    </td>
                    <td class="rounder border px-4 py-2">{{ $articulo->descuento }}%</td>
                    <td class="rounder border px-4 py-2">{{ $articulo->unidadVenta }}</td>
                    <td class="rounder border px-4 py-2">{{ $articulo->precioI }}@unless($articulo->iva_incluido)<span class="text-xs text-gray-600 dark:text-gray-300 font-semibold ml-1">+IVA</span>@endunless</td>
                    <td class="rounder border px-4 py-2">{{ $articulo->precioF }}</td>
                    <td class="rounder border px-4 py-2">{{ $articulo->stockMinimo }}</td>
                    <td class="rounder border px-4 py-2">
                        @if($articulo->suelto == 1)
                            <div class="w-8 h-8 p-2 grid justify-items-center content-center bg-green-400 rounded-full">{{ $articulo->stock }}</div>
                        @else
                            {{ $articulo->stock }}
                        @endif
                    </td>
                    @admin
                    <td class="rounder border px-4 py-2">
                        @if($articulo->activo != 1)
                            <button wire:click="ActivarArticuloEdit({{ $articulo->id }})"
                                    class="rounded bg-brand-600 hover:bg-brand-500 text-white h-8 px-3">
                                Activar
                            </button>
                        @else
                            <div class="flex items-center gap-1">
                                <button wire:click="confirmarArticuloEdit({{ $articulo->id }})"
                                        class="rounded bg-brand-600 hover:bg-brand-500 text-white h-8 px-3">
                                    Editar
                                </button>
                                <button wire:click="confirmarArticuloDeletion({{ $articulo->id }})"
                                        class="rounded bg-red-500 hover:bg-red-400 text-white h-8 px-3">
                                    Quitar
                                </button>
                            </div>
                        @endif
                    </td>
                    @endadmin
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-400">No se encontraron artículos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── TARJETAS (mobile) ───────────────────────────────────── --}}
    <div class="md:hidden space-y-2">
        @forelse($articulos as $articulo)
            @php $stockBajo = $articulo->stock <= $articulo->stockMinimo; @endphp
            <div class="rounded-xl border bg-white dark:bg-gray-800 p-3 {{ $stockBajo ? 'border-red-300 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex justify-between gap-2">
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $articulo->articulo }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $articulo->codigo_proveedor }}{{ $articulo->codigo ? '-'.$articulo->codigo : '' }}</div>
                        @if($articulo->detalles && $articulo->detalles !== '')
                            <div class="text-xs text-gray-400">{{ $articulo->detalles }}</div>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-[10px] text-gray-400 uppercase">Stock</div>
                        <div class="text-lg font-bold {{ $stockBajo ? 'text-red-600' : 'text-gray-700 dark:text-gray-200' }}">{{ $articulo->stock }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                    <div><span class="block text-[10px] text-gray-400 uppercase">Costo</span>${{ $articulo->precioI }}@unless($articulo->iva_incluido)<span class="text-[10px] text-gray-500 font-semibold"> +IVA</span>@endunless</div>
                    <div><span class="block text-[10px] text-gray-400 uppercase">Venta</span>${{ $articulo->precioF }}</div>
                    <div><span class="block text-[10px] text-gray-400 uppercase">Desc</span>{{ $articulo->descuento }}%</div>
                </div>
                @admin
                <div class="mt-3 flex gap-2">
                    @if($articulo->activo != 1)
                        <button wire:click="ActivarArticuloEdit({{ $articulo->id }})" class="flex-1 rounded-lg bg-brand-600 hover:bg-brand-500 text-white h-10 font-medium">Activar</button>
                    @else
                        <button wire:click="confirmarArticuloEdit({{ $articulo->id }})" class="flex-1 rounded-lg bg-brand-600 hover:bg-brand-500 text-white h-10 font-medium">Editar</button>
                        <button wire:click="confirmarArticuloDeletion({{ $articulo->id }})" class="flex-1 rounded-lg bg-red-500 hover:bg-red-400 text-white h-10 font-medium">Quitar</button>
                    @endif
                </div>
                @endadmin
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">No se encontraron artículos.</div>
        @endforelse
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
    <x-dialog-modal wire:model.live="activarArt" maxWidth="md">
        <x-slot name="title">✅ Activar artículo</x-slot>
        <x-slot name="content">
            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">{{ $nombreActivar }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                @if(!$iva_incluido)
                    Costo: <span class="font-semibold">${{ number_format($precioI, 2, ',', '.') }}</span>
                    <span class="text-orange-500 text-xs font-semibold">+ IVA</span>
                    = <span class="font-semibold text-gray-700 dark:text-gray-200">${{ number_format($precioI * 1.21, 2, ',', '.') }}</span>
                @else
                    Costo: <span class="font-semibold">${{ number_format($precioI, 2, ',', '.') }}</span>
                @endif
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <x-label value="Grupo (opcional)" />
                    <select wire:model.live="grupoActivar"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                        <option value="">— Sin grupo —</option>
                        @foreach($gruposActivar as $g)
                            <option value="{{ $g->id }}">{{ $g->NombreGrupo }} ({{ $g->porsentaje }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-label value="Margen %" />
                    <x-input type="number" step="0.01" wire:model.live.debounce.400ms="margenActivar"
                        class="mt-1 block w-full" placeholder="ej: 50" />
                    <p class="text-[11px] text-gray-400 mt-0.5">Calcula el precio sobre el costo{{ $iva_incluido ? '' : ' (+IVA)' }}.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <x-label value="Precio de venta" />
                    <x-input type="number" step="1" wire:model="precioF" class="mt-1 block w-full" />
                    <x-input-error for="precioF" class="mt-1" />
                </div>
                <div>
                    <x-label value="Stock" />
                    <x-input type="number" step="1" wire:model="stock" class="mt-1 block w-full" />
                    <x-input-error for="stock" class="mt-1" />
                </div>
                <div>
                    <x-label value="Stock mínimo" />
                    <x-input type="number" step="1" wire:model="stockMinimo" class="mt-1 block w-full" />
                    <x-input-error for="stockMinimo" class="mt-1" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('activarArt', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-2" wire:click="ConfirmarActivar()">Activar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

</div>
