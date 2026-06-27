<div class="px-4 pb-4 sm:px-6 sm:pb-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">📦 Artículos por Grupo</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Elegí un proveedor, seleccioná un grupo y asociá o quitá artículos.</p>
        </div>
        <a href="{{ route('proveedor.proveedor') }}" class="text-sm bg-blue-700 hover:bg-blue-600 text-white px-3 py-2 rounded-md">+ Proveedor</a>
    </div>

    {{-- Paso 1: elegir proveedor --}}
    <div class="max-w-md mb-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proveedor</label>
        <select wire:model.live="proveedorId"
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
            <option value="">— Seleccionar proveedor —</option>
            @foreach ($proveedores as $prov)
                <option value="{{ $prov->id }}">{{ $prov->nombre }}{{ $prov->abreviatura ? ' ('.$prov->abreviatura.')' : '' }}</option>
            @endforeach
        </select>
    </div>

    @if ($proveedorId)
        {{-- Paso 2: grupos del proveedor --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Grupos del proveedor</h3>
                <button wire:click="abrirCrearGrupo"
                    class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md">+ Crear grupo</button>
            </div>

            @if ($grupos->isEmpty())
                <p class="text-sm text-gray-400 italic">Este proveedor no tiene grupos todavía. Creá uno.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($grupos as $g)
                        <button wire:click="seleccionarGrupo({{ $g->id }})"
                            class="px-3 py-2 rounded-lg border text-sm transition
                                {{ $grupoId == $g->id
                                    ? 'bg-emerald-600 border-emerald-600 text-white'
                                    : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:border-emerald-400' }}">
                            <span class="font-medium">{{ $g->NombreGrupo }}</span>
                            <span class="opacity-75">· {{ $g->porsentaje }}%</span>
                            <span class="ml-1 inline-block text-xs px-1.5 rounded-full
                                {{ $grupoId == $g->id ? 'bg-white/25' : 'bg-gray-100 dark:bg-gray-600' }}">{{ $g->cantidad }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Paso 3: gestionar artículos del grupo seleccionado --}}
    @if ($grupo)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- En el grupo --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/30 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                        En el grupo «{{ $grupo->NombreGrupo }}» ({{ $enGrupo->count() }})
                    </h4>
                </div>
                <div class="max-h-[55vh] overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($enGrupo as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-2">
                                        <div class="text-gray-800 dark:text-gray-100">{{ $a->articulo }}</div>
                                        <div class="text-xs text-gray-400">{{ $a->codigo }} · {{ $a->categoria }} · ${{ $a->precioF }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right w-24">
                                        <button wire:click="quitar({{ $a->id }})"
                                            class="text-xs bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded-md">Quitar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="px-3 py-6 text-center text-gray-400 italic">Todavía no hay artículos en este grupo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Disponibles --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Disponibles del proveedor
                        <span class="text-gray-400 font-normal">({{ $totalDisponibles }})</span>
                    </h4>
                    <input type="text" wire:model.live.debounce.350ms="buscar"
                        placeholder="Buscar por nombre o código…"
                        class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                <div class="max-h-[55vh] overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($disponibles as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-2">
                                        <div class="text-gray-800 dark:text-gray-100">{{ $a->articulo }}</div>
                                        <div class="text-xs text-gray-400">{{ $a->codigo }} · {{ $a->categoria }} · ${{ $a->precioI }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right w-24">
                                        <button wire:click="agregar({{ $a->id }})"
                                            class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded-md">Agregar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="px-3 py-6 text-center text-gray-400 italic">
                                    @if ($buscar) Nada coincide con «{{ $buscar }}». @else No hay artículos disponibles para este proveedor. @endif
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($totalDisponibles > $disponibles->count())
                        <div class="px-3 py-2 text-xs text-center text-gray-400 border-t border-gray-100 dark:border-gray-700">
                            Mostrando {{ $disponibles->count() }} de {{ $totalDisponibles }}. Afiná la búsqueda para ver más.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif ($proveedorId)
        <p class="text-sm text-gray-400 italic">Seleccioná un grupo para gestionar sus artículos.</p>
    @endif

    {{-- Modal crear grupo --}}
    <x-dialog-modal wire:model.live="crearGrupoModal" maxWidth="md">
        <x-slot name="title">Crear grupo</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="nombreGrupo" value="Nombre del grupo" />
                    <x-input id="nombreGrupo" type="text" class="mt-1 block w-full" wire:model="nombreGrupo" />
                    <x-input-error for="nombreGrupo" class="mt-1" />
                </div>
                <div>
                    <x-label for="porsentaje" value="Porcentaje de margen (%)" />
                    <x-input id="porsentaje" type="number" step="0.01" class="mt-1 block w-full" wire:model="porsentaje" />
                    <x-input-error for="porsentaje" class="mt-1" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('crearGrupoModal', false)">Cancelar</x-secondary-button>
            <x-button class="ms-3" wire:click="crearGrupo" wire:loading.attr="disabled">Crear</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
