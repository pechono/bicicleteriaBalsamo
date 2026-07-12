{{-- resources/views/livewire/informes/mas-vendidos.blade.php --}}

<div class="bg-white p-6 rounded-xl shadow-lg ">
    <!-- Header con título y botones -->
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">🏆 Los Más Vendidos</h2>
            <p class="text-gray-500 text-sm mt-1">Análisis de productos con mejor rendimiento</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportarExcel" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Excel
            </button>
            <button wire:click="exportarPDF" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </button>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Período -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📅 Período</label>
                <select wire:model.live="periodo" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="semana">Última Semana</option>
                    <option value="mes">Último Mes</option>
                    <option value="anio">Último Año</option>
                    <option value="personalizado">Personalizado</option>
                </select>
            </div>
            
            <!-- Fechas personalizadas -->
            @if($periodo == 'personalizado')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📅 Desde</label>
                <input type="date" wire:model.live="fechaInicio" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📅 Hasta</label>
                <input type="date" wire:model.live="fechaFin" class="w-full rounded-lg border-gray-300">
            </div>
            @endif
            
            <!-- Categoría -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Categoría</label>
                <select wire:model.live="categoriaId" class="w-full rounded-lg border-gray-300">
                    <option value="">Todas</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Top Cantidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🏅 Mostrar Top</label>
                <select wire:model.live="topCantidad" class="w-full rounded-lg border-gray-300">
                    <option value="5">Top 5</option>
                    <option value="10">Top 10</option>
                    <option value="15">Top 15</option>
                    <option value="20">Top 20</option>
                </select>
            </div>
            
            <!-- Métrico -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📊 Ordenar por</label>
                <select wire:model.live="metricoTipo" class="w-full rounded-lg border-gray-300">
                    <option value="ingresos">💰 Ingresos</option>
                    <option value="cantidad">📦 Cantidad Vendida</option>
                    <option value="ganancia">📈 Ganancia</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <p class="text-blue-100 text-sm">💰 Ingresos Totales</p>
            <p class="text-2xl font-bold">${{ number_format($totalIngresos, 2) }}</p>
            @if($tendencia != 0)
            <div class="mt-2 text-xs">
                <span class="{{ $tendencia >= 0 ? 'text-green-300' : 'text-red-300' }}">
                    {{ $tendencia >= 0 ? '↑' : '↓' }} {{ number_format(abs($tendencia), 1) }}%
                </span>
                <span class="text-blue-100 ml-1">vs período anterior</span>
            </div>
            @endif
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <p class="text-green-100 text-sm">📊 Total Ventas</p>
            <p class="text-2xl font-bold">{{ number_format($totalVentas) }}</p>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <p class="text-purple-100 text-sm">📦 Unidades Vendidas</p>
            <p class="text-2xl font-bold">{{ number_format($totalProductos) }}</p>
        </div>
        
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <p class="text-orange-100 text-sm">🎫 Ticket Promedio</p>
            <p class="text-2xl font-bold">${{ number_format($ticketPromedio, 2) }}</p>
        </div>
    </div>
    
    <!-- Tabla de Productos Más Vendidos -->
    @if($articulosMasVendidos && $articulosMasVendidos->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ventas</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Prom.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ganancia</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($articulosMasVendidos as $index => $articulo)
                @php
                    $maxIngresos = $articulosMasVendidos->max('total_ingresos');
                    $porcentaje = $maxIngresos > 0 ? ($articulo->total_ingresos / $maxIngresos) * 100 : 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} text-sm font-bold">
                            {{ $index + 1 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $articulo->articulo }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $articulo->categoria }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        {{ number_format($articulo->total_cantidad) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        {{ number_format($articulo->numero_ventas) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        ${{ number_format($articulo->precio_promedio, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                        ${{ number_format($articulo->total_ingresos, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right">
                        ${{ number_format($articulo->total_ganancia, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay datos</h3>
        <p class="mt-1 text-sm text-gray-500">No se encontraron ventas en el período seleccionado.</p>
        <p class="mt-2 text-xs text-gray-400">Verifica que tengas registros en la tabla "ventas"</p>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex justify-between items-center text-sm text-gray-500">
            <div>
                <span class="font-medium">Total productos mostrados:</span> {{ $articulosMasVendidos ? $articulosMasVendidos->count() : 0 }}
            </div>
            <div>
                <span class="font-medium">Actualizado:</span> {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
    
    @if(session()->has('message'))
    <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
        {{ session('message') }}
    </div>
    @endif
</div>