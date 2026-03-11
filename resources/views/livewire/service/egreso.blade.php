<div class="space-y-6 p-4 ">
    <!-- Buscadores separados -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 w-7/12 ">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Buscar Ingresos
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ">
            <!-- Buscador por N° de Ingreso -->
            <div class="w-fit">
                <label class=" text-sm font-medium text-gray-600 mb-1 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    N° de Ingreso
                </label>
                <input
                    wire:model.live="searchIngreso"
                    type="text"
                    placeholder="Ej: 8, 15, 23..."
                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                />
            </div>
            
            <!-- Buscador por Datos del Cliente -->
            <div class="w-full">
                <label class=" text-sm font-medium text-gray-600 mb-1 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Cliente (nombre, apellido, DNI, teléfono)
                </label>
                <input
                    wire:model.live="searchCliente"
                    type="text"
                    placeholder="Buscar por datos del cliente..."
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm"
                />
            </div>
        </div>
        
        <!-- Indicadores de búsqueda activa -->
        <div class="flex flex-wrap gap-2 mt-3">
            @if($searchIngreso)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    Ingreso: {{ $searchIngreso }}
                    <button wire:click="$set('searchIngreso', '')" class="ml-1 hover:text-blue-600">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            
            @if($searchCliente)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Cliente: {{ $searchCliente }}
                    <button wire:click="$set('searchCliente', '')" class="ml-1 hover:text-green-600">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="w-full">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Listado de Ingresos
                </h3>
                
                <!-- Contador de resultados -->
                <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">
                    {{ $clientes->total() }} resultados
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                            <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center border-r " rowspan="2">N° Ingreso</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center border-r" colspan="4">Propietario</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center" colspan="4">Bicicleta</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center border-l" rowspan="2">Acción</th>
                        </tr>
                        
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Nombre</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Apellido</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">DNI</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Teléfono</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Marca</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Tipo</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Color</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase tracking-wider border-r">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($clientes as $cliente)
                        <!-- Fila principal -->
                        <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $ver == $cliente->nro_ingreso ? 'bg-blue-50' : '' }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    #{{ $cliente->nro_ingreso }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">{{ $cliente->nombre }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">{{ $cliente->apellido }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">{{ $cliente->dni }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">{{ $cliente->telefono }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">{{ $cliente->marca }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ $cliente->tipo }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r ">
                                <span class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $cliente->color }}"></span>
                                    {{ $cliente->color }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r">{{ $cliente->estado }}</td>

                            <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                    
                                    @if ($ver == $cliente->nro_ingreso)
                                        @if($cliente->estado=='Pendiente' || $cliente->estado=='pendiente')
                                            <button 
                                                wire:click="terminarProceso({{ $cliente->nro_ingreso }})" placeholder="Terminar Proceso"
                                                class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow" >
                                            Terminar 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />    
                                                </svg>
                                            </button>
                                        @endif
                                        @if($cliente->estado=='Completado()' || $cliente->estado=='completado')
                                            <button 
                                                wire:click="cerrarDetalles" placeholder="Terminar Proceso"
                                                class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow" >
                                                <h3>cerrar</h3>
                                               

                                            </button>
                                        @endif

                                        
                                    @else
                                        <button 
                                        wire:click="verCliente({{ $cliente->nro_ingreso }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </button>
                                    @endif
                                    


                                </td>
                            </tr>

                            <!-- Fila expandible con artículos -->
                            @if ($ver == $cliente->nro_ingreso)
                                    @if($cliente->estado=='Completado' || $cliente->estado=='completado')
                                    <tr> 
                                        
                                        <td colspan="7" class="px-6 py-4">
    <div class="flex flex-col w-full">
    <div class="flex items-start gap-4">
        <!-- Tabla -->
        <table class="w-10/12 border-collapse border border-gray-300 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Código</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Artículo</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($procesosTerminado as $item)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2">{{$item->id ?? 'N/A'}}</td>
                    <td class="border border-gray-300 px-4 py-2">{{$item->codigo_proveedor}}-{{$item->codigo}}</td>
                    <td class="border border-gray-300 px-4 py-2">{{$item->articulo}}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                            {{$item->cantidad}}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="border border-gray-300 px-6 py-8">
                        <div class="bg-white rounded-lg p-6 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h1 class="text-xl font-bold text-gray-800">¡Sin procesos terminados!</h1>
                                <p class="text-gray-600">No hay datos disponibles para mostrar en este momento.</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Botón a la par de la tabla -->
        <div class="flex-1 justify-center">
            <button 
                                                wire:click="terminarProcesoVenta({{ $cliente->nro_ingreso }})" placeholder="Terminar Proceso"
                                                class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow" >
                                                <h3>Entregar</h3>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.99 7.5 3.75-3.75m0 0 3.75 3.75m-3.75-3.75v16.499H4.49" />
                                                </svg>

                                            </button>
        </div>
    </div>
</div>
</td>
                                    </tr>
                                    @else    
                                        <tr class="bg-blue-50 border-t-2 border-blue-200">
                                                <td colspan="7" class="px-6 py-4">
                                                    <div class="bg-white rounded-lg shadow-inner p-4">
                                                        <div class="flex items-center mb-3 justify-between">
                                                            <div>
                                                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                                </svg>
                                                                <h4 class="text-md font-semibold text-gray-800">Artículos del ingreso #{{ $cliente->nro_ingreso }}</h4>
                                                            </div>
                                                            <div>
                                                                <button 
                                                                    wire:click="cerrarDetalles()"
                                                                    class="ml-auto inline-flex items-center px-2 py-1 bg-gray-200 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-300 transition-colors duration-150">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Cerrar
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @forelse ($procesos as $item)
                                                        <span class="inline-flex items-center bg-gray-100 rounded-full px-3 py-1 text-sm font-medium text-gray-700 mr-2 mb-2">
                                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                            #{{ $item->id }} - {{ $item->articulo }}
                                                        </span>
                                                        @empty
                                                        <div class="text-center py-4">
                                                            <p class="text-gray-500">No hay artículos asociados a este ingreso</p>
                                                        </div>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                
                                                <td colspan="3" class="px-6 py-4">
                                                    <div class="bg-white rounded-lg shadow-inner p-4">
                                                        <div class="flex items-center mb-3 justify-between">
                                                            <div>
                                                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <h4 class="text-md font-semibold text-gray-800">Detalles del ingreso #{{ $nDetalles->detalles }}</h4>
                                                            </div>
                                                            <div>
                                                                <button 
                                                                    wire:click="cerrarDetalles()"
                                                                    class="ml-auto inline-flex items-center px-2 py-1 bg-gray-200 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-300 transition-colors duration-150">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Cerrar
                                                                </button>
                                                            </div>

                                                </td>

                                            </tr>
                                    @endif
                            @endif
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-4 text-gray-500 text-lg">No se encontraron ingresos</p>
                                <p class="text-gray-400">Probá con otros términos de búsqueda</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Paginación -->
    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>