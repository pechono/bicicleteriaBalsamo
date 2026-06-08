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
                @if(session('message'))
                    <span class="text-xs text-green-600 mt-1">{{ session('message') }}</span>
                @endif
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
                <button wire:click="crearCategoria" type="button"
                    class="text-xs text-purple-600 hover:underline mt-1 self-start">
                    + Agregar categoría
                </button>
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

            <h2 class="text-lg font-semibold text-black mb-1">Agregar artículo al grupo</h2>

            {{-- Mensajes de éxito / error --}}
            @if(session('message'))
                <div class="mb-3 px-3 py-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                    ✅ {{ session('message') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    ❌ {{ session('error') }}
                </div>
            @endif
            @if($mensajeError != '-')
                <p class="mb-3 text-sm {{ str_contains($mensajeError,'✅') ? 'text-green-600' : 'text-red-600' }}">
                    {{ $mensajeError }}
                </p>
            @endif

            {{-- Fila 1: Código + Artículo --}}
            <div class="flex gap-3 mb-3">
                <div class="w-[28%]">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Código Prov.</label>
                    <x-input type="text" class="mt-1 block w-full text-sm"
                             wire:model="codigo" wire:change="comprobarCodigo" placeholder="Código"/>
                    <x-input-error for="codigo" class="mt-1" />
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Artículo *</label>
                    <x-input type="text" class="mt-1 block w-full text-sm"
                             wire:model="articulo" placeholder="Nombre del artículo"/>
                    <x-input-error for="articulo" class="mt-1" />
                </div>
            </div>

            {{-- Fila 2: Unidad de medida + Unidad de Venta --}}
            <div class="flex gap-3 mb-3">
                <div class="w-1/2">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Unidad de medida *</label>
                    <select wire:model="unidad_id" class="mt-1 block w-full text-sm rounded border-gray-300 shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="unidad_id" class="mt-1" />
                </div>
                <div class="w-1/2">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Unidad de Venta *</label>
                    <x-input type="text" class="mt-1 block w-full text-sm"
                             wire:model="unidadVenta" placeholder="Unidad / Pack"/>
                    <x-input-error for="unidadVenta" class="mt-1" />
                </div>
            </div>

            {{-- Fila 3: Precio Inicial + % + Calcular + Precio Final --}}
            <div class="flex gap-3 mb-3 items-end">
                <div class="flex-1">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Precio Inicial *</label>
                    <x-input type="number" step="0.01" class="mt-1 block w-full text-sm"
                             wire:model="precioI" placeholder="0.00"/>
                    <x-input-error for="precioI" class="mt-1" />
                </div>
                <div class="w-24">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">% Ganancia</label>
                    <x-input type="number" step="0.01" class="mt-1 block w-full text-sm"
                             wire:model="porcentaje" placeholder="0"/>
                </div>
                <div class="pb-0.5">
                    <button wire:click="calcular"
                            class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded shadow">
                        Calcular →
                    </button>
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Precio Final *</label>
                    <x-input type="number" step="0.01" class="mt-1 block w-full text-sm bg-green-50"
                             wire:model="precioF" placeholder="0.00"/>
                    <x-input-error for="precioF" class="mt-1" />
                </div>
            </div>

            {{-- Fila 4: Descuento + Stock Mínimo + Stock Actual --}}
            <div class="flex gap-3 mb-3">
                <div class="w-1/3">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Descuento (%) *</label>
                    <x-input type="number" class="mt-1 block w-full text-sm"
                             wire:model="descuento" placeholder="0"/>
                    <x-input-error for="descuento" class="mt-1" />
                </div>
                <div class="w-1/3">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Mínimo *</label>
                    <x-input type="number" class="mt-1 block w-full text-sm"
                             wire:model="stockMinimo" placeholder="0"/>
                    <x-input-error for="stockMinimo" class="mt-1" />
                </div>
                <div class="w-1/3">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Actual *</label>
                    <x-input type="number" class="mt-1 block w-full text-sm"
                             wire:model="stock" placeholder="0"/>
                    <x-input-error for="stock" class="mt-1" />
                </div>
            </div>

            {{-- Fila 5: Detalles --}}
            <div class="mb-4">
                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Detalles</label>
                <x-input type="text" class="mt-1 block w-full text-sm"
                         wire:model="detalles" placeholder="Descripción adicional (opcional)"/>
            </div>

            {{-- Botón --}}
            <div class="flex justify-end">
                <button wire:click="cargarArticulo" wire:loading.attr="disabled"
                        class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded shadow transition">
                    <span wire:loading.remove wire:target="cargarArticulo">💾 Cargar Artículo</span>
                    <span wire:loading wire:target="cargarArticulo">Guardando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MODAL PROVEEDOR                                     --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($modalProveedor)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         wire:click.self="cerrarModales">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-blue-600 text-white">
                <h3 class="text-lg font-bold">Nuevo Proveedor</h3>
                <button wire:click="cerrarModales" class="text-2xl leading-none hover:text-blue-200">×</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Nombre *</label>
                        <input wire:model="np_nombre" type="text" placeholder="Nombre del proveedor"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"/>
                        @error('np_nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Teléfono</label>
                        <input wire:model="np_telefono" type="text" placeholder="Teléfono"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"/>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Rubro</label>
                        <input wire:model="np_rubro" type="text" placeholder="Rubro"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"/>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Dirección</label>
                        <input wire:model="np_direccion" type="text" placeholder="Dirección"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"/>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Localidad</label>
                        <input wire:model="np_localidad" type="text" placeholder="Localidad"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"/>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Mail</label>
                        <input wire:model="np_mail" type="email" placeholder="correo@ejemplo.com"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"/>
                        @error('np_mail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input wire:model="np_activo" type="checkbox" id="np_activo" class="rounded border-gray-300 text-blue-600"/>
                        <label for="np_activo" class="text-sm text-gray-700">Activo</label>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button wire:click="cerrarModales"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancelar
                </button>
                <button wire:click="guardarProveedor"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Guardar Proveedor
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MODAL GRUPO                                         --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($modalGrupo)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         wire:click.self="cerrarModales">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-green-600 text-white">
                <h3 class="text-lg font-bold">Nuevo Grupo</h3>
                <button wire:click="cerrarModales" class="text-2xl leading-none hover:text-green-200">×</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @if(!$proveedor_id)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                        ⚠️ Primero seleccioná un proveedor en el formulario principal.
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                        Proveedor: <strong>{{ $proveedores->firstWhere('id', $proveedor_id)?->nombre }}</strong>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-gray-700">Nombre del grupo *</label>
                    <input wire:model="ng_nombre" type="text" placeholder="Ej: Cadenas, Frenos, Ruedas..."
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500"/>
                    @error('ng_nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Porcentaje de ganancia *</label>
                    <input wire:model="ng_porcentaje" type="number" step="0.01" min="0" placeholder="0.00"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500"/>
                    @error('ng_porcentaje') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button wire:click="cerrarModales"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancelar
                </button>
                <button wire:click="guardarGrupo"
                    class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    Guardar Grupo
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MODAL CATEGORÍA                                     --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($modalCategoria)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         wire:click.self="cerrarModales">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-purple-600 text-white">
                <h3 class="text-lg font-bold">Nueva Categoría</h3>
                <button wire:click="cerrarModales" class="text-2xl leading-none hover:text-purple-200">×</button>
            </div>
            <div class="px-6 py-5">
                <label class="text-sm font-medium text-gray-700">Nombre de la categoría *</label>
                <input wire:model="nc_nombre" type="text" placeholder="Ej: MdO, Repuestos, Accesorios..."
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-purple-500 focus:border-purple-500"/>
                @error('nc_nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button wire:click="cerrarModales"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancelar
                </button>
                <button wire:click="guardarCategoria"
                    class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                    Guardar Categoría
                </button>
            </div>
        </div>
    </div>
    @endif

</div>