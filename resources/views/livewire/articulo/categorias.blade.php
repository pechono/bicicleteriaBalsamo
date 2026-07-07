<div class="px-6 pb-6">
    <div class="mb-5">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">📂 Categorías de artículos</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Creá, renombrá o eliminá categorías para ordenar tus artículos.</p>
    </div>

    {{-- Crear --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nueva categoría</label>
        <div class="flex gap-2">
            <input type="text" wire:model="nueva" wire:keydown.enter="crear" placeholder="Nombre de la categoría"
                class="flex-1 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
            <button wire:click="crear" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md">+ Crear</button>
        </div>
        @error('nueva') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Listado --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr class="text-xs uppercase text-gray-500">
                    <th class="px-4 py-2 text-left">Categoría</th>
                    <th class="px-4 py-2 text-center w-32">Artículos</th>
                    <th class="px-4 py-2 text-right w-48">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($categorias as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2">
                            @if ($editId === $c->id)
                                <div class="flex items-center gap-2">
                                    <input type="text" wire:model="editNombre" wire:keydown.enter="guardarEdicion"
                                        class="rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-1">
                                    <button wire:click="guardarEdicion" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded-md">Guardar</button>
                                    <button wire:click="cancelarEdicion" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2.5 py-1 rounded-md">Cancelar</button>
                                </div>
                                @error('editNombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @else
                                <span class="text-gray-800 dark:text-gray-100 font-medium">{{ $c->categoria }}</span>
                                @if (in_array($c->id, $protegidas))
                                    <span class="ml-2 text-[11px] text-gray-400">(protegida)</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-300">{{ $c->cantidad }}</td>
                        <td class="px-4 py-2 text-right">
                            @if ($editId !== $c->id)
                                <button wire:click="editar({{ $c->id }})" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded-md">Renombrar</button>
                                @if (!in_array($c->id, $protegidas))
                                    <button wire:click="eliminar({{ $c->id }})"
                                        wire:confirm="¿Eliminar «{{ $c->categoria }}»? Sus {{ $c->cantidad }} artículo(s) pasarán a General."
                                        class="text-xs bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded-md ml-1">Eliminar</button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- toast --}}
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail[0]; type=$event.detail[1]||'success'; setTimeout(()=>show=false,4000)"
         x-show="show" x-transition x-cloak
         class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white"
         :class="{ 'bg-green-500': type==='success', 'bg-yellow-500': type==='warning', 'bg-red-500': type==='error' }">
        <p x-text="message"></p>
    </div>
</div>
