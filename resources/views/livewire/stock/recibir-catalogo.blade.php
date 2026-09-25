<div class="w-full p-2 sm:px-5">

    @php
        $badge = fn($e) => match($e) {
            'recibido' => 'bg-emerald-100 text-emerald-700',
            'parcial'  => 'bg-sky-100 text-sky-700',
            default    => 'bg-amber-100 text-amber-700',
        };
    @endphp

    @if(!$orden)
        {{-- ================= LISTA DE PEDIDOS ================= --}}
        <div class="mt-2 text-2xl font-semibold">Recibir Pedidos de Catálogo</div>
        <p class="text-sm text-gray-500 mb-3">Cuando llega la mercadería, entrá al pedido y dale ingreso a cada artículo (ahí recién va al stock).</p>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b text-left">
                        <th class="px-3 py-2">N°</th>
                        <th class="px-3 py-2">Proveedor</th>
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2 text-center">Recibidos</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($ordenes as $o)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-semibold">#{{ str_pad($o->numero, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-3 py-2">{{ $o->proveedor }}</td>
                            <td class="px-3 py-2">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 text-center">{{ $o->recibidos_count }}/{{ $o->items_count }}</td>
                            <td class="px-3 py-2 text-center"><span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge($o->estado) }}">{{ ucfirst($o->estado) }}</span></td>
                            <td class="px-3 py-2 text-right">
                                <button wire:click="verOrden({{ $o->id }})" class="px-3 py-1.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium rounded-lg">Ver / Recibir</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">Todavía no hay pedidos de catálogo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        {{-- ================= DETALLE / RECIBIR ================= --}}
        <div class="flex items-center justify-between mt-2 mb-1">
            <div class="text-2xl font-semibold">Pedido #{{ str_pad($orden->numero, 4, '0', STR_PAD_LEFT) }}</div>
            <button wire:click="cerrarOrden" class="text-sm text-gray-500 hover:text-gray-800">← Volver a la lista</button>
        </div>
        <div class="text-sm text-gray-600 mb-3">
            Proveedor: <b>{{ $orden->proveedor }}</b> ·
            Estado: <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge($orden->estado) }}">{{ ucfirst($orden->estado) }}</span>
            <button wire:click="enviarWhatsApp({{ $orden->id }})" wire:confirm="¿Enviar el pedido al proveedor por WhatsApp?"
                    class="ml-2 px-3 py-1 bg-green-600 hover:bg-green-500 text-white text-xs font-medium rounded-lg">
                📲 {{ $orden->enviado ? 'Reenviar' : 'Enviar' }} al proveedor
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b text-left">
                        <th class="px-3 py-2">Código</th>
                        <th class="px-3 py-2">Artículo</th>
                        <th class="px-3 py-2 text-center">Cant.</th>
                        <th class="px-3 py-2 text-right">Costo</th>
                        <th class="px-3 py-2 text-center">Ingreso</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($items as $it)
                        <tr class="{{ $it->recibido ? 'bg-emerald-50/40' : '' }}">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $it->abreviatura }}{{ $it->codigo ? '-'.$it->codigo : '' }}</td>
                            <td class="px-3 py-2">{{ $it->articulo }} @if(!$it->stock_articulo_id)<span class="text-xs text-gray-400">(nuevo)</span>@endif</td>
                            <td class="px-3 py-2 text-center">{{ $it->cantidad }}</td>
                            <td class="px-3 py-2 text-right">${{ number_format((int) $it->precio_costo, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($it->recibido)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">✓ Ingresado</span>
                                @else
                                    <button wire:click="darIngreso({{ $it->id }})"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg">Dar ingreso</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ================= MODAL: DAR INGRESO (ítem nuevo → pasar a stock) ================= --}}
    @if($recibiendoItemId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-5 max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-1">Dar ingreso a stock</h2>
                <p class="text-xs text-gray-500 mb-3">Artículo nuevo. Se crea en stock con la cantidad recibida.</p>

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
                            @foreach($gruposModal as $g)
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
                        <label class="text-xs text-gray-500">Stock que ingresa</label>
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
                    <button wire:click="cerrarModal" class="px-3 py-1.5 text-sm border rounded-lg">Cancelar</button>
                    <button wire:click="confirmarIngreso" class="px-4 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">Dar ingreso</button>
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
