<div>
    <h2 class="text-xl font-bold mb-4">Calendario de Servicios</h2>

    @foreach($dias as $fecha => $servicios)
        <div class="mb-6 p-4 border rounded shadow">

            <h3 class="font-bold text-lg mb-2">
                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
            </h3>

            @foreach($servicios as $servicio)
                <div class="p-2 mb-2 border rounded bg-gray-50">

                    <strong>
                        {{ $servicio->articulo->articulo ?? 'Servicio' }}
                    </strong>

                    <div class="text-sm text-gray-600">
                        Cliente:
                        {{ $servicio->ingreso->bici->cliente->nombre ?? 'N/A' }}
                    </div>

                    <div class="text-sm">
                        Estado:
                        <span class="
                            @if($servicio->estado == 'Pendiente') text-yellow-600
                            @elseif($servicio->estado == 'Terminado') text-green-600
                            @else text-gray-600
                            @endif
                        ">
                            {{ $servicio->estado }}
                        </span>
                    </div>

                </div>
            @endforeach

        </div>
    @endforeach
</div>