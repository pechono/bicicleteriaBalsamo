<div class="p-4 max-w-4xl mx-auto space-y-6">

    <h2 class="text-2xl font-bold text-gray-800">🔧 Cuenta del Mecánico</h2>

    {{-- ── CARGA MANUAL ────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-700 mb-4">Agregar ítem manualmente</h3>
        <div class="flex flex-wrap gap-3">
            <div class="flex-shrink-0 w-44">
                <select wire:model="mecanicoId"
                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-amber-400 focus:border-amber-400">
                    <option value="">Mecánico...</option>
                    @foreach($mecanicos as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                    @endforeach
                </select>
                @error('mecanicoId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex-1 min-w-[160px]">
                <input wire:model="itemDesc" type="text" placeholder="Descripción (ej: Parche fútbol, Servis...)"
                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-amber-400 focus:border-amber-400"
                    wire:keydown.enter="agregarItem"/>
                @error('itemDesc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="w-32">
                <input wire:model="itemMonto" type="number" min="0" step="0.01" placeholder="$ Monto"
                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-amber-400 focus:border-amber-400"
                    wire:keydown.enter="agregarItem"/>
                @error('itemMonto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button wire:click="agregarItem" type="button"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                + Agregar
            </button>
        </div>
    </div>

    {{-- ── CUENTAS PENDIENTES POR MECÁNICO ────────────────── --}}
    @forelse($cuentas as $cuenta)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header mecánico --}}
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-sm">
                    {{ strtoupper(substr($cuenta['mecanico']->nombre, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $cuenta['mecanico']->nombre }}</p>
                    <p class="text-xs text-gray-500">{{ $cuenta['items']->count() }} ítem(s) pendiente(s)</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Total a pagar</p>
                    <p class="text-xl font-bold text-green-700">${{ number_format($cuenta['total'], 2) }}</p>
                </div>
                @if($cuenta['items']->count() > 0)
                <button wire:click="confirmarCierre({{ $cuenta['mecanico']->id }})"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    💰 Cerrar semana
                </button>
                @endif
            </div>
        </div>

        {{-- Tabla de ítems --}}
        @if($cuenta['items']->count() > 0)
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-2 text-xs text-gray-500 font-medium">Descripción</th>
                    <th class="text-left px-4 py-2 text-xs text-gray-500 font-medium">Vinculado a</th>
                    <th class="text-left px-4 py-2 text-xs text-gray-500 font-medium">Fecha</th>
                    <th class="text-right px-5 py-2 text-xs text-gray-500 font-medium">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($cuenta['items'] as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2.5 text-gray-700">{{ $item->descripcion }}</td>
                    <td class="px-4 py-2.5 text-gray-400 text-xs">
                        @if($item->nro_egreso_id)
                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">Egreso #{{ $item->nro_egreso_id }}</span>
                        @else
                            <span class="text-gray-300">Manual</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-gray-400 text-xs">
                        {{ $item->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-5 py-2.5 text-right font-semibold text-green-700">
                        ${{ number_format($item->monto, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <div class="text-5xl mb-3">✅</div>
        <p class="text-lg font-medium">Sin cuentas pendientes</p>
        <p class="text-sm">Todos los mecánicos están al día</p>
    </div>
    @endforelse

    {{-- ── HISTORIAL DE CIERRES ────────────────────────────── --}}
    @if($historial->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">📋 Últimas liquidaciones</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($historial as $fecha => $items)
            <div class="px-5 py-3">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Cerrado el {{ $fecha }}</span>
                    <span class="text-sm font-bold text-gray-700">${{ number_format($items->sum('monto'), 2) }}</span>
                </div>
                <div class="flex flex-wrap gap-1">
                    @foreach($items->groupBy('mecanico_id') as $mecItems)
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                        {{ $mecItems->first()->mecanico->nombre }}: ${{ number_format($mecItems->sum('monto'), 2) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── MODAL CONFIRMAR CIERRE ───────────────────────────── --}}
    @if($confirmCierre && $mecanicoACerrar)
    @php $nombre = $mecanicos->firstWhere('id', $mecanicoACerrar)?->nombre ?? '' @endphp
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="px-6 py-5 text-center">
                <div class="text-5xl mb-3">💰</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Cerrar semana de {{ $nombre }}</h3>
                <p class="text-sm text-gray-500">Todos los ítems pendientes se marcarán como <strong>pagados</strong>. Esta acción no se puede deshacer.</p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="cancelarCierre"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button wire:click="cerrarSemana"
                    class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                    ✅ Confirmar pago
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
