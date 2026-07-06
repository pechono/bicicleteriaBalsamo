<div class="px-6 pb-6">
    <div class="mb-5">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">🧾 Actualizar desde factura</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Elegí la empresa y el código, y sumá lo recibido al stock y/o actualizá el precio.</p>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">✅ {{ session('message') }}</div>
    @endif

    {{-- Búsqueda --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Empresa / Proveedor</label>
                <select wire:model.live="proveedor_id" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                    <option value="">— Seleccionar —</option>
                    @foreach ($proveedores as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
                @error('proveedor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Código</label>
                <input type="text" wire:model.live="codigo" wire:keydown.enter="buscar" placeholder="Código del proveedor"
                    class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                @error('codigo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <button wire:click="buscar" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md">🔍 Buscar</button>
            </div>
        </div>
    </div>

    {{-- Resultado --}}
    @if ($encontrado)
        @php
            $nc = (int) round($nuevoCosto ?: 0);
            $delta = $nc - (int) $costoActual;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="mb-4">
                <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $nombre }}</div>
                <div class="text-sm text-gray-500 mt-1 flex flex-wrap gap-x-6 gap-y-1">
                    <span>Stock actual: <b class="text-gray-800 dark:text-gray-200">{{ $stockActual }}</b></span>
                    <span>Costo actual: <b class="text-gray-800 dark:text-gray-200">${{ number_format($costoActual, 0, ',', '.') }}</b></span>
                    <span>Venta actual: <b class="text-gray-800 dark:text-gray-200">${{ number_format($ventaActual, 0, ',', '.') }}</b></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cantidad recibida (se suma al stock)</label>
                    <input type="number" wire:model="cantidadRecibida" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('cantidadRecibida') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-[11px] text-gray-400 mt-1">Quedaría en: {{ $stockActual + (int)($cantidadRecibida ?: 0) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Precio de costo</label>
                    <input type="number" wire:model.live="nuevoCosto" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('nuevoCosto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @if ($delta > 0)
                        <p class="text-[11px] font-semibold text-red-600 mt-1">▲ Aumentó ${{ number_format($delta, 0, ',', '.') }} respecto al actual</p>
                    @elseif ($delta < 0)
                        <p class="text-[11px] font-semibold text-green-600 mt-1">▼ Bajó ${{ number_format(abs($delta), 0, ',', '.') }} respecto al actual</p>
                    @else
                        <p class="text-[11px] text-gray-400 mt-1">Sin cambio de precio</p>
                    @endif
                </div>
            </div>

            <label class="inline-flex items-center mt-3 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" wire:model="ajustarVenta" class="rounded border-gray-300 text-blue-600 mr-2">
                Ajustar el precio de venta manteniendo el mismo margen
            </label>

            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button wire:click="buscar" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-md">Cancelar</button>
                <button wire:click="guardar" wire:loading.attr="disabled" wire:target="guardar"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>
        </div>
    @endif

    {{-- No está en stock, pero sí en el catálogo: dar de alta --}}
    @if ($enCatalogo)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-amber-300 dark:border-amber-700 p-5">
            <div class="mb-4 rounded-md bg-amber-50 border border-amber-200 text-amber-800 px-3 py-2 text-sm">
                No está en stock todavía, pero lo encontré en el <b>catálogo</b> de este proveedor. Completá los datos y dalo de alta con lo recibido.
            </div>

            @if ($grupos->isEmpty())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
                    ⚠️ Este proveedor no tiene <b>grupos</b> definidos (el grupo fija el % de ganancia). Creá al menos uno en
                    <a href="{{ route('proveedor.crearGrupo') }}" class="underline font-semibold">Proveedores → Crear grupo</a> y volvé a buscar.
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nombre del artículo</label>
                <input type="text" wire:model="aNombre" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                @error('aNombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <p class="text-[11px] text-gray-400 mt-1">Código: <b>{{ $codigo }}</b> · Público de lista (ref.): ${{ number_format($aPublicoLista, 0, ',', '.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Costo (de la factura)</label>
                    <input type="number" wire:model.live="aCosto" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('aCosto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Grupo (define la ganancia)</label>
                    <select wire:model.live="aGrupoId" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">— Seleccionar —</option>
                        @foreach ($grupos as $g)
                            <option value="{{ $g->id }}">{{ $g->NombreGrupo }} ({{ $g->porsentaje }}%)</option>
                        @endforeach
                    </select>
                    @error('aGrupoId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">IVA a sumar (%)</label>
                    <input type="number" wire:model.live="aIva" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-[11px] text-gray-400 mt-1">21 si el proveedor discrimina IVA, 0 si ya lo incluye.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Precio de venta</label>
                    <input type="number" wire:model="aPrecioVenta" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('aPrecioVenta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-[11px] text-gray-400 mt-1">= (costo + IVA) + % del grupo. Editable.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cantidad recibida (stock inicial)</label>
                    <input type="number" wire:model="aStock" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('aStock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Stock mínimo</label>
                    <input type="number" wire:model="aStockMinimo" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('aStockMinimo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button wire:click="buscar" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-md">Cancelar</button>
                <button wire:click="darDeAlta" wire:loading.attr="disabled" wire:target="darDeAlta"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md">
                    <span wire:loading.remove wire:target="darDeAlta">Dar de alta</span>
                    <span wire:loading wire:target="darDeAlta">Dando de alta…</span>
                </button>
            </div>
        </div>
    @endif

    {{-- toast --}}
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail[0]; type=$event.detail[1]||'success'; setTimeout(()=>show=false,4000)"
         x-show="show" x-transition x-cloak
         class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white"
         :class="{ 'bg-green-500': type==='success', 'bg-yellow-500': type==='warning', 'bg-red-500': type==='error' }">
        <p x-text="message"></p>
    </div>
</div>
