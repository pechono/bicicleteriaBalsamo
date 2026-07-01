<div class="px-6 pb-6">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">🔧 Mano de Obra / Servicios</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Se administran acá, separados de los productos.</p>
        </div>
        <button wire:click="nuevo" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md">
            + Nuevo servicio
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Servicio</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Precio</th>
                    <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-300">Estado</th>
                    <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-300">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($servicios as $s)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200 font-medium">{{ $s->articulo }}</td>
                        <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">${{ number_format($s->precioF, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            @if ($s->activo)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Activo</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center whitespace-nowrap">
                            <button wire:click="editar({{ $s->id }})" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded hover:bg-blue-200">Editar</button>
                            <button wire:click="toggleActivo({{ $s->id }})" class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded hover:bg-gray-200">{{ $s->activo ? 'Desactivar' : 'Activar' }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No hay servicios cargados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal alta/edición --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="$set('mostrarModal', false)">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $editId ? 'Editar servicio' : 'Nuevo servicio' }}</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase">Nombre</label>
                        <input type="text" wire:model="nombre" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase">Precio</label>
                        <input type="number" wire:model="precio" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('precio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                    <button wire:click="$set('mostrarModal', false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded">Cancelar</button>
                    <button wire:click="guardar" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
