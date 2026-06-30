<div class="px-6 pb-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">📥 Importar Lista de Precios</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Subí la lista del proveedor y elegí el <strong>formato</strong>, el <strong>proveedor</strong> y el <strong>grupo</strong>.
            Los artículos nuevos se crean <strong>inactivos</strong> para que los actives manualmente. Los existentes
            (mismo código para ese proveedor) solo actualizan su precio. Si la lista está <strong>en dólares</strong>, completá la cotización.
        </p>
    </div>

    {{-- Mensaje de éxito --}}
    @if (session('message'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm dark:bg-green-900/40 dark:text-green-200 dark:border-green-700">
            ✅ {{ session('message') }}
        </div>
    @endif

    {{-- Resultado detallado --}}
    @if ($resultado)
        <div class="mb-4 grid grid-cols-2 gap-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 dark:bg-blue-900/30 dark:border-blue-700">
                <p class="text-xs text-blue-600 dark:text-blue-300 uppercase tracking-wide">Nuevos</p>
                <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ $resultado['creados'] }}</p>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 dark:bg-amber-900/30 dark:border-amber-700">
                <p class="text-xs text-amber-600 dark:text-amber-300 uppercase tracking-wide">Precios actualizados</p>
                <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ $resultado['actualizados'] }}</p>
            </div>
        </div>
    @endif

    {{-- Formulario de carga --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Formato de lista</label>
                <select wire:model="formato"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    <option value="">— Seleccionar —</option>
                    @foreach ($formatos as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('formato') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proveedor</label>
                <select wire:model="proveedor_id"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    <option value="">— Seleccionar —</option>
                    @foreach ($proveedores as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                    @endforeach
                </select>
                @error('proveedor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cotización dólar <span class="text-gray-400 font-normal">(solo si la lista está en USD)</span>
                </label>
                <input type="number" step="0.01" wire:model.live="cotizacion" placeholder="Ej: 1200 (dejar vacío si está en pesos)"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                @error('cotizacion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Archivo (.xlsx / .pdf)</label>
                <input type="file" wire:model="archivo" accept=".xlsx,.xls,.pdf"
                    class="block w-full text-sm text-gray-600 dark:text-gray-300
                           file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0
                           file:text-sm file:font-semibold file:bg-green-50 file:text-green-700
                           hover:file:bg-green-100">
                @error('archivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <div wire:loading wire:target="archivo" class="text-xs text-gray-400 mt-1">Subiendo archivo…</div>
            </div>
        </div>

        <div class="mt-4 flex gap-3">
            <button wire:click="analizar" wire:loading.attr="disabled" wire:target="analizar,archivo"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md disabled:opacity-50">
                <span wire:loading.remove wire:target="analizar">🔍 Analizar archivo</span>
                <span wire:loading wire:target="analizar">Analizando…</span>
            </button>
            @if ($total > 0)
                <button wire:click="cancelar"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-md dark:bg-gray-600 dark:text-gray-200">
                    Cancelar
                </button>
            @endif
        </div>
    </div>

    {{-- Vista previa --}}
    @if ($total > 0)
        @php $factor = ($cotizacion && (float)$cotizacion > 0) ? (float)$cotizacion : 1; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                    Vista previa — {{ $total }} artículo(s)
                    @if ($total > count($preview))
                        <span class="text-gray-400 font-normal">(mostrando primeros {{ count($preview) }})</span>
                    @endif
                    @if ($factor > 1)
                        <span class="text-green-600 font-normal">· precios × {{ number_format($factor, 2, ',', '.') }} (USD→$)</span>
                    @endif
                </p>
                <button wire:click="confirmar" wire:loading.attr="disabled" wire:target="confirmar"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmar">✅ Confirmar importación</span>
                    <span wire:loading wire:target="confirmar">Importando… no cierres la página</span>
                </button>
            </div>
            <div class="overflow-x-auto max-h-[60vh]">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Código prov.</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Artículo</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Costo</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Público</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($preview as $row)
                            @php
                                $pi = round(($row['precioI'] ?? $row['precio'] ?? 0) * $factor);
                                $pf = round(($row['precioF'] ?? $row['precio'] ?? 0) * $factor);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $abreviatura ? $abreviatura.'-'.$row['codigo'] : $row['codigo'] }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $row['articulo'] }}</td>
                                <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">${{ number_format($pi, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">${{ number_format($pf, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
