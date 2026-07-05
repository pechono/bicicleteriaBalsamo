<div class="flex ">
    <div class=" h-auto  w-full  m-1 ">
        <div class=" bg-white p-4 rounded-lg border">
            <div class=" bg-white p-4 rounded-lg shadow-lg w-auto border">
            {{-- articulos --}}
                <div class="mt-4 text-2xl flex justify-between shadow-inner">
                    <div>Terminar Proceso Nro:     {{$nro}}</div>
                </div>

                <strong>{{$nro}}</strong>
                <strong>Cliente: {{$clientesBici->nombre}} {{$clientesBici->apellido}}</strong>
            </div>

            <div class="mt-3 w-full rounded-lg border shadow-lg p-4">

                                <table class="table-auto w-full">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2">Id</th>
                                            <th class="px-4 py-2">Codigo</th>
                                            <th class="px-4 py-2">Artículo</th>
                                            <th class=" py-2">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($procesos as $Items)                
                                        
                                            @if (!$this->stockInsufisinte($Items->id))
                                                <tr wire:key="{{ $Items->id }}"
                                                    class="cursor-pointer {{ $this->estaEnCarrito($Items->id) ? 'hover:text-white hover:bg-red-400' : 'hover:text-white hover:bg-green-300' }}"
                                                    wire:dblclick="{{ $estaEnCarrito ? 'deletCar('.$Items->id.')' : 'addCar('.$Items->id.')' }}"
                                                    wire:loading.attr="disabled">
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($Items->id) ? 'text-brand-500 font-bold':'' }}">{{ $Items->id }}</td>
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($Items->id) ? 'text-brand-500 font-bold':'' }}">{{$Items->codigo_proveedor}}-{{ $Items->codigo }}</td>

                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($Items->id) ? 'text-brand-500 font-bold':'' }}">{{ $Items->articulo }}</td>
                                                    <td class="rounder border flex p-1 flex-wrap">   
                                                                @if ($this->estaEnCarrito($Items->id))
                                                                    <div class="flex gap-1 justify-center">
                                                                        <!-- Botón Eliminar -->
                                                                        <button wire:click="deletCar({{ $Items->id }})" 
                                                                                wire:loading.attr="disabled"
                                                                                class="group flex items-center justify-center w-8 h-8 bg-gradient-to-br from-red-400 to-red-600 hover:from-red-500 hover:to-red-700 rounded-lg transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                        </button>

                                                                        <!-- Botón Modificar -->
                                                                        <button wire:click="modCar({{ $Items->id }})" 
                                                                                wire:loading.attr="disabled"
                                                                                class="group flex items-center justify-center w-8 h-8 bg-gradient-to-br from-brand-400 to-brand-600 hover:from-brand-500 hover:to-brand-700 rounded-lg transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <!-- Botón Agregar -->
                                                                    <button wire:click="addCar({{ $Items->id }})" 
                                                                            wire:loading.attr="disabled"
                                                                            class="group flex items-center justify-center w-8 h-8 bg-gradient-to-br from-green-400 to-brand-600 hover:from-brand-500 hover:to-brand-700 rounded-lg transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95 mx-auto">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                                      
        
                                                </tr>      
                                            @endif
                                        
                                        @endforeach
                                    </tbody>
                                </table>

                            
                        </div>  
    

        </div>  
    </div>            
    <div class=" h-auto  md:w-[60%]  m-5 ">

                <div class=" bg-white p-4 rounded-lg border">
                    <div class=" bg-white p-4 rounded-lg shadow-lg w-auto border">
                    {{-- articulos --}}
                        <div class="mt-4 text-2xl flex justify-between shadow-inner">
                            <div>Articulos/Procesos</div>
                        </div>
                        <div>
                            <!-- Campo de búsqueda -->
                            <div class="mt-3">
                                <input wire:model.live="q" type="search" placeholder="Buscar" class="shadow appearance-none border rounded w-full py-2 px-3
                                    text-gray-700 leading-tight focus:outline-none focus:shadow-outline placeholder-brand-400">
                            </div>
                            @if (!$q)
                                <p class="text-gray-500 mt-2">Ingrese algún texto para buscar un artículo.</p>
                            @endif
                        </div>
                        <div class="mt-3 w-full rounded-lg border shadow-lg p-4">
                            @if ($q)

                                <table class="table-auto w-full">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2">Id</th>
                                            <th class="px-4 py-2">Codigo</th>
                                            <th class="px-4 py-2">Artículo</th>
                                            <th class="px-4 py-2">Unidad Cantidad</th>
                                            <th class="px-4 py-2">Precio Final</th>
                                            <th class="px-4 py-2">Stock</th>
                                            <th class=" py-2">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articulos as $articulo)                
                                        
                                            @if (!$this->stockInsufisinte($articulo->id))
                                                <tr wire:key="{{ $articulo->id }}"
                                                    class="cursor-pointer {{ $this->estaEnCarrito($articulo->id) ? 'hover:text-white hover:bg-red-400' : 'hover:text-white hover:bg-green-300' }}"
                                                    wire:dblclick="{{ $estaEnCarrito ? 'deletCar('.$articulo->id.')' : 'addCar('.$articulo->id.')' }}"
                                                    wire:loading.attr="disabled">
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">{{ $articulo->id }}</td>
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">{{$articulo->codigo_proveedor}}-{{ $articulo->codigo }}</td>

                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">{{ $articulo->articulo }}-{{ $articulo->presentacion }}-{{ $articulo->unidad }}</td>
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">{{ $articulo->unidadVenta }}</td>
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">{{ $articulo->precioF }}</td>
                                                    <td class="rounder border px-4 py-2 {{ $this->Ofeta($articulo->id) ? 'text-brand-500 font-bold':'' }}">
                                                        @if ($articulo->suelto == 1)
                                                            <div class="w-8 h-8 p-2 grid justify-items-center content-center bg-green-400 rounded-full">{{ $articulo->stock }}</div>
                                                        @else
                                                            {{ $articulo->stock }}
                                                        @endif
                                                    </td>
                                                    <td class="rounder border flex p-1 flex-wrap">
                                                        @if ($this->estaEnCarrito($articulo->id))

                                                            <button wire:click="deletCar({{ $articulo->id }})" wire:loading.attr="disabled" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold p-2 rounded-lg ">
                                                                Elim
                                                            </button>
                                                            <button wire:click="modCar({{ $articulo->id }})" wire:loading.attr="disabled" class="ml-1 flex-1 bg-brand-500 hover:bg-brand-700 text-white font-bold p-2 rounded-lg "">
                                                                Mod
                                                            </button> 
                                                        
                                                        @else
                                                            <button wire:click="addCar({{ $articulo->id }})" wire:loading.attr="disabled" class="flex-1 bg-brand-500 hover:bg-brand-700 text-white font-bold p-2 rounded-lg ">
                                                                Agregar
                                                            </button>

                                                        @endif
                                                    </td>
                                                </tr>      
                                            @endif
                                        
                                        @endforeach
                                    </tbody>
                                </table>

                            @endif
                        </div>
                    {{-- fin articulos --}}
                    </div>
                </div>
                <div class=" bg-white p-4 rounded-lg shadow-lg w-auto mt-10">
                        {{-- seleccionados --}}
                        <div class="mt-3 w-full rounded-lg border shadow-lg p-4">
                            <table class="">
                                <thead>
                                    <tr>
                                        <td class="px-4 py-2"><div class="flex items-center" >Id</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Codigo</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Articulo</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Precio Final</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">S Min.</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Stock</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Cant.</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Descuento.</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Sub Total</div></td>
                                        <td class="px-4 py-2"><div class="flex items-center">Accion</div></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subtotal=0;
                                        $total=0;
                                    @endphp
                                        @foreach ($inTheCar as $item)
                                            <tr class="{{ $this->Ofeta($item->articulo_id) ? 'text-brand-500 font-bold':'' }}">
                                                <td class="rounder border px-4 py-2">{{ $item->articulo_id }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->codigo_proveedor }}-{{ $item->codigo }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->articulo }} {{ $item->presentacion }} - {{ $item->unidad  }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->precioF  }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->stockMinimo }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->stock }}</td>
                                                <td class="rounder border px-4 py-2">{{ $item->cantidad }}</td>
                                                <td class="rounder border px-4 py-2">
                                                    <div class="flex items-center ">
                                                        <div class="w-6">{{ $item->descuento }}</div>
                                                        <div class="w-5">
                                                            <button class=' h-18 w-16 text-white text-l rounded-md bg-brand-600 hover:bg-green-300' wire:click="descuentoArt({{ $item->articulo_id }})" wire:loading.attr="disabled" >
                                                            Desc.
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                @php
                                                $subtotal=($item->cantidad * $item->precioF)-($item->cantidad * $item->precioF)*$item->descuento/100;
                                                $total+=$subtotal;
                                                @endphp
                                                <td class="rounder border px-4 py-2">{{ $subtotal}}</td>
                                                <td class="rounder border  py-1 flex p-1 flex-wrap">
                                                        <button wire:click="deletCar({{$item->articulo_id}})" wire:loading.attr="disabled" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded-lg ">
                                                            Elim
                                                        </button>
                                                        <button wire:click="modCar({{$item->articulo_id}})" wire:loading.attr="disabled" class="ml-1 flex-1 bg-brand-500 hover:bg-brand-700 text-white font-bold py-2 px-2 rounded-lg "">
                                                            Mod
                                                        </button> 
                                                       
                                                   
                                                </td>
                                            </tr>
                                        @endforeach

                                </tbody>
                            </table>
                        </div>
                        {{-- fin seleccionados --}}
                </div>
{{-- cliente------ Boton terminar --}}
                <div class=" bg-white  rounded-lg shadow-md  m-5 h-3/4">
                        <div class="w-full rounded-lg shadow-md m-5 p-4">
                            <div class="flex gap-4 w-full">
                                 <div class="flex-1">
                                    <div class="col-span-6 sm:col-span-4 rounded-lg border shadow-lg p-4">
                                        <div class="text-lg mb-4">Mecanico </div>
                                        <select id="mecanicoSelect" class="block w-full text-1xl rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" 
                                                wire:model.live='mecanicoSelect' >
                                            <option value="">Seleccionar...</option>
                                            @foreach ($mecanicos as $m)
                                                <option value="{{ $m->id }}">
                                                    {{ $m->id }} - {{ $m->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="mecanicoSelect" class="mt-2" />
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="bg-gray-100 rounded-lg border shadow-lg p-4 h-full flex flex-col justify-center">
                                        <div class="text-lg text-gray-600 mb-2">Total:</div>
                                        <div class="text-4xl font-bold text-right">
                                            ${{ number_format($total, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    @if ($BloquearBoton)

                                    <div class=' rounded-lg border shadow-lg bg-green-400 m-4 p-2 flex justify-between'>
                                        <x-danger-button wire:click="cancelarOperacion()" wire:loading.attr="disabled">
                                            {{ __('Cancelar') }}
                                        </x-danguer-button>
                                            @if ($mecanicoSelect)
                                            <x-secondary-button class="ms-3" wire:click="PreguntaConfirmarVenta()" wire:loading.attr="disabled">
                                                {{ __('Confirmar') }}
                                            </x-secondary-button>
                                            @endif
                                    </div>
                                @endif
                    </div>

{{-- fin cliente boton terminar------------ --}}


    </div>
{{--fin articulos y seleccionados --}}

        {{-- modal --}}
        @if ($agregarCant)
        <x-dialog-modal wire:model.live="agregarCant" maxWidth="2xl">
            <x-slot name="title">
                {{ __('Selecionar Articulo') }}
            </x-slot>
            <x-slot name="content">
                {{-- Datos del artículo --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm">
                        <span class="font-mono text-xs text-gray-500">#{{ $articulosMuestra->id }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 ml-2">{{ $articulosMuestra->articulo }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm p-4">
                        <div><span class="text-gray-500">Stock actual:</span> <b class="text-gray-800 dark:text-gray-200">{{ $articulosMuestra->stock }}</b></div>
                        <div><span class="text-gray-500">Precio final:</span> <b class="text-gray-800 dark:text-gray-200">${{ $articulosMuestra->precioF }}</b></div>
                    </div>
                </div>

                {{-- Cantidad / Precio --}}
                <div class="text-center">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase mb-2">{{ $esMdO ? 'Precio a cobrar' : 'Ingresar cantidad' }}</label>
                    @if ($esMdO)
                        <input id="precioMdO" wire:model="precioMdO"
                            @if ($agregarCant === 1) wire:keydown.enter="save({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})"
                            @elseif ($agregarCant === 2) wire:keydown.enter="updateSave({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" @endif
                            type="text" placeholder="0" class="text-center text-4xl shadow appearance-none border rounded w-40 h-20 py-2 px-3" />
                        <x-input-error for="precioMdO" class="mt-2" />
                        <div class="text-xs text-gray-500 mt-1">Mano de obra: la cantidad queda en 1.</div>
                    @else
                        <input id="cantidadArt" wire:model="cantidadArt"
                            @if ($agregarCant === 1) wire:keydown.enter="save({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})"
                            @elseif ($agregarCant === 2) wire:keydown.enter="updateSave({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" @endif
                            type="text" placeholder="0" class="text-center text-4xl shadow appearance-none border rounded w-40 h-20 py-2 px-3" />
                        <x-input-error for="cantidadArt" class="mt-2" />
                    @endif
                    @if($majStock)<div class="text-center text-red-500 mt-2">{{ $majStock }}</div>@endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-danger-button wire:click="$toggle('agregarCant', false)" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-danger-button>
                @if ($agregarCant==1 )
                    <x-secondary-button class="ms-3" wire:click="save({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" wire:loading.attr="disabled">
                    {{ $esMdO ? __('Agregar') : __('Agregar Cantidad') }}
                    </x-secondary-button>
                @else<x-secondary-button class="ms-3" wire:click="updateSave({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" wire:loading.attr="disabled">
                    {{ $esMdO ? __('Guardar precio') : __('Modificar Cantidad') }}
                    </x-secondary-button>
                    
                @endif
                
            </x-slot>
        </x-dialog-modal>
        @endif

        <x-dialog-modal wire:model.live="cDescuento" maxWidth="2xl">
            <x-slot name="title">
                {{ __('Seleccionar Articulo') }}
            </x-slot>
            <x-slot name="content">
                {{-- Datos del artículo --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm">
                        <span class="font-mono text-xs text-gray-500">#{{ $id }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 ml-2">{{ $art }}</span>
                        @if($categoria && $categoria !== '-')<span class="text-gray-500 ml-1">{{ $categoria }}</span>@endif
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-1 text-sm p-4">
                        <div><span class="text-gray-500">Precio inicial:</span> <b class="text-gray-800 dark:text-gray-200">${{ $precioI }}</b></div>
                        <div><span class="text-gray-500">Precio final:</span> <b class="text-gray-800 dark:text-gray-200">${{ $precioF }}</b></div>
                        <div><span class="text-gray-500">Descuento:</span> <b class="text-gray-800 dark:text-gray-200">{{ $descuento }}</b></div>
                        <div><span class="text-gray-500">Stock:</span> <b class="text-gray-800 dark:text-gray-200">{{ $stock }}</b></div>
                        <div><span class="text-gray-500">Stock mín.:</span> <b class="text-gray-800 dark:text-gray-200">{{ $stockMinimo }}</b></div>
                        @if($caducidad && $caducidad !== '-' && $caducidad !== 'No')<div><span class="text-gray-500">Caducidad:</span> <b class="text-gray-800 dark:text-gray-200">{{ $caducidad }}</b></div>@endif
                        @if($detalles && $detalles !== '-')<div><span class="text-gray-500">Detalles:</span> <b class="text-gray-800 dark:text-gray-200">{{ $detalles }}</b></div>@endif
                    </div>
                </div>

                {{-- Descuento --}}
                <div class="text-center">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase mb-2">Aplicar descuento (%)</label>
                    <input id="descArt" wire:model='descArt' type="text" placeholder="0" class="text-center text-4xl shadow appearance-none border rounded w-40 h-20 py-2 px-3">
                    <x-input-error for="descArt" class="mt-2" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-danger-button wire:click="$toggle('cDescuento', false)" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-danger-button>

            <x-secondary-button class="ms-3" wire:click="saveDescuento({{ $id }})" wire:loading.attr="disabled">
                    {{ __('Aceptar Descuento') }}
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>

        {{-- ----modal confirmar venta---- --}}
        <x-dialog-modal wire:model.live="confirmarOpVenta" maxWidth="2xl">
            <x-slot name="title">
                Confirmar Reparación
            </x-slot>

            <x-slot name="content">

                {{-- Resumen de trabajos realizados --}}
                <div class="mb-5 bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-4 py-2.5 bg-gray-100 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700">📋 Trabajos / Artículos cargados</h4>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 border-b border-gray-200">
                                <th class="text-left px-4 py-2">Artículo</th>
                                <th class="text-center px-3 py-2">Cant.</th>
                                <th class="text-right px-4 py-2">P. Final</th>
                                <th class="text-right px-4 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($inTheCar as $car)
                            <tr>
                                <td class="px-4 py-2 text-gray-700">{{ $car->articulo }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $car->cantidad }}</td>
                                <td class="px-4 py-2 text-right text-gray-600">${{ number_format($car->precioF, 2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-gray-800">
                                    ${{ number_format($car->cantidad * $car->precioF * (1 - $car->descuento / 100), 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 bg-gray-50">
                                <td colspan="3" class="px-4 py-2 text-sm font-semibold text-gray-700 text-right">Total cobrado:</td>
                                <td class="px-4 py-2 text-right font-bold text-brand-700">
                                    ${{ number_format($inTheCar->sum(fn($c) => $c->cantidad * $c->precioF * (1 - $c->descuento / 100)), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Sección ítems del mecánico --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h4 class="font-semibold text-amber-800 mb-3 flex items-center gap-2">
                        🔧 Ítems a favor del mecánico
                        <span class="text-xs font-normal text-amber-600">(opcional)</span>
                    </h4>

                    {{-- Formulario agregar ítem --}}
                    <div class="flex gap-2 mb-3">
                        <input wire:model="itemDesc" type="text" placeholder="Descripción (ej: Servis, Parche...)"
                            class="flex-1 text-sm border-gray-300 rounded-lg focus:ring-amber-400 focus:border-amber-400"
                            wire:keydown.enter="agregarItemMecanico"/>
                        <input wire:model="itemMonto" type="number" min="0" step="0.01" placeholder="$ Monto"
                            class="w-28 text-sm border-gray-300 rounded-lg focus:ring-amber-400 focus:border-amber-400"
                            wire:keydown.enter="agregarItemMecanico"/>
                        <button wire:click="agregarItemMecanico" type="button"
                            class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition">
                            +
                        </button>
                    </div>

                    {{-- Lista de ítems cargados --}}
                    @if(count($mecanicoItems) > 0)
                    <div class="space-y-1.5 mb-2">
                        @foreach($mecanicoItems as $i => $item)
                        <div class="flex items-center justify-between bg-white border border-amber-200 rounded-lg px-3 py-2 text-sm">
                            <span class="text-gray-700">{{ $item['descripcion'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-brand-700">${{ number_format($item['monto'], 2) }}</span>
                                <button wire:click="quitarItemMecanico({{ $i }})" type="button"
                                    class="text-red-400 hover:text-red-600 text-lg leading-none transition">×</button>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-right text-sm font-bold text-amber-800 pt-1">
                            Total: ${{ number_format(array_sum(array_column($mecanicoItems, 'monto')), 2) }}
                        </div>
                    </div>
                    @else
                    <p class="text-xs text-amber-600 italic">Sin ítems cargados — podés agregar después desde la cuenta del mecánico.</p>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-danger-button wire:click="$toggle('confirmarOpVenta', false)" wire:loading.attr="disabled">
                    Cancelar
                </x-danger-button>
                <x-secondary-button class="ms-3" wire:click="ConfirmarVenta()" wire:loading.attr="disabled">
                    ✅ Confirmar Reparación
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
       

</div>


