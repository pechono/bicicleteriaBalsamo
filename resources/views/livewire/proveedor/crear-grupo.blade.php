<div class="p-4 max-w-7xl mx-auto space-y-4">

    {{-- Cabecera --}}
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Grupos por Proveedor</h1>
                    <p class="text-indigo-100 text-sm">Seleccioná un proveedor para crear o ver sus grupos</p>
                </div>
            </div>
            <a href="{{ route('proveedor.proveedor') }}"
                class="bg-white text-indigo-700 hover:bg-indigo-50 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Proveedor
            </a>
        </div>
    </div>

    {{-- Tabla proveedores --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rubro</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Localidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IVA incl.</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($proveedors as $proveedor)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $proveedor->nombre }}</div>
                            @if($proveedor->abreviatura)
                                <div class="text-xs text-gray-400">{{ $proveedor->abreviatura }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $proveedor->telefono }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-full text-xs font-medium">{{ $proveedor->rubro }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $proveedor->localidad }}</td>
                        <td class="px-4 py-3">
                            @if($proveedor->iva_incluido ?? false)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">Sí</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="modalGrupo({{ $proveedor->id }})"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                Gestionar grupos
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">No hay proveedores</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Grupos del Proveedor --}}
    @if ($crearGrupoModal)
    <x-dialog-modal wire:model.live="crearGrupoModal" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                </svg>
                Grupos — {{ $datosPro->nombre }}
            </div>
        </x-slot>
        <x-slot name="content">
            {{-- Info proveedor --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">Rubro</div>
                    <div class="font-semibold text-gray-800 dark:text-white text-sm mt-1">{{ $datosPro->rubro }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">Localidad</div>
                    <div class="font-semibold text-gray-800 dark:text-white text-sm mt-1">{{ $datosPro->localidad }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">Teléfono</div>
                    <div class="font-semibold text-gray-800 dark:text-white text-sm mt-1">{{ $datosPro->telefono }}</div>
                </div>
                <div class="bg-{{ $datosPro->iva_incluido ? 'green' : 'orange' }}-50 rounded-lg p-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">IVA incluido</div>
                    <div class="font-bold text-sm mt-1 text-{{ $datosPro->iva_incluido ? 'green' : 'orange' }}-700">
                        {{ $datosPro->iva_incluido ? '✅ Sí' : '⚠️ No' }}
                    </div>
                </div>
            </div>

            {{-- Crear nuevo grupo --}}
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 mb-5">
                <div class="text-sm font-semibold text-indigo-800 dark:text-indigo-300 mb-3">Crear nuevo grupo</div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <x-label value="Nombre del grupo" />
                        <x-input type="text" class="mt-1 block w-full" wire:model="NombreGrupo" placeholder="Ej: Cubiertas, Frenos..." />
                        <x-input-error for="NombreGrupo" class="mt-1" />
                    </div>
                    <div class="w-full sm:w-36">
                        <x-label value="Porcentaje %" />
                        <x-input type="number" step="0.01" class="mt-1 block w-full" wire:model="porsentaje" placeholder="20" />
                        <x-input-error for="porsentaje" class="mt-1" />
                    </div>
                    <div class="flex items-end">
                        <button wire:click="addGrupo()" wire:loading.attr="disabled"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition w-full sm:w-auto">
                            Crear
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lista de grupos --}}
            <div>
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Grupos existentes</div>
                @forelse ($grupos as $item)
                <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">{{ substr($item->NombreGrupo, 0, 1) }}</span>
                        </div>
                        <span class="font-medium text-gray-800 dark:text-white text-sm">{{ $item->NombreGrupo }}</span>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-3 py-1 rounded-full text-sm font-bold">
                        {{ $item->porsentaje }}%
                    </span>
                </div>
                @empty
                <div class="text-center text-gray-400 py-6 text-sm">No hay grupos para este proveedor</div>
                @endforelse
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('crearGrupoModal', false)">Cerrar</x-secondary-button>
        </x-slot>
    </x-dialog-modal>
    @endif

</div>
