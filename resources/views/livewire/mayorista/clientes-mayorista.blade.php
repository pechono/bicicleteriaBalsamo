<div class="p-4 max-w-6xl mx-auto space-y-4">

    {{-- Cabecera --}}
    <div class="bg-gradient-to-r from-teal-600 to-teal-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Clientes Mayoristas</h1>
                    <p class="text-teal-100 text-sm">Gestión de clientes con precio mayorista</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input wire:model.live.debounce.300ms="busqueda" type="text" placeholder="Buscar..."
                    class="border-0 bg-white/20 text-white placeholder-teal-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-white/50" />
                <button wire:click="nuevo" class="bg-white text-teal-700 hover:bg-teal-50 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">CUIT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">% Extra</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($clientes as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $c['apellido'] }}, {{ $c['nombre'] }}</div>
                            @if($c['email'])<div class="text-xs text-gray-400">{{ $c['email'] }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-mono text-xs">{{ $c['cuit'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $c['telefono'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-teal-100 text-teal-700 px-2 py-1 rounded-full text-xs font-bold">{{ $c['porcentaje_extra'] }}%</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($c['activo'])
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">Activo</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs font-semibold">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="enviarAcceso({{ $c['id'] }})" class="p-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition" title="Enviar acceso al portal por WhatsApp">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                                <button wire:click="editar({{ $c['id'] }})" class="p-1.5 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-lg transition" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($c['activo'])
                                <button wire:click="confirmarEliminar({{ $c['id'] }})" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Desactivar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No hay clientes registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    <x-dialog-modal wire:model.live="modalForm" maxWidth="2xl">
        <x-slot name="title">{{ $editingId ? 'Editar Cliente' : 'Nuevo Cliente Mayorista' }}</x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label value="Nombre *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="nombre" />
                    <x-input-error for="nombre" class="mt-1" />
                </div>
                <div>
                    <x-label value="Apellido *" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="apellido" />
                    <x-input-error for="apellido" class="mt-1" />
                </div>
                <div>
                    <x-label value="Teléfono" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="telefono" />
                </div>
                <div>
                    <x-label value="Email" />
                    <x-input type="email" class="mt-1 block w-full" wire:model="email" />
                    <x-input-error for="email" class="mt-1" />
                </div>
                <div>
                    <x-label value="CUIT" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="cuit" placeholder="20-12345678-9" />
                </div>
                <div>
                    <x-label value="% Extra margen" />
                    <x-input type="number" step="0.01" min="0" class="mt-1 block w-full" wire:model="porcentaje_extra" placeholder="0" />
                    <p class="text-xs text-gray-400 mt-1">Se suma al % del grupo</p>
                    <x-input-error for="porcentaje_extra" class="mt-1" />
                </div>
                <div class="md:col-span-2">
                    <x-label value="Dirección" />
                    <x-input type="text" class="mt-1 block w-full" wire:model="direccion" />
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="cuenta_corriente_habilitada" class="rounded text-teal-600" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Habilitar cuenta corriente (el cliente la ve en su portal)</span>
                    </label>
                </div>
                @if($editingId)
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="activo" class="rounded text-teal-600" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Cliente activo</span>
                    </label>
                </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalForm', false)">Cancelar</x-secondary-button>
            <x-primary-button class="ms-3" wire:click="guardar">Guardar</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal confirmar desactivar --}}
    <x-dialog-modal wire:model.live="modalDelete" maxWidth="sm">
        <x-slot name="title">Desactivar Cliente</x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300">¿Confirmás que querés desactivar este cliente?</p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalDelete', false)">Cancelar</x-secondary-button>
            <x-danger-button class="ms-3" wire:click="eliminar">Desactivar</x-danger-button>
        </x-slot>
    </x-dialog-modal>

    {{-- toast --}}
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail[0]; type=$event.detail[1]||'success'; setTimeout(()=>show=false,4000)"
         x-show="show" x-transition x-cloak
         class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white"
         :class="{ 'bg-green-500': type==='success', 'bg-yellow-500': type==='warning', 'bg-red-500': type==='error' }">
        <p x-text="message"></p>
    </div>

</div>
