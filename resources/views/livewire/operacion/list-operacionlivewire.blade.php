<div class="p-2 sm:px-5 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    <div class="mt-4 text-2xl flex justify-between shadow-inner">
        <div>Operaciones Realizadas</div>
        <button wire:click="limpiarFiltros" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Limpiar Filtros
        </button>
    </div>

    <div class="mt-3">
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between">
                <div class="w-1/3">
                    <input wire:model.live.debounce.300ms='q' type="search" placeholder="Buscar por ID, cliente o usuario..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline placeholder-blue-400">
                </div>
                
                @if($msj)
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-2 rounded">
                        {{ $msj }}
                    </div>
                @endif
            </div>
            
            <div class="flex space-x-2">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Dia ▼</button>
                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 bg-white rounded-lg shadow-lg p-4 w-64">
                        <label class="block text-gray-700">Elegir Día</label>
                        <input type="date" wire:model.live='Dia' class="w-full rounded-lg mt-1 border-gray-300">
                        <button wire:click="cancelarD" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm w-full">Aplicar</button>
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Entre Días ▼</button>
                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 bg-white rounded-lg shadow-lg p-4 w-80">
                        <div class="space-y-2">
                            <div>
                                <label class="block text-gray-700">Fecha Inicio</label>
                                <input type="date" wire:model.live='fechaI' class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-gray-700">Fecha Fin</label>
                                <input type="date" wire:model.live='fechaF' class="w-full rounded-lg border-gray-300">
                            </div>
                            <button wire:click="cancelarDE" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm w-full">Aplicar</button>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Mes ▼</button>
                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 bg-white rounded-lg shadow-lg p-4 w-64">
                        <div class="space-y-2">
                            <div>
                                <label class="block text-gray-700">Seleccionar Mes</label>
                                <select wire:model.live="mes" class="w-full rounded-lg border-gray-300">
                                    <option value="">Seleccionar</option>
                                    @foreach($meses as $numeroMes => $nombreMes)
                                        <option value="{{ $numeroMes }}">{{ $nombreMes }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700">Seleccionar Año</label>
                                <select wire:model.live="anio" class="w-full rounded-lg border-gray-300">
                                    <option value="">Seleccionar</option>
                                    @foreach($aniosUnicos as $aniosUnico)
                                        <option value="{{ $aniosUnico }}">{{ $aniosUnico }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button wire:click="cancelarM" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm w-full">Aplicar</button>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Año ▼</button>
                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 bg-white rounded-lg shadow-lg p-4 w-64">
                        <label class="block text-gray-700">Seleccionar Año</label>
                        <select wire:model.live="anio" class="w-full rounded-lg border-gray-300">
                            <option value="">Seleccionar</option>
                            @foreach($aniosUnicos as $aniosUnico)
                                <option value="{{ $aniosUnico }}">{{ $aniosUnico }}</option>
                            @endforeach
                        </select>
                        <button wire:click="cancelarA" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm w-full">Aplicar</button>
                    </div>
                </div>

                @if($datos['opcion'] && $datos['valor1'])
                    <a href="{{ route('infoOpImprimir', ['datos' => urlencode(json_encode($datos))]) }}" target="_blank" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Generar Reporte
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-auto w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border cursor-pointer" wire:click="sortby('id')">
                            <div class="flex items-center justify-between">
                                Operacion
                                @if($sortBy == 'id')
                                    <span>{!! $sortAsc ? '↑' : '↓' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-2 border cursor-pointer" wire:click="sortby('venta')">
                            <div class="flex items-center justify-between">
                                Venta
                                @if($sortBy == 'venta')
                                    <span>{!! $sortAsc ? '↑' : '↓' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-2 border cursor-pointer" wire:click="sortby('Fecha')">
                            <div class="flex items-center justify-between">
                                Fecha
                                @if($sortBy == 'Fecha')
                                    <span>{!! $sortAsc ? '↑' : '↓' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-2 border cursor-pointer" wire:click="sortby('tipoVenta')">
                            <div class="flex items-center justify-between">
                                Tipo de Venta
                                @if($sortBy == 'tipoVenta')
                                    <span>{!! $sortAsc ? '↑' : '↓' !!}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-2 border">Cliente</th>
                        <th class="px-4 py-2 border">Usuario</th>
                        <th class="px-4 py-2 border w-32">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ops as $op)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">{{ $op->id }}</td>
                        <td class="border px-4 py-2 text-right">${{ number_format($op->venta, 2) }}</td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($op->Fecha)->format('d/m/Y H:i') }}</td>
                        <td class="border px-4 py-2">{{ $op->tipoVenta }}</td>
                        <td class="border px-4 py-2">{{ $op->apellido }}, {{ $op->nombre }}</td>
                        <td class="border px-4 py-2">{{ $op->name }}</td>
                        <td class="border px-4 py-2 text-center">
                            <button wire:click='verOp({{ $op->id }})' class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Ver
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">No hay registros</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ops->links() }}
        </div>
    </div>

    <!-- Modal Ver Operacion -->
    <x-dialog-modal wire:model.live="verOperacion" class="max-w-5xl">
        <x-slot name="title">
            <h1 class="text-xl font-bold">Ver Operación #{{ $operacion }}</h1>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <!-- Información de la operación -->
                <div class="bg-gray-50 p-4 rounded-lg grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-bold">Cliente:</span> {{ $cliente }}
                    </div>
                    <div>
                        <span class="font-bold">Fecha:</span> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <span class="font-bold">Usuario:</span> {{ $usuario }}
                    </div>
                    <div>
                        <span class="font-bold">Tipo de Venta:</span> {{ $tipo }}
                    </div>
                    <div>
                        <span class="font-bold">Total Venta:</span> ${{ number_format($suma, 2) }}
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="border px-4 py-2">Artículo</th>
                                <th class="border px-4 py-2">Descripción</th>
                                <th class="border px-4 py-2">Precio</th>
                                <th class="border px-4 py-2">Cantidad</th>
                                <th class="border px-4 py-2">Descuento</th>
                                <th class="border px-4 py-2">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventaOp as $vOp)
                            <tr>
                                <td class="border px-4 py-2">{{ $vOp->articulo }}</td>
                                <td class="border px-4 py-2">{{ $vOp->presentacion }} - {{ $vOp->unidad }}</td>
                                <td class="border px-4 py-2 text-right">${{ number_format($vOp->precioF, 2) }}</td>
                                <td class="border px-4 py-2 text-center">{{ $vOp->cantidad }}</td>
                                <td class="border px-4 py-2 text-center">{{ $vOp->descuento }}%</td>
                                <td class="border px-4 py-2 text-right">${{ number_format(($vOp->precioF * $vOp->cantidad) * (1 - $vOp->descuento/100), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Sección de actualización de precios -->
                @if ($detalles)
                <div class="mt-6">
                    <div class="bg-yellow-100 p-3 rounded-lg mb-4">
                        <h3 class="font-bold text-lg">Actualización de Precios</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="border px-4 py-2">Artículo</th>
                                    <th class="border px-4 py-2">Descripción</th>
                                    <th class="border px-4 py-2">Precio Actual</th>
                                    <th class="border px-4 py-2">Cantidad</th>
                                    <th class="border px-4 py-2">Descuento</th>
                                    <th class="border px-4 py-2">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listArt as $vOp)
                                <tr>
                                    <td class="border px-4 py-2">{{ $vOp->articulo }}</td>
                                    <td class="border px-4 py-2">{{ $vOp->presentacion }}-{{ $vOp->unidad }}</td>
                                    <td class="border px-4 py-2 text-right">${{ number_format($vOp->precioF, 2) }}</td>
                                    <td class="border px-4 py-2 text-center">{{ $vOp->cantidad }}</td>
                                    <td class="border px-4 py-2 text-center">{{ $vOp->descuento }}%</td>
                                    <td class="border px-4 py-2 text-right">${{ number_format($vOp->precioF * $vOp->cantidad, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="5" class="border px-4 py-2 text-right">Total a Pagar:</td>
                                    <td class="border px-4 py-2 text-right">${{ number_format($sumTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <button wire:click='confirmarCancelacion' class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Confirmar Cancelación
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <div class="space-x-2">
                    @if ($operacion)
                        <a href="{{ route('comprobante', ['operacion' => $operacion]) }}" target="_blank" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Imprimir Comprobante
                        </a>
                    @endif
                    @if (!$detalles)
                        <button wire:click='cancelarCuenta({{ $operacion }})' class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Cancelar Cuenta
                        </button>
                    @endif
                </div>
                <button wire:click="$set('verOperacion', false)" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cerrar
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>