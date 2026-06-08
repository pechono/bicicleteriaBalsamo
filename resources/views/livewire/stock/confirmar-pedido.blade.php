<div class="w-full p-2 sm:px-5 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

    <div class="mt-4 text-2xl flex justify-between shadow-inner mb-4">
        <div>Confirmar Pedido a Proveedor</div>
    </div>

    <div class="mt-3 w-full">

        {{-- Selector de proveedor + botón --}}
        <div class="flex items-end gap-4 mb-4">
            <div>
                <x-label for="proveedor_id" value="{{ __('Proveedor') }}" />
                <select id="proveedor_id" wire:model='proveedor_id'
                        class="block mt-1 border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-80">
                    <option value="">Seleccionar...</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">
                            {{ $proveedor->id }} - {{ $proveedor->nombre }} - {{ $proveedor->rubro }} - {{ $proveedor->localidad }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="proveedor_id" class="mt-2" />
            </div>

            <button wire:click='guardarPedido()' class="h-9 px-4 bg-blue-500 hover:bg-blue-400 text-white rounded">
                Confirmar Pedido
            </button>
        </div>

        {{-- Tabla del carrito --}}
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <td class="px-4 py-2">Id</td>
                    <td class="px-4 py-2">Código</td>
                    <td class="px-4 py-2">Artículo</td>
                    <td class="px-4 py-2">Presentación</td>
                    <td class="px-4 py-2">Cantidad</td>
                </tr>
            </thead>
            <tbody>
                @forelse ($inTheCar as $car)
                <tr>
                    <td class="rounder border px-4 py-2">{{ $car->id }}</td>
                    <td class="rounder border px-4 py-2">{{ $car->codigo_proveedor }}-{{ $car->codigo }}</td>
                    <td class="rounder border px-4 py-2">{{ $car->articulo }}</td>
                    <td class="rounder border px-4 py-2">{{ $car->presentacion }}-{{ $car->unidad }}</td>
                    <td class="rounder border px-4 py-2">{{ $car->cantidad }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay artículos en el pedido.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- Modal confirmación --}}
    <x-dialog-modal wire:model.live="modal" maxWidth="2xl">
        <x-slot name="title">
            {{ __('Pedido a Proveedores') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Se ha generado el pedido al proveedor correctamente.') }}
        </x-slot>

        <x-slot name="footer">
            <button wire:click='cerrar()' class="h-10 bg-green-600 hover:bg-green-500 py-2 px-4 rounded mr-2 text-white">
                Cerrar
            </button>
            @if ($operacion)
                <a href="{{ route('pedidoImprimir', ['id' => $operacion]) }}" target="_blank"
                   class="h-10 px-4 py-2 bg-blue-500 hover:bg-blue-400 text-white rounded">
                    Imprimir
                </a>
            @endif
        </x-slot>
    </x-dialog-modal>

</div>
