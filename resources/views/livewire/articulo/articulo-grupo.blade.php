<div class="">

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg m-2 p-4">
        <div class="flex flex-col md:flex-row md:items-start md:space-x-4 space-y-4 md:space-y-0">

            <!-- Proveedor -->
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <label for="proveedor" class="text-sm font-medium text-black mb-1">Proveedor</label>
                    <select wire:model.live="proveedor_id" id="proveedor"
                            wire:change="mostrarGrupo"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar proveedor...</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @error('proveedor_id') 
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <button wire:click="crearProveedor" type="button"
                    class="text-xs text-blue-600 hover:underline mt-1 self-start">
                    + Agregar proveedor
                </button>
            </div>

            <!-- Grupo -->
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <label for="grupo" class="text-sm font-medium text-black mb-1">Grupo</label>
                    <select wire:model="grupo" id="grupo"
                            wire:change="articulosGrupos"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar grupo...</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}">{{ $g->NombreGrupo }}</option>
                        @endforeach
                    </select>
                </div>
                @error('grupo') 
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <button wire:click="crearGrupo" type="button"
                    class="text-xs text-green-600 hover:underline mt-1 self-start">
                    + Agregar grupo
                </button>
            </div>

            <div class="flex flex-col">
                <div class="flex flex-col">
                        <label for="categoria" class="text-sm font-medium text-black mb-1">Categoria</label>
                        <select id="categoria" wire:model="categoria_id" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">
                                    {{ $categoria->id }} - {{ $categoria->categoria }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id') 
                          <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                </div> 
            </div>

            <!-- Botón Seleccionar -->
            <div class="flex flex-col justify-end pt-[28px]">
                <button wire:click="seleccionar" type="button"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow w-full">
                    Seleccionar
                </button>
            </div>
        </div>
    </div>
   
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 m-2 justify-center">

        <!-- 📋 Lista de artículos del grupo (MEJORADA) -->
        <div class="bg-white rounded-lg shadow-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-black">Artículos en el grupo</h2>
                
                <!-- SELECTOR DE ÍTEMS POR PÁGINA -->
                @if(isset($articulosGrupo) && $articulosGrupo->count() > 0)
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Mostrar:</label>
                    <select wire:model.live="paginacionPorDefecto" 
                            wire:change="actualizarPaginacion($event.target.value)"
                            class="border rounded-md text-sm p-1">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                @endif
            </div>

            @if(isset($articulosGrupo) && $articulosGrupo->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-black">ID</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-black">Código Prov</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-black">Artículo</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-black">Presentación</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-black">Unidad/Venta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($articulosGrupo as $articulo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $articulo->id }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $articulo->codigo_proveedor ?? '' }}{{ $articulo->codigo }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium text-black">
                                        {{ $articulo->articulo }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $articulo->presentacion }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $articulo->unidadVenta }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- INFORMACIÓN DE PAGINACIÓN -->
                <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div class="text-sm text-gray-600">
                        Mostrando {{ $articulosGrupo->firstItem() }} - {{ $articulosGrupo->lastItem() }} 
                        de {{ $articulosGrupo->total() }} artículos
                    </div>
                    
                    <!-- LINKS DE PAGINACIÓN -->
                    <div class="flex space-x-1">
                        {{ $articulosGrupo->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 text-sm">No hay artículos en este grupo.</p>
                    @if($grupo)
                        <p class="text-xs text-gray-400 mt-2">Agregue artículos usando el formulario de la derecha.</p>
                    @else
                        <p class="text-xs text-gray-400 mt-2">Seleccione un grupo para ver sus artículos.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- 🆕 Formulario para agregar artículo -->
        <div class="bg-white rounded-lg shadow-xl p-4">
            <h2 class="text-lg font-semibold text-black mb-4">
                Agregar artículo al grupo 
                @if($mensajeError != '-')
                    <span class="text-sm font-normal ml-2">{{ $mensajeError }}</span>
                @endif
            </h2>
            
            <div class="col-span-6 sm:col-span-4 flex gap-4 mb-4">
                <!-- Campo Código (30%) -->
                <div class="w-[30%]">
                    <label for="codigo" class="text-sm font-medium text-black mb-1">Código Proveedor</label>
                    <x-input id="codigo" type="text" class="mt-1 block w-full bg-white" 
                             wire:model="codigo" wire:change="comprobarCodigo" placeholder="Código"/>
                    <x-input-error for="codigo" class="mt-2" />
                </div>
                
                <!-- Campo Artículo (70%) -->
                <div class="flex-grow">
                    <label for="articulo" class="text-sm font-medium text-black mb-1">Artículo</label>
                    <x-input id="articulo" type="text" class="mt-1 block w-full" 
                             wire:model="articulo" placeholder="Artículo"/>
                    <x-input-error for="articulo" class="mt-2" />
                </div>
            </div>

            <!-- Presentación y Unidad -->
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label for="presentacion" class="text-sm font-medium text-black mb-1">Presentación</label>
                    <x-input id="presentacion" type="text" class="mt-1 block w-full" 
                             wire:model="presentacion" placeholder="Presentación"/>
                    <x-input-error for="presentacion" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <label for="unidad" class="text-sm font-medium text-black mb-1">Unidad</label>
                    <select id="unidad" wire:model="unidad_id" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Seleccionar...</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="unidad_id" class="mt-2" />
                </div>
            </div>

            <!-- Descuento y Unidad Venta -->
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label for="descuento" class="text-sm font-medium text-black mb-1">Descuento (%)</label>
                    <x-input id="descuento" type="number" class="mt-1 block w-full" 
                             wire:model="descuento" placeholder="0"/>
                    <x-input-error for="descuento" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <label for="unidadVenta" class="text-sm font-medium text-black mb-1">Unidad de Venta</label>
                    <x-input id="unidadVenta" type="text" class="mt-1 block w-full" 
                             wire:model="unidadVenta" placeholder="Unidad/Pack"/>
                    <x-input-error for="unidadVenta" class="mt-2" />
                </div>
            </div>

            <!-- Precios y Porcentaje -->
            <div class="flex gap-4 mb-4 items-end">
                <div class="w-1/4">
                    <label for="precioI" class="text-sm font-medium text-black mb-1">Precio Inicial</label>
                    <x-input id="precioI" type="number" step="0.01" class="mt-1 block w-full" 
                             wire:model="precioI" placeholder="0.00"/>
                    <x-input-error for="precioI" class="mt-2" />
                </div>
                <div class="w-1/4">
                    <label for="precioF" class="text-sm font-medium text-black mb-1">Precio Final</label>
                    <x-input id="precioF" type="number" step="0.01" class="mt-1 block w-full" 
                             wire:model="precioF" placeholder="0.00"/>
                    <x-input-error for="precioF" class="mt-2" />
                </div>
                <div class="w-1/4">
                    <label for="porcentaje" class="text-sm font-medium text-black mb-1">Porcentaje</label>
                    <x-input id="porcentaje" type="number" step="0.01" class="mt-1 block w-full" 
                             wire:model="porcentaje" placeholder="0"/>
                    <x-input-error for="porcentaje" class="mt-2" />
                </div>
                <div class="w-1/4">
                    <button wire:click="calcular" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                        Calcular
                    </button>
                </div>
            </div>

            <!-- Stock -->
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label for="stockMinimo" class="text-sm font-medium text-black mb-1">Stock Mínimo</label>
                    <x-input id="stockMinimo" type="number" class="mt-1 block w-full" 
                             wire:model="stockMinimo" placeholder="0"/>
                    <x-input-error for="stockMinimo" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <label for="stock" class="text-sm font-medium text-black mb-1">Stock Actual</label>
                    <x-input id="stock" type="number" class="mt-1 block w-full" 
                             wire:model="stock" placeholder="0"/>
                    <x-input-error for="stock" class="mt-2" />
                </div>
            </div>

            <!-- Detalles -->
            <div class="mb-4">
                <x-label for="detalles" value="Detalles" />
                <x-input id="detalles" type="text" class="mt-1 block w-full" wire:model="detalles" />
                <x-input-error for="detalles" class="mt-2" />
            </div>

            <!-- Botón Cargar -->
            <div class="flex justify-end">
                <button wire:click="cargarArticulo" 
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                    Cargar Artículo
                </button>
            </div>
        </div>
    </div>
</div>