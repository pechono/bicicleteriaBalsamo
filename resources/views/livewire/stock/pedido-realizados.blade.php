<div class="p-2 sm:px-5 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    <div class="mt-4 text-2xl flex justify-between shadow-inner">
    <div>Pedidos Realizados</div>

    </div>

   <div class="mt-3">
    <div class="flex justify-between">
        <div>
            <input wire:model.live='q' type="search" placeholder="Buscar" class="shadow appearance-none border rounded w-full py-2 px-3

            text-gray-706 leading-tight focus:outline-none focus: shadow-outline placeholder-blue-400" name="">
        </div>


    </div>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <td class="px-4 py-2">
                    <div class="flex items-center" >
                       <button wire:click="sortby('id')">Pedido</button>
                     <x-sort-icon sortFiel='id': sortBy=$sortBy, sortAsc=$sortAsc/>
                    </div>
                </td>
                <td class="px-4 py-2">
                    <div class="flex items-center">
                        <Button wire:click="sortby('apellido')">Proveedor</Button>
                        <x-sort-icon sortFiel='apellido': sort-by='$sortBy' : sort-asc='$sortAsc'>

                    </div>
                </td>
                <td class="px-4 py-2">
                    <div class="flex items-center">
                        <Button wire:click="sortby('nombre')">Fecha</Button>
                        <x-sort-icon sortFiel='nombre': sort-by='$sortBy' : sort-asc='$sortAsc'/>
                    </div>
                </td>
                    <td class="px-4 py-2">
                    <div class="flex items-center">Accion</div>
                </td>
            </tr>
        </thead>
        <tbody>
            @forelse ($pedidos as $op)
            <tr>
                <td class="rounder border px-4 py-2">{{ $op->pedido }}</td>
                <td class="rounder border px-4 py-2">{{ $op->nombre }}{{ $op->localidad }}</td>
                <td class="rounder border px-4 py-2">{{ $op->Fecha }}</td>
                <td class="rounder border px-4 py-2">
                    <x-secondary-button wire:click='verPed({{ $op->pedido }})'>
                        Ver
                    </x-secondary-button>
                </td>
            </tr>
            @empty
            <h2>No hay registro</h2>
            @endforelse


        </tbody>
    </table>
   </div>
   {{-- <div class="mt-2">{{ $clientes->links() }}</div> --}}

   <!-- Delete User Confirmation Modal -->
    <x-dialog-modal wire:model.live="verPedido" class="w-3/5">
        <x-slot name="title">
            <h1>Ver Pedido</h1>
        </x-slot>

        <x-slot name="content" class="w-full">
            {{-- Datos del pedido --}}
            <div class="mb-4 rounded-lg bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2">Pedido a Proveedor N° {{ $pedido }}</div>
                <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
                    <div><span class="text-gray-500">Empresa:</span> <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $proveedor }}</span></div>
                    <div><span class="text-gray-500">Localidad:</span> <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $localidad }}</span></div>
                </div>
            </div>

            {{-- Artículos del pedido --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wide">Código</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wide">Artículo</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wide">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ( $artPedido as $op )
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $op->codigo_proveedor }}{{ $op->codigo }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $op->articulo }} {{ $op->presentacion }} {{ $op->unidad }}</td>
                                <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-200">{{ $op->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-slot>


        <x-slot name="footer">
            @if ($pedido)
                <a href="{{ route('pedidoImprimir',['id'=>$pedido]) }}" target="_blank" class=" px-4 py-2 bg-blue-500 text-white rounded">
                    Imprimir Comprobante
                </a>
                <button wire:click="enviarWhatsApp" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
                    <span wire:loading.remove wire:target="enviarWhatsApp">Enviar por WhatsApp</span>
                    <span wire:loading wire:target="enviarWhatsApp">Enviando…</span>
                </button>
            @endif
            <x-secondary-button wire:click="$toggle('verPedido', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
        </x-slot>

</x-dialog-modal>
    <!--Fin Delete  Confirmation Modal -->
</div>
