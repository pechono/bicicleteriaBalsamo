<div class="p-4 space-y-6 max-w-7xl mx-auto">

    {{-- ── TARJETAS RESUMEN ──────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <a href="{{ route('service.ingresarBike') }}"
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border-l-4 border-blue-500 p-5 hover:shadow-2xl transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full">
                    <span class="text-xl">🚲</span>
                </div>
                <span class="text-xs font-semibold text-blue-500 uppercase tracking-wide">Servicios</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-800 dark:text-white group-hover:text-blue-600 transition">{{ $bikesPendientes }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pendientes</p>
        </a>

        <a href="{{ route('service.egresoBici') }}"
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border-l-4 border-green-500 p-5 hover:shadow-2xl transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-green-100 dark:bg-green-900 p-2 rounded-full">
                    <span class="text-xl">✅</span>
                </div>
                <span class="text-xs font-semibold text-green-500 uppercase tracking-wide">Servicios</span>
            </div>
            <p class="text-3xl font-extrabold text-green-700 dark:text-green-400 group-hover:text-green-600 transition">{{ $bikesTerminadas }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Terminadas sin entregar</p>
        </a>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border-l-4 border-red-500 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-red-100 dark:bg-red-900 p-2 rounded-full">
                    <span class="text-xl">⚠️</span>
                </div>
                <span class="text-xs font-semibold text-red-500 uppercase tracking-wide">Atención</span>
            </div>
            <p class="text-3xl font-extrabold text-red-600 dark:text-red-400">{{ $bikesVencidas }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Con fecha vencida</p>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border-l-4 border-emerald-500 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-emerald-100 dark:bg-emerald-900 p-2 rounded-full">
                    <span class="text-xl">💰</span>
                </div>
                <span class="text-xs font-semibold text-emerald-500 uppercase tracking-wide">Hoy</span>
            </div>
            <p class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">${{ number_format($ventasHoy, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Facturado hoy</p>
        </div>

    </div>

    {{-- ── ACCESOS RÁPIDOS ───────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg p-4">
        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Accesos rápidos</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('service.ingresarBike') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
                🚲 Ingresar Bici
            </a>
            <a href="{{ route('service.egresoBici') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold shadow transition">
                🔧 Registro Servicio
            </a>
            <a href="{{ route('venta.ventaExpress') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow transition">
                🚀 Venta Express
            </a>
            <a href="{{ route('service.calendarioServicios') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                📅 Calendario
            </a>
            <a href="{{ route('service.cuentaMecanico') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold shadow transition">
                💰 Cuenta Mecánico
            </a>
        </div>
    </div>

    {{-- ── FILA PRINCIPAL ────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Bicis a retirar hoy/mañana --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-5 py-3">
                <h3 class="font-semibold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bicis a retirar
                </h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">

                @if($bikesHoy->count() > 0)
                <div class="px-5 py-2 bg-blue-50 dark:bg-blue-900/20">
                    <span class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wide">HOY</span>
                </div>
                @foreach($bikesHoy as $bici)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $bici->nombre }} {{ $bici->apellido }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bici->marca }} · {{ $bici->color }} · #{{ str_pad($bici->nro_id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                        @if($bici->estado === 'Terminado') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                        {{ $bici->estado }}
                    </span>
                </div>
                @endforeach
                @endif

                @if($bikesMañana->count() > 0)
                <div class="px-5 py-2 bg-indigo-50 dark:bg-indigo-900/20">
                    <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide">MAÑANA</span>
                </div>
                @foreach($bikesMañana as $bici)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $bici->nombre }} {{ $bici->apellido }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bici->marca }} · {{ $bici->color }} · #{{ str_pad($bici->nro_id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                        @if($bici->estado === 'Terminado') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                        {{ $bici->estado }}
                    </span>
                </div>
                @endforeach
                @endif

                @if($bikesHoy->count() === 0 && $bikesMañana->count() === 0)
                <div class="px-5 py-10 text-center">
                    <span class="text-3xl">🎉</span>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Sin retiros para hoy ni mañana</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Columna derecha --}}
        <div class="space-y-5">

            {{-- Stock bajo --}}
            @if($stockBajo->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3">
                    <h3 class="font-semibold text-white text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Stock bajo
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($stockBajo as $item)
                    <div class="px-5 py-2.5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate flex-1">{{ $item->articulo }}</p>
                        <span class="text-xs font-bold text-red-600 dark:text-red-400 ml-3 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded-full">
                            {{ $item->stock }} / {{ $item->stockMinimo }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Cuenta mecánico --}}
            @if($cuentaMecanico->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-3 flex items-center justify-between">
                    <h3 class="font-semibold text-white text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Cuenta mecánico (pendiente)
                    </h3>
                    <a href="{{ route('service.cuentaMecanico') }}" class="text-xs text-amber-100 hover:text-white transition">Ver todo →</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($cuentaMecanico as $mec)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                                <span class="text-sm font-bold text-amber-700 dark:text-amber-300">{{ substr($mec['nombre'], 0, 1) }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $mec['nombre'] }}</p>
                        </div>
                        <span class="text-sm font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-3 py-1 rounded-full">
                            ${{ number_format($mec['total'], 2, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
