<div class="p-4 max-w-full">

    {{-- Título --}}
    <h2 class="text-lg font-bold text-gray-800 mb-5">📅 Calendario de Servicios</h2>

    {{-- Dos semanas --}}
    @foreach($semanas as $semana)
    <div class="mb-8">

        {{-- Label de semana --}}
        <div class="flex items-center gap-3 mb-2">
            <span class="font-bold text-gray-700 {{ $loop->first ? 'text-blue-700' : 'text-gray-500' }}">
                {{ $semana['label'] }}
            </span>
            <span class="text-sm text-gray-400">{{ $semana['rango'] }}</span>
        </div>

        {{-- Grilla --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <div class="grid grid-cols-7 min-w-[700px]">

                {{-- Encabezados --}}
                @foreach($semana['dias'] as $dia)
                <div class="text-center py-2 border-b border-gray-200
                    {{ $dia['esHoy'] ? 'bg-blue-500 text-white' : 'bg-gray-50 text-gray-600' }}
                    {{ !$loop->last ? 'border-r border-gray-200' : '' }}">
                    <div class="text-xs font-semibold uppercase tracking-wide">{{ $dia['diaNombre'] }}</div>
                    <div class="text-lg font-bold mt-0.5
                        {{ $dia['esHoy'] ? 'text-white' : ($dia['esPasado'] ? 'text-red-400' : 'text-gray-800') }}">
                        {{ $dia['diaNum'] }}
                    </div>
                    @if($dia['ingresos']->count() > 0)
                    <div class="text-xs mt-0.5
                        {{ $dia['esHoy'] ? 'text-blue-100' : ($dia['ingresos']->count() >= 2 ? 'text-red-500 font-semibold' : 'text-gray-400') }}">
                        {{ $dia['ingresos']->count() }} bici{{ $dia['ingresos']->count() > 1 ? 's' : '' }}
                    </div>
                    @endif
                </div>
                @endforeach

                {{-- Celdas --}}
                @foreach($semana['dias'] as $dia)
                <div class="min-h-[140px] p-1.5
                    {{ $dia['esHoy'] ? 'bg-blue-50' : ($dia['esPasado'] ? 'bg-red-50' : 'bg-white') }}
                    {{ !$loop->last ? 'border-r border-gray-200' : '' }}">

                    @php
                        $paleta = [
                            'bg-purple-100 text-purple-700 border-purple-300',
                            'bg-blue-100 text-blue-700 border-blue-300',
                            'bg-green-100 text-green-700 border-green-300',
                            'bg-yellow-100 text-yellow-700 border-yellow-300',
                            'bg-red-100 text-red-700 border-red-300',
                            'bg-indigo-100 text-indigo-700 border-indigo-300',
                            'bg-pink-100 text-pink-700 border-pink-300',
                            'bg-orange-100 text-orange-700 border-orange-300',
                            'bg-teal-100 text-teal-700 border-teal-300',
                            'bg-cyan-100 text-cyan-700 border-cyan-300',
                        ];
                        $bordeIzq = [
                            'border-l-purple-400', 'border-l-blue-400', 'border-l-green-400',
                            'border-l-yellow-400', 'border-l-red-400', 'border-l-indigo-400',
                            'border-l-pink-400',   'border-l-orange-400', 'border-l-teal-400',
                            'border-l-cyan-400',
                        ];
                    @endphp

                    @foreach($dia['ingresos'] as $ingreso)
                    @php
                        $primerServicio = $ingreso->servicios_grilla->first();
                        $colorIdx = $primerServicio
                            ? abs(crc32($primerServicio->categoria)) % count($paleta)
                            : 0;
                        $bordeColor = $bordeIzq[$colorIdx];
                    @endphp
                    <button wire:click="abrirModal({{ $ingreso->nro_id }})"
                        class="w-full text-left mb-1.5 p-2 rounded-lg border-l-4 border border-gray-200 shadow-sm text-xs cursor-pointer transition hover:shadow-md hover:border-gray-300 bg-white {{ $bordeColor }}">

                        <div class="font-bold text-gray-800 mb-0.5">
                            #{{ str_pad($ingreso->nro_id, 4, '0', STR_PAD_LEFT) }}
                            <span class="font-normal text-gray-500">{{ $ingreso->nombre }}</span>
                        </div>

                        <div class="text-gray-400 truncate mb-1">{{ $ingreso->marca }}</div>

                        {{-- Chips de servicio --}}
                        @if($ingreso->servicios_grilla->count() > 0)
                        <div class="flex flex-wrap gap-0.5">
                            @foreach($ingreso->servicios_grilla as $srv)
                            @php
                                $idx = abs(crc32($srv->categoria)) % count($paleta);
                            @endphp
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium border {{ $paleta[$idx] }}">
                                {{ Str::limit($srv->articulo, 14) }}
                            </span>
                            @endforeach
                        </div>
                        @endif

                    </button>
                    @endforeach

                </div>
                @endforeach

            </div>
        </div>
    </div>
    @endforeach

    {{-- MODAL --}}
    @if($modalAbierto && count($seleccionado))
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         wire:click.self="cerrarModal">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-800 text-white">
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Ingreso</span>
                    <h3 class="text-xl font-bold">#{{ str_pad($seleccionado['nro_id'], 4, '0', STR_PAD_LEFT) }}</h3>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-3 py-1 rounded-full font-semibold
                        @if($seleccionado['estado'] === 'Terminado') bg-green-500 text-white
                        @elseif($seleccionado['estado'] === 'Entregado') bg-blue-500 text-white
                        @else bg-yellow-400 text-gray-900
                        @endif">
                        {{ $seleccionado['estado'] }}
                    </span>
                    <button wire:click="cerrarModal"
                        class="text-gray-400 hover:text-white text-2xl leading-none transition">×</button>
                </div>
            </div>

            {{-- Body modal --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Cliente --}}
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Cliente</p>
                    <p class="font-semibold text-gray-800 text-lg">
                        {{ $seleccionado['nombre'] }} {{ $seleccionado['apellido'] }}
                    </p>
                    @if(!empty($seleccionado['telefono']))
                    <p class="text-sm text-gray-500">📞 {{ $seleccionado['telefono'] }}</p>
                    @endif
                </div>

                {{-- Bicicleta --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Bicicleta</p>
                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <div>
                            <span class="text-gray-400 text-xs">Marca</span>
                            <p class="font-semibold text-gray-700">{{ $seleccionado['marca'] }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs">Color</span>
                            <p class="font-semibold text-gray-700">{{ $seleccionado['color'] }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs">Tipo</span>
                            <p class="font-semibold text-gray-700">{{ $seleccionado['tipo'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Servicios --}}
                @if(!empty($seleccionado['servicios']))
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Servicios</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($seleccionado['servicios'] as $servicio)
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $servicio['categoria'] === 'MdO' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $servicio['articulo'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Fecha retiro --}}
                @if(!empty($seleccionado['fecha_retiro']))
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>📅</span>
                    <span>Retiro estimado:
                        <strong>{{ \Carbon\Carbon::parse($seleccionado['fecha_retiro'])->isoFormat('dddd D [de] MMMM [de] YYYY') }}</strong>
                    </span>
                </div>
                @endif

                {{-- Detalles --}}
                @if(!empty($seleccionado['detalles']))
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Observaciones</p>
                    <p class="text-sm text-gray-600 italic">{{ $seleccionado['detalles'] }}</p>
                </div>
                @endif

            </div>

            {{-- Footer modal --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button wire:click="cerrarModal"
                    class="w-full py-2.5 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-700 transition">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
