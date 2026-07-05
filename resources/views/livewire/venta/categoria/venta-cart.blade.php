<div class="">
    <div class=" h-auto  md:w-[100%]  m-1 p-4">

            <div class=" bg-white p-4 rounded-lg border">
                <div class=" bg-white p-4 rounded-lg shadow-lg w-auto border">
                {{-- articulos --}}
                    <div class="mt-4 text-2xl flex justify-between shadow-inner">
                        <div>Articulo</div>
                    </div>
                  
                @forelse ($categoris as $categoria)
                    <h2 class="text-xl font-bold my-4">{{ $categoria->categoria }}</h2> <!-- Nombre de la categoría -->
                    @php
                        $i=0;
                    @endphp
                    <div class="flex flex-wrap gap-4"> 
                       
                        @forelse ($articulos as $articulo)
                            
                           @if ( $articulo->categoria_id==$categoria->id )
                                <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 xl:w-1/6 py-2 px-2 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                                    <h5 class="mb-4 text-lg font-medium text-gray-500 dark:text-gray-400">{{ $articulo->articulo }}</h5>
                                    <div class="flex items-baseline text-gray-900 dark:text-white">
                                        <span class="text-2xl font-semibold">$</span>
                                        <span class="text-3xl font-extrabold tracking-tight">{{ $articulo->precioF }}</span>
                                        <span class="ms-1 text-lg font-normal text-gray-500 dark:text-gray-400">{{ $articulo->unidadVenta }}</span>
                                    </div>
                                    <ul role="list" class="space-y-2 my-7 ">
                                        <li class="flex items-center">
                                            <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                            </svg>
                                            <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">Conteine {{ $articulo->presentacion }} {{ $articulo->unidad }}</span>
                                        </li>
                                        <li class="flex">
                                            <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                            </svg>
                                            <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">{{$articulo->stock}} articulos en stock</span>
                                        </li>
                                        <li class="flex">
                                            <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                            </svg>
                                            <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">Descuento aceptable de {{$articulo->descuento}} </span>
                                        </li>
                                            <li class="flex  decoration-gray-500">
                                            <svg class="flex-shrink-0 w-4 h-4 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                            </svg>
                                            <span class="text-base font-normal leading-tight text-gray-500 ms-3">Stock minimo de {{$articulo->stockMinimo}} </span>
                                        </li>
                                        <li class="flex  decoration-gray-500">
                                            <svg class="flex-shrink-0 w-4 h-4 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                            </svg>
                                            <span class="text-base font-normal leading-tight text-gray-500 ms-3">Con vencimiento, {{$articulo->caducidad}} </span>
                                        </li>
                                    </ul>
                                    <div class=" flex p-1 flex-wrap">
                                        @if ($this->estaEnCarrito($articulo->id))
                                            <button wire:click="deletCar({{ $articulo->id }})" wire:loading.attr="disabled" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold p-2 rounded-lg ">
                                                Elim
                                            </button>
                                            <button wire:click="modCar({{ $articulo->id }})" wire:loading.attr="disabled" class="ml-1 flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold p-2 rounded-lg "">
                                                Mod
                                            </button>
                                        @else
                                            <button wire:click="addCar({{ $articulo->id }})" wire:loading.attr="disabled" class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold p-2 rounded-lg ">
                                                Agregar
                                            </button>

                                        @endif
                                    </div>
                                </div>        
                                @php
                                $i+=1;
                                @endphp
                            @endif
                        @empty
                            <p class="text-gray-500">No hay artículos en esta categoría.</p>
                        @endforelse
                        @if ($i==0)
                            <p class="text-gray-500">No hay artículos en esta categoría.</p>
                        @endif
                    </div>
                 
                @empty
                    <h2 class="text-gray-500">No hay categorías disponibles.</h2>
                @endforelse


                
            </div>
            <div class=" bg-white p-4 rounded-lg shadow-lg w-auto mt-10">
                    {{-- seleccionados --}}
                    <div class="mt-3 w-full rounded-lg border shadow-lg p-4">
                        <table class="">
                            <thead>
                                <tr>
                                    <td class="px-4 py-2"><div class="flex items-center" >Id</div></td>
                                    <td class="px-4 py-2"><div class="flex items-center">Articulo</div></td>
                                    <td class="px-4 py-2"><div class="flex items-center">Presentacion</div> </td>
                                   
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
                                        <tr class="{{ $this->Ofeta($item->articulo_id) ? 'text-green-500 font-bold':'' }}">
                                            <td class="rounder border px-4 py-2">{{ $item->articulo_id }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->articulo }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->presentacion }} - {{ $item->unidad  }} / {{ $item->unidadVenta }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->precioF  }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->stockMinimo }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->stock }}</td>
                                            <td class="rounder border px-4 py-2">{{ $item->cantidad }}</td>
                                            <td class="rounder border px-4 py-2">
                                                <div class="flex items-center ">
                                                    <div class="w-6">{{ $item->descuento }}</div>
                                                    <div class="w-5">
                                                        <button class=' h-18 w-16 text-white text-l rounded-md bg-green-600 hover:bg-green-300' wire:click="descuentoArt({{ $item->articulo_id }})" wire:loading.attr="disabled" >
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
                                                    <button wire:click="deletCar({{$item->articulo_id}})" wire:loading.attr="disabled" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg ">
                                                        Elim
                                                    </button>
                                                    <button wire:click="modCar({{$item->articulo_id}})" wire:loading.attr="disabled" class="ml-1 flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg "">
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
    </div>
    <div class="w-full bg-white rounded-lg shadow-md">
       
        <div class="flex flex-wrap items-start md:items-stretch  border p-4">
            <!-- Selección de Cliente -->
            <div class="w-full md:w-1/3 flex flex-col rounded-lg border shadow-lg p-4">
                <div class="text-lg font-semibold text-gray-700 mb-2">Cliente</div>
                @if(!$clienteConfirmado)
                    {{-- Buscador --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live="d" placeholder="Buscar (nombre, apellido, DNI o teléfono)..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    @if(!empty($d))
                        <div class="mt-3">
                            <div class="text-sm text-gray-600 mb-2">Resultados: {{ $clientes->count() }}</div>
                            @if($clientes->isNotEmpty())
                                <div class="max-h-56 overflow-y-auto border border-gray-200 rounded-lg">
                                    @foreach ($clientes as $cliente)
                                        <div wire:click="seleccionarCliente({{ $cliente->id }})"
                                             class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition {{ $cliente_id == $cliente->id ? 'bg-blue-100' : '' }}">
                                            <div class="font-medium text-gray-900">{{ $cliente->apellido }}, {{ $cliente->nombre }}</div>
                                            <div class="text-xs text-gray-500 mt-1">@if($cliente->dni) DNI: {{ $cliente->dni }} @endif @if($cliente->telefono) | Tel: {{ $cliente->telefono }} @endif</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 bg-gray-50 rounded-lg">
                                    <p class="text-gray-500 text-sm">No se encontraron clientes para "{{ $d }}"</p>
                                    <button wire:click="abrirModalCliente" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition">+ Crear nuevo cliente</button>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($cliente_id && !$clienteConfirmado)
                        @php $clienteSeleccionado = $clientes->firstWhere('id', $cliente_id); @endphp
                        @if($clienteSeleccionado)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                <div><span class="text-xs text-blue-600">A confirmar:</span><div class="font-medium text-blue-800">{{ $clienteSeleccionado->apellido }}, {{ $clienteSeleccionado->nombre }}</div></div>
                                <button wire:click="limpiarCliente" class="text-red-500 hover:text-red-700">✕</button>
                            </div>
                        @endif
                        <button wire:click="confirmarClienteAdd" class="text-white bg-green-500 hover:bg-green-600 rounded-md w-full py-2 px-5 mt-3 transition">✓ Confirmar Cliente</button>
                    @endif

                    @if(empty($d) && !$cliente_id)
                        <div class="text-center py-8">
                            <p class="text-gray-500">Buscar cliente</p>
                            <p class="text-xs text-gray-400 mt-1">Nombre, apellido, DNI o teléfono</p>
                            <button wire:click="abrirModalCliente" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition">+ Crear nuevo cliente</button>
                        </div>
                    @endif
                @else
                    {{-- Cliente confirmado --}}
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">✓</div>
                            <div>
                                <div class="text-xs text-green-600 font-medium">Cliente confirmado</div>
                                <div class="text-lg font-bold text-gray-800">{{ $clienteSeleccionadoApellido }}, {{ $clienteSeleccionadoNombre }}</div>
                            </div>
                        </div>
                        <button wire:click="limpiarCliente" class="text-red-500 hover:text-red-700 bg-white rounded-full p-2 shadow-sm" title="Cambiar cliente">✕</button>
                    </div>
                @endif
                <x-input-error for="cliente_id" class="mt-2" />
            </div>
            <div class="w-full md:w-1/3 h-full flex  flex-col rounded-lg border shadow-lg p-4 ">
                <div class="text-lg px-5">Seleccionar Cliente</div>
                <select id="tipo_id" class="block w-full mt-4 text-1xl rounded-md" wire:model='tipo_id' wire:click='tipoVenta()'>
                    <option value="">Seleccionar...</option>
                    @foreach ($tipoVentas as $tipo)
                        <option value="{{ $tipo->id }}">
                            {{ $tipo->id }} - {{ $tipo->tipoVenta }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="tipo_id" class="mt-2" />
                <div class="text-xl px-5">  </div>

            </div>
            <div class="w-full md:w-1/3 h-sc flex flex-col rounded-lg border shadow-lg p-4">
                <div class="text-lg px-5"></div>
                <div class="text-3xl font-semibold">Total:</div>
                <div class="text-4xl">{{ $total }}</div>         
            </div>
           
        </div>
        
        
        <!-- Botones de Operación -->
        @if ($BloquearBoton)
            <div class="flex justify-between items-center bg-green-400 rounded-lg border shadow-lg m-4 p-2">
                <x-danger-button wire:click="cancelarOperacion()" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-danger-button>
                @if ($cliente_id || $tipo_id)
                    <x-secondary-button class="ms-3" wire:click="PreguntaConfirmarVenta()" wire:loading.attr="disabled">
                        {{ __('Confirmar') }}
                    </x-secondary-button>
                @endif
            </div>
        @endif
    </div>
    
    {{-- <div class="w-full bg-white  rounded-lg shadow-md ">
            <div class=" w-auto rounded-lg shadow-md m-5 p-4 flex">
                <div class=" sm:col-span-4  rounded-lg border shadow-lg ">
                    <div class="text-lg mt-4 px-5">Seleccionar Cliente</div>
                    <select id="cliente_id" class="block w-full mt-4 text-1xl rounded-md" name="cliente_id" wire:model='cliente_id'>
                        <option value="">Seleccionar...</option>
                        @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}">
                            {{ $cliente->apellido }} , {{ $cliente->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <button wire:click='confirmarClienteAdd' class="text-white bg-green-500 hover:bg-green-300 rounded-md w-full py-2 px-5 mt-1.5" style="margin-top: 3px;">
                        Agregar Cliente
                    </button>
                    <x-input-error for="cliente_id" class="mt-2" />
                </div>

                <div class=" sm:col-span-4 mt-4 rounded-lg border shadow-lg ">
                    <div class="text-lg mt-4 px-5">Tipo de Venta</div>
                    <select id="tipo_id"  class="block  w-full mt-4 text-1xl" wire:model='tipo_id'  wire:click='tipoVenta()' class="rounded-md "/>
                        <option value="">Seleccionar...</option>
                        @foreach ($tipoVentas as $tipo)
                            <option value="{{ $tipo->id}}"  >
                                {{ $tipo->id}}-{{ $tipo->tipoVenta}}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error for="tipo_id" class="mt-2" />
                </div>

                <div class="flex bg-gray-100 pr-4 mb-10 mt-10 h-20 items-center border-gray-300 rounded-lg border  shadow-lg">
                    <div class="text-3xl flex items-end justify-end flex-grow mr-10">
                        Total:
                    </div>
                    <div class="text-4xl rounded-md shadow-sm flex items-center justify-end">
                        {{ $total }}
                    </div>
                </div>
            </div>
            @if ($BloquearBoton)

            <div class=' rounded-lg border shadow-lg bg-green-400 m-4 p-2 flex justify-between'>
                <x-danger-button wire:click="cancelarOperacion()" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-danguer-button>
                    @if ($cliente_id || $tipo_id)
                    <x-secondary-button class="ms-3" wire:click="PreguntaConfirmarVenta()" wire:loading.attr="disabled">
                        {{ __('Confirmar') }}
                    </x-secondary-button>
                    @endif
            </div>
            @endif
    </div> --}}

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
                    <span class="font-semibold text-gray-800 dark:text-gray-200 ml-2">{{ $articulosMuestra->articulo }} - {{ $articulosMuestra->presentacion }}-{{ $articulosMuestra->unidad }}</span>
                </div>
                <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-gray-700 text-sm">
                    <div class="px-4 py-2"><div class="text-xs text-gray-500 uppercase tracking-wide">Stock actual</div><div class="font-semibold text-gray-800 dark:text-gray-200">{{ $articulosMuestra->stock }}</div></div>
                    <div class="px-4 py-2"><div class="text-xs text-gray-500 uppercase tracking-wide">Precio final</div><div class="font-semibold text-gray-800 dark:text-gray-200">${{ $articulosMuestra->precioF }}</div></div>
                </div>
            </div>

            {{-- Cantidad --}}
            <div class="text-center">
                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase mb-2">Ingresar cantidad</label>
                <input
                    id="cantidadArt"
                    wire:model="cantidadArt"
                    @if ($agregarCant === 1)
                        wire:keydown.enter="save({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})"
                    @elseif ($agregarCant === 2)
                        wire:keydown.enter="updateSave({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})"
                    @endif
                    type="text"
                    placeholder="0"
                    class="text-center text-4xl shadow appearance-none border rounded w-40 h-20 py-2 px-3"
                />
                <x-input-error for="cantidadArt" class="mt-2" />
            </div>
            <div class="text-center text-red-500 mt-2">{{ $majStock }}</div>
        </x-slot>

        <x-slot name="footer">
            <x-danger-button wire:click="$toggle('agregarCant', false)" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </x-danger-button>
            @if ($agregarCant==1 )
                <x-secondary-button class="ms-3" wire:click="save({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" wire:loading.attr="disabled">
                {{ __('Agregar Cantidad') }}
                </x-secondary-button>
            @else<x-secondary-button class="ms-3" wire:click="updateSave({{ $articulosMuestra->id }}, {{ $articulosMuestra->stock }})" wire:loading.attr="disabled">
                {{ __('Modificar Cantidad') }}
                </x-secondary-button>
                
            @endif
            
        </x-slot>
    </x-dialog-modal>
    @endif

    <x-dialog-modal wire:model.live="cDescuento" maxWidth="2xl">
        <x-slot name="title">
            {{ __('Seleecionar Articulo') }}
        </x-slot>
        <x-slot name="content">
            {{-- Datos del artículo --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm">
                    <span class="font-mono text-xs text-gray-500">#{{ $id }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200 ml-2">{{ $art }}</span>
                    <span class="text-gray-500 ml-1">{{ $categoria }} - {{ $presentacion }}-{{ $unidad }}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-1 text-sm p-4">
                    <div><span class="text-gray-500">Precio inicial:</span> <b class="text-gray-800 dark:text-gray-200">${{ $precioI }}</b></div>
                    <div><span class="text-gray-500">Precio final:</span> <b class="text-gray-800 dark:text-gray-200">${{ $precioF }}</b></div>
                    <div><span class="text-gray-500">Descuento:</span> <b class="text-gray-800 dark:text-gray-200">{{ $descuento }}</b></div>
                    <div><span class="text-gray-500">Stock:</span> <b class="text-gray-800 dark:text-gray-200">{{ $stock }}</b></div>
                    <div><span class="text-gray-500">Stock mín.:</span> <b class="text-gray-800 dark:text-gray-200">{{ $stockMinimo }}</b></div>
                    <div><span class="text-gray-500">Caducidad:</span> <b class="text-gray-800 dark:text-gray-200">{{ $caducidad }}</b></div>
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
            {{ __('Eliminar articulo') }}
        </x-slot>

        <x-slot name="content">
            {{ __('¿Esta seguro de Desea Realizar esta Operacion de Venta') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button wire:click="$toggle('confirmarOpVenta', false)" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </x-danguer-button>

            <x-secondary-button class="ms-3" wire:click="ConfirmarVenta()" wire:loading.attr="disabled">
                {{ __('Realizar Venta') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
    {{-- ---- Fin modal confirmar venta---- --}}
    <!-- aDD User Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingClienteAdd" maxWidth="2xl">
        <x-slot name="title">
            {{ __('Cargar Cliente') }}
        </x-slot>
        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4">
                <x-label for="apellido" value="{{ __('Apellido') }}" />
                <x-input id="apellido" type="text" class="mt-1 block w-full" wire:model="apellido" name='apellido' />
                <x-input-error for="apellido" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="nombre" value="{{ __('Nombre') }}" />
                <x-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" name='nombre' />
                <x-input-error for="nombre" class="mt-2" />

            </div><div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="telefono" value="{{ __('Telefono') }}" />
                <x-input id="telefono" type="text" class="mt-1 block w-full" wire:model="telefono"  />
                <x-input-error for="telefono" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-danger-button wire:click="$toggle('confirmingClienteAdd', false)" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </x-danger-button>

            <x-secondary-button class="ms-3" wire:click="saveCliente()" wire:loading.attr="disabled">
                {{ __('Guardar') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

</div>

