<div class="p-4 max-w-7xl mx-auto space-y-4">

    {{-- ── Cabecera ──────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Proveedores</h1>
                    <p class="text-blue-100 text-sm">Gestión de proveedores</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model.live="active" value="1" class="rounded" />
                    Solo activos
                </label>
                <button wire:click="addModalProveedor"
                    class="bg-white text-blue-700 hover:bg-blue-50 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Proveedor
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tabla ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empresa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rubro</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Localidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IVA incl.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($proveedors as $proveedor)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 font-mono text-xs">{{ $proveedor->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $proveedor->nombre }}</div>
                            @if($proveedor->abreviatura)
                                <div class="text-xs text-gray-400">{{ $proveedor->abreviatura }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $proveedor->telefono }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $proveedor->rubro }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-sm">{{ $proveedor->localidad }}</td>
                        <td class="px-4 py-3">
                            @if($proveedor->iva_incluido)
                                <span class="bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold">Sí</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300 px-2 py-1 rounded-full text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($proveedor->activo)
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">Activo</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs font-semibold">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($proveedor->activo)
                                    <button wire:click="editProveedor({{ $proveedor->id }})"
                                        class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmarProveedorDeletion({{ $proveedor->id }})"
                                        class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Desactivar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="ModalActivarProveedor({{ $proveedor->id }})"
                                        class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition">
                                        Activar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            </svg>
                            No hay proveedores registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Modal Eliminar ────────────────────────────────────── --}}
    <x-dialog-modal wire:model.live="DeleteModal" maxWidth="sm">
        <x-slot name="title">Desactivar Proveedor</x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300">¿Confirmás que querés desactivar este proveedor?</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('DeleteModal', false)">Cancelar</x-secondary-button>
            <x-danger-button class="ms-3" wire:click="delete()">Desactivar</x-danger-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ── Modal Activar ─────────────────────────────────────── --}}
    <x-dialog-modal wire:model.live="activaModal" maxWidth="sm">
        <x-slot name="title">Activar Proveedor</x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300">¿Confirmás que querés activar este proveedor?</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('activaModal', false)">Cancelar</x-secondary-button>
            <x-secondary-button class="ms-3" wire:click="activar()">Activar</x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ── Modal Crear/Editar ────────────────────────────────── --}}
    @foreach(['AddModal' => 'Nuevo Proveedor', 'aditModalProveedor' => 'Editar Proveedor'] as $modalProp => $modalTitulo)
    <x-dialog-modal wire:model.live="{{ $modalProp }}" maxWidth="2xl">
        <x-slot name="title">{{ $modalTitulo }}</x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label value="Empresa *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="nombre" />
                    <x-input-error for="nombre" class="mt-1" />
                </div>
                <div>
                    <x-label value="Abreviatura *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="abreviatura" />
                    <x-input-error for="abreviatura" class="mt-1" />
                </div>
                <div>
                    <x-label value="Teléfono *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="telefono" />
                    <x-input-error for="telefono" class="mt-1" />
                </div>
                <div>
                    <x-label value="Rubro *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="rubro" />
                    <x-input-error for="rubro" class="mt-1" />
                </div>
                <div>
                    <x-label value="Dirección *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="direccion" />
                    <x-input-error for="direccion" class="mt-1" />
                </div>
                <div>
                    <x-label value="Localidad *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="localidad" />
                    <x-input-error for="localidad" class="mt-1" />
                </div>
                <div class="md:col-span-2">
                    <x-label value="Mail *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="mail" />
                    <x-input-error for="mail" class="mt-1" />
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                        <input type="checkbox" wire:model="iva_incluido" class="rounded text-orange-600 w-5 h-5" />
                        <div>
                            <span class="font-semibold text-orange-800 dark:text-orange-300 text-sm">El precio de costo ya incluye IVA (21%)</span>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">Activalo si este proveedor discrimina el IVA en sus precios.</p>
                        </div>
                    </label>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('{{ $modalProp }}', false)">Cancelar</x-secondary-button>
            @if($modalProp === 'AddModal')
                <x-primary-button class="ms-3" wire:click="saveProveedor()">Guardar</x-primary-button>
            @else
                <x-primary-button class="ms-3" wire:click="editSave()">Actualizar</x-primary-button>
            @endif
        </x-slot>
    </x-dialog-modal>
    @endforeach

</div>
