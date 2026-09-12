<div class="w-full p-2 sm:px-5">

    <div class="mt-2 text-2xl font-semibold">Pedido a Proveedores (desde Catálogo)</div>
    <p class="text-sm text-gray-500 mb-3">Precio de costo actualizado y pedido mínimo (Dal Santo). Lo que no esté en stock, se puede pasar al agregarlo.</p>

    {{-- ── Filtros ── --}}
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <input wire:model.live.debounce.300ms="q" type="search" placeholder="Buscar código o artículo…"
               class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none w-56">
        <select wire:model.live="proveedor_id" class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none">
            <option value="">Todos los proveedores</option>
            @foreach($proveedores as $prov)
                <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">

        {{-- ================= CATÁLOGO ================= --}}
        <div class="lg:w-8/12 bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b text-left">
                        <th class="px-3 py-2">Código</th>
                        <th class="px-3 py-2">Artículo</th>
                        <th class="px-3 py-2 text-right">Costo</th>
                        <th class="px-3 py-2 text-center">Mín.</th>
                        <th class="px-3 py-2 text-center">Stock</th>
                        <th class="px-3 py-2 text-center">Cant.</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($items as $it)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $it->abreviatura }}{{ $it->codigo ? '-'.$it->codigo : '' }}</td>
                            <td class="px-3 py-2">{{ $it->articulo }}</td>
                            <td class="px-3 py-2 text-right font-semibold">${{ number_format((int) $it->precio_costo, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($it->pedido_minimo)
                                    <span class="text-sky-700 font-semibold">{{ $it->pedido_minimo }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($it->articulo_id)
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">✓ En stock</span>
                                @else
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">No está</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="number" min="1" wire:model.defer="cantidades.{{ $it->id }}"
                                       placeholder="{{ $it->pedido_minimo ?: 1 }}"
                                       class="w-16 border rounded px-2 py-1 text-center">
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button wire:click="agregar({{ $it->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium rounded-lg">
                                    ➕ Agregar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay artículos en el catálogo para ese filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $items->links() }}</div>
        </div>

        {{-- ================= CARRITO ================= --}}
        <div class="lg:w-4/12">
            <div class="bg-white rounded-lg shadow p-4 sticky top-24">
                <h3 class="font-semibold mb-2 flex items-center justify-between">
                    <span>🛒 Pedido</span>
                    <span class="text-sm text-gray-500">{{ $inTheCar->count() }} ítem(s)</span>
                </h3>

                @forelse($inTheCar as $c)
                    <div class="flex items-center justify-between gap-2 border-b py-1.5 text-sm">
                        <div class="min-w-0">
                            <div class="truncate">{{ $c->articulo }}</div>
                            <div class="text-xs text-gray-500">{{ $c->cantidad }} × ${{ number_format((int) $c->precioI, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="font-semibold">${{ number_format((int) $c->cantidad * (int) $c->precioI, 0, ',', '.') }}</span>
                            <button wire:click="quitarCar({{ $c->id }})" class="text-red-500 hover:text-red-700" title="Quitar">✕</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">Todavía no agregaste nada.</p>
                @endforelse

                @if($inTheCar->count())
                    <div class="flex justify-between items-center mt-3 pt-2 border-t font-semibold">
                        <span>Total estimado</span>
                        <span class="text-emerald-600">${{ number_format($totalCar, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('stock.confirmarPedido') }}"
                       class="mt-3 block text-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">
                        Realizar Pedido →
                    </a>
                    <button wire:click="vaciarCarrito" wire:confirm="¿Vaciar el pedido?"
                            class="mt-2 w-full px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">
                        Vaciar
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= MODAL: PASAR A STOCK ================= --}}
    @if($promoverId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-5 max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-1">Pasar a stock</h2>
                <p class="text-xs text-gray-500 mb-3">Este artículo no está en stock. Cargalo para poder incluirlo en el pedido.</p>

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="text-xs text-gray-500">Nombre</label>
                        <input type="text" wire:model="pNombre" class="w-full border rounded px-2 py-1">
                        <x-input-error for="pNombre" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Costo (lista)</label>
                        <input type="text" value="${{ number_format((int) $pCosto, 0, ',', '.') }}" disabled class="w-full border rounded px-2 py-1 bg-gray-100">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Categoría</label>
                        <select wire:model="pCategoriaId" class="w-full border rounded px-2 py-1">
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->categoria }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="pCategoriaId" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Grupo</label>
                        <select wire:model.live="pGrupoId" class="w-full border rounded px-2 py-1">
                            <option value="">Elegir…</option>
                            @foreach($gruposPromover as $g)
                                <option value="{{ $g->id }}">{{ $g->NombreGrupo }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="pGrupoId" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">% Ganancia</label>
                        <input type="number" wire:model.live="pPorcentaje" class="w-full border rounded px-2 py-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">% IVA a sumar</label>
                        <input type="number" wire:model.live="pIva" class="w-full border rounded px-2 py-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Precio de venta</label>
                        <div class="flex gap-1">
                            <input type="number" wire:model="pPrecioVenta" class="w-full border rounded px-2 py-1">
                            <button type="button" wire:click="usarPublico" class="px-2 py-1 text-xs bg-gray-200 rounded" title="Usar público de la lista">Púb.</button>
                        </div>
                        <x-input-error for="pPrecioVenta" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Stock inicial</label>
                        <input type="number" wire:model="pStock" class="w-full border rounded px-2 py-1">
                        <x-input-error for="pStock" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Stock mínimo</label>
                        <input type="number" wire:model="pStockMinimo" class="w-full border rounded px-2 py-1">
                        <x-input-error for="pStockMinimo" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="cerrarPromover" class="px-3 py-1.5 text-sm border rounded-lg">Cancelar</button>
                    <button wire:click="confirmarPromover" class="px-4 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">
                        Pasar a stock y agregar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Toast --}}
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail[0]; type=$event.detail[1]||'success'; setTimeout(()=>show=false,4000)"
         x-show="show" x-transition x-cloak
         class="fixed bottom-4 right-4 p-3 rounded-lg shadow-lg z-[60] text-white"
         :class="{ 'bg-green-600': type==='success', 'bg-yellow-500': type==='warning', 'bg-red-600': type==='error' }">
        <span x-text="message"></span>
    </div>
</div>
