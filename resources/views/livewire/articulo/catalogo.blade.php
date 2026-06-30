<div class="px-6 pb-6">
    <div class="mb-5">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">🗂️ Catálogo de listas</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Productos de las listas de proveedores. <strong>No ocupan lugar en tus artículos</strong> hasta que los
            "pasás a artículos" (ahí se crea el artículo con stock). Lo ya pasado queda marcado <span class="text-green-600 font-semibold">En stock</span>.
        </p>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">✅ {{ session('message') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar por código o nombre…"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
            </div>
            <select wire:model.live="proveedor_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                <option value="">Todos los proveedores</option>
                @foreach ($proveedores as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                @endforeach
            </select>
        </div>
        <label class="inline-flex items-center mt-3 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" wire:model.live="soloPendientes" class="rounded border-gray-300 text-blue-600 mr-2">
            Mostrar solo los que todavía no pasé a artículos
        </label>
    </div>

    {{-- Recalcular cotización dólar --}}
    @if ($usdCount > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 mb-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">💵 Ítems en dólares: {{ $usdCount }}
                        @if ($usdCotiz) <span class="font-normal">(cotización actual: ${{ number_format($usdCotiz, 2, ',', '.') }})</span> @endif
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-300">Actualizá la cotización y recalculo los precios en pesos {{ $proveedor_id ? 'de este proveedor' : '(de todos)' }}.</p>
                </div>
                <div>
                    <input type="number" step="0.01" wire:model="nuevaCotizacion" placeholder="Nueva cotización"
                        class="w-40 rounded-md border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                    @error('nuevaCotizacion') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
                <button wire:click="recalcularCotizacion" wire:loading.attr="disabled" wire:target="recalcularCotizacion"
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-md">
                    <span wire:loading.remove wire:target="recalcularCotizacion">Recalcular</span>
                    <span wire:loading wire:target="recalcularCotizacion">Recalculando…</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Código</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Artículo</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Costo</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Público</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-300">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($items as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-2 font-mono text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->abreviatura ? $row->abreviatura.'-'.$row->codigo : $row->codigo }}
                                @if ($row->moneda === 'USD') <span class="text-amber-600">(USD)</span> @endif
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $row->articulo }}</td>
                            <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">${{ number_format($row->precio_costo, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">${{ number_format($row->precio_publico, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-center">
                                @if ($row->articulo_id)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ En stock</span>
                                @else
                                    <button wire:click="abrirPromover({{ $row->id }})"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded">
                                        Pasar a artículos
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay ítems en el catálogo con esos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $items->links() }}</div>
    </div>

    {{-- Modal pasar a artículos --}}
    @if ($promoverId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="cerrarPromover">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pasar a artículos</h3>
                    <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $pCodigo }}</p>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase">Grupo *</label>
                        <select wire:model="pGrupoId" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">— Elegí el grupo —</option>
                            @foreach ($gruposPromover as $g)
                                <option value="{{ $g->id }}">{{ $g->NombreGrupo }}</option>
                            @endforeach
                        </select>
                        @error('pGrupoId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @if ($gruposPromover->isEmpty())
                            <p class="text-amber-600 text-xs mt-1">Este proveedor no tiene grupos. Creá uno en la pantalla de Artículos/Grupos.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase">Nombre</label>
                        <input type="text" wire:model="pNombre" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('pNombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Costo</label>
                            <input type="number" value="{{ $pCosto }}" disabled class="mt-1 block w-full text-sm rounded border-gray-200 bg-gray-100 dark:bg-gray-900 dark:border-gray-700 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Precio de venta *</label>
                            <input type="number" wire:model="pPrecioVenta" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('pPrecioVenta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Stock *</label>
                            <input type="number" wire:model="pStock" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('pStock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Stock mínimo *</label>
                            <input type="number" wire:model="pStockMinimo" class="mt-1 block w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('pStockMinimo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                    <button wire:click="cerrarPromover" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded">Cancelar</button>
                    <button wire:click="confirmarPromover" wire:loading.attr="disabled" wire:target="confirmarPromover"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded">
                        <span wire:loading.remove wire:target="confirmarPromover">Crear artículo con stock</span>
                        <span wire:loading wire:target="confirmarPromover">Creando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
