<div class="w-full p-2 sm:px-5">

    <div class="mt-2 text-2xl font-semibold">Pedido a Proveedores (desde Catálogo)</div>
    <p class="text-sm text-gray-500 mb-3">Costo actualizado (con IVA si la lista viene neta) y pedido mínimo. El carrito admite varios proveedores; filtrá por uno para ver solo lo suyo. El pedido NO toca el stock (se recibe después).</p>

    @if($ultimoPedidoNumero)
        <div class="mb-3 flex flex-wrap items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3 text-sm">
            <span>✓ <b>Pedido #{{ str_pad($ultimoPedidoNumero, 4, '0', STR_PAD_LEFT) }}</b> generado. Está en "Recibir Pedidos de Catálogo".</span>
            <a href="{{ route('stock.pedidoCatalogoInforme', $ultimoPedidoId) }}" target="_blank"
               class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-medium">🖨️ Ver informe</a>
        </div>
    @endif

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
        {{-- CATÁLOGO --}}
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
                        @php $costo = $it->iva_incluido ? (int) $it->precio_costo : (int) round($it->precio_costo * 1.21); @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $it->abreviatura }}{{ $it->codigo ? '-'.$it->codigo : '' }}</td>
                            <td class="px-3 py-2">{{ $it->articulo }}</td>
                            <td class="px-3 py-2 text-right font-semibold">
                                ${{ number_format($costo, 0, ',', '.') }}@unless($it->iva_incluido)<span class="text-[10px] text-gray-400"> c/IVA</span>@endunless
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($it->pedido_minimo)<span class="text-sky-700 font-semibold">{{ $it->pedido_minimo }}</span>@else<span class="text-gray-300">—</span>@endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($it->articulo_id)
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">✓ En stock</span>
                                @else
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nuevo</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="number" min="1" wire:model.defer="cantidades.{{ $it->id }}"
                                       placeholder="{{ $it->pedido_minimo ?: 1 }}"
                                       class="w-16 border rounded px-2 py-1 text-center">
                            </td>
                            <td class="px-3 py-2 text-right">
                                @isset($enCarrito[$it->id])
                                    <button wire:click="agregar({{ $it->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg">✓ Agregado ({{ $enCarrito[$it->id] }})</button>
                                @else
                                    <button wire:click="agregar({{ $it->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium rounded-lg">➕ Agregar</button>
                                @endisset
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay artículos en el catálogo para ese filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $items->links() }}</div>
        </div>

        {{-- CARRITO (por proveedor) --}}
        <div class="lg:w-4/12">
            <div class="bg-white rounded-lg shadow p-4 sticky top-24">
                <h3 class="font-semibold mb-2 flex items-center justify-between">
                    <span>🛒 Pedido</span>
                    <span class="text-sm text-gray-500">{{ $cartCount }} ítem(s)</span>
                </h3>

                @forelse($cartGroups as $g)
                    <div class="mb-4 border rounded-lg p-2">
                        <div class="text-sm font-semibold text-gray-700 mb-1">🏭 {{ $g['proveedor'] }}</div>
                        @foreach($g['items'] as $c)
                            <div class="flex items-center justify-between gap-2 border-b py-1.5 text-sm">
                                <div class="min-w-0">
                                    <div class="truncate">{{ $c->articulo }}</div>
                                    <div class="text-xs text-gray-500">{{ $c->cantidad }} × ${{ number_format((int) $c->costo_ef, 0, ',', '.') }}</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-semibold">${{ number_format((int) $c->subtotal, 0, ',', '.') }}</span>
                                    <button wire:click="quitarCart({{ $c->lista_id }})" class="text-red-500 hover:text-red-700" title="Quitar">✕</button>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center mt-2 pt-1 font-semibold text-sm">
                            <span>Subtotal</span>
                            <span class="text-emerald-600">${{ number_format((int) $g['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <button wire:click="realizarProveedor({{ $g['proveedor_id'] }})"
                                class="mt-2 block w-full text-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-sm">
                            Realizar Pedido →
                        </button>
                        <button wire:click="borrarProveedor({{ $g['proveedor_id'] }})" wire:confirm="¿Borrar el pedido de {{ $g['proveedor'] }}?"
                                class="mt-2 w-full px-4 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg">🗑️ Borrar</button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">Todavía no agregaste nada.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail[0]; type=$event.detail[1]||'success'; setTimeout(()=>show=false,4000)"
         x-show="show" x-transition x-cloak
         class="fixed bottom-4 right-4 p-3 rounded-lg shadow-lg z-[60] text-white"
         :class="{ 'bg-green-600': type==='success', 'bg-yellow-500': type==='warning', 'bg-red-600': type==='error' }">
        <span x-text="message"></span>
    </div>
</div>
