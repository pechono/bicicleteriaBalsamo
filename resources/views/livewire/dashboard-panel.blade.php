<div class="p-4 space-y-6 max-w-7xl mx-auto">

    {{-- ── TARJETAS RESUMEN ──────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <a href="{{ route('service.ingresarBike') }}"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">🚲</span>
                <span class="text-xs text-gray-400">Servicios</span>
            </div>
            <p class="text-3xl font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $bikesPendientes }}</p>
            <p class="text-sm text-gray-500 mt-1">Pendientes</p>
        </a>

        <a href="{{ route('service.egresoBici') }}"
            class="bg-white rounded-2xl shadow-sm border border-green-100 p-5 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">✅</span>
                <span class="text-xs text-gray-400">Servicios</span>
            </div>
            <p class="text-3xl font-bold text-green-700 group-hover:text-green-600 transition">{{ $bikesTerminadas }}</p>
            <p class="text-sm text-gray-500 mt-1">Terminadas sin entregar</p>
        </a>

        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">⚠️</span>
                <span class="text-xs text-gray-400">Atención</span>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $bikesVencidas }}</p>
            <p class="text-sm text-gray-500 mt-1">Con fecha vencida</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">💰</span>
                <span class="text-xs text-gray-400">Hoy</span>
            </div>
            <p class="text-3xl font-bold text-emerald-700">${{ number_format($ventasHoy, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-1">Facturado hoy</p>
        </div>

    </div>

    {{-- ── ACCESOS RÁPIDOS ───────────────────────────────── --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('service.ingresarBike') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            🚲 Ingresar Bici
        </a>
        <a href="{{ route('service.egresoBici') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            🔧 Registro Servicio
        </a>
        <a href="{{ route('venta.ventaExpress') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            🚀 Venta Express
        </a>
        <a href="{{ route('service.calendarioServicios') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            📅 Calendario
        </a>
        <a href="{{ route('service.cuentaMecanico') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            💰 Cuenta Mecánico
        </a>
    </div>

    {{-- ── FILA PRINCIPAL ────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Bicis a retirar hoy/mañana --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-700 text-sm">📅 Bicis a retirar</h3>
            </div>
            <div class="divide-y divide-gray-50">

                {{-- Hoy --}}
                @if($bikesHoy->count() > 0)
                <div class="px-5 py-2 bg-blue-50">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wide">HOY</span>
                </div>
                @foreach($bikesHoy as $bici)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $bici->nombre }} {{ $bici->apellido }}</p>
                        <p class="text-xs text-gray-500">{{ $bici->marca }} · {{ $bici->color }} · #{{ str_pad($bici->nro_id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($bici->estado === 'Terminado') bg-green-100 text-green-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $bici->estado }}
                    </span>
                </div>
                @endforeach
                @endif

                {{-- Mañana --}}
                @if($bikesMañana->count() > 0)
                <div class="px-5 py-2 bg-indigo-50">
                    <span class="text-xs font-semibold text-indigo-700 uppercase tracking-wide">MAÑANA</span>
                </div>
                @foreach($bikesMañana as $bici)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $bici->nombre }} {{ $bici->apellido }}</p>
                        <p class="text-xs text-gray-500">{{ $bici->marca }} · {{ $bici->color }} · #{{ str_pad($bici->nro_id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($bici->estado === 'Terminado') bg-green-100 text-green-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $bici->estado }}
                    </span>
                </div>
                @endforeach
                @endif

                @if($bikesHoy->count() === 0 && $bikesMañana->count() === 0)
                <div class="px-5 py-8 text-center text-gray-400 text-sm">Sin retiros para hoy ni mañana</div>
                @endif

            </div>
        </div>

        {{-- Alertas y Mecánico --}}
        <div class="space-y-5">

            {{-- Stock bajo --}}
            @if($stockBajo->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-orange-100 bg-orange-50">
                    <h3 class="font-semibold text-orange-700 text-sm">📦 Stock bajo</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($stockBajo as $item)
                    <div class="px-5 py-2.5 flex items-center justify-between">
                        <p class="text-sm text-gray-700 truncate flex-1">{{ $item->articulo }}</p>
                        <span class="text-xs font-bold text-red-600 ml-3">
                            {{ $item->stock }} / {{ $item->stockMinimo }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Cuenta mecánico --}}
            @if($cuentaMecanico->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-amber-100 bg-amber-50 flex items-center justify-between">
                    <h3 class="font-semibold text-amber-700 text-sm">🔧 Cuenta mecánico (pendiente)</h3>
                    <a href="{{ route('service.cuentaMecanico') }}" class="text-xs text-amber-600 hover:underline">Ver todo →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($cuentaMecanico as $mec)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700">{{ $mec['nombre'] }}</p>
                        <span class="text-sm font-bold text-amber-700">${{ number_format($mec['total'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
