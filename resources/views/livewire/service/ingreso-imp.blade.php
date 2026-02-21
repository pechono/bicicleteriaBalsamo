<div class="flex">
    <div class=" w-10/12 space-y-4 p-4">
    
    {{-- ================= CABECERA CON TÍTULO Y ACCIONES ================= --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-4 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center gap-1">
                <div class="bg-white/20 p-1 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Detalle del Ingreso</h1>
                    <p class="text-blue-100">N° de ingreso: <span class="font-mono font-bold text-xl">In-{{$nro_ingreso}}</span></p>
                </div>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">

                <button 
                
                class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir 
                </button>
                <button 
                    
                    class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </button>
            </div>
        </div>
    </div>
            <div class="flex">
                {{-- ================= TARJETA DE LA BICICLETA ================= --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500 w-[25%]">
                    <div class="bg-gradient-to-r from-green-50 to-white px-4 py-3 border-b">
                        <h2 class="font-semibold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Datos de la Bicicleta
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1  gap-4">
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Marca</span>
                                <p class="font-semibold text-gray-800">{{$bicicleta->marca}}</p>
                            </div>
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Tipo</span>
                                <p class="font-semibold text-gray-800">{{$bicicleta->tipo}}</p>
                            </div>
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider mb-1 block">Colores</span>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs flex items-center gap-1">
                                        
                                        {{ $bicicleta->color }}
                                    </span>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             {{-- ================= TARJETA DEL CLIENTE ================= --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500 w-[25%]">
                    <div class="bg-gradient-to-r from-blue-50 to-white px-4 py-3 border-b">
                        <h2 class="font-semibold text-blue-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Datos del Cliente
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1  gap-4">
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Nombre completo</span>
                                <p class="font-semibold text-gray-800">{{$bicicleta->apellido}} {{$bicicleta->nombre}}</p>
                            </div>
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">DNI</span>
                                <p class="font-semibold text-gray-800">{{$bicicleta->dni}}</p>
                            </div>
                            <div class="bg-gray-50 p-1 rounded-lg">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">Teléfono</span>
                                <p class="font-semibold text-gray-800">{{$bicicleta->telefono}}</p>
                            </div>
                            
                        </div>
                    </div>
                </div>


                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-indigo-500 w-[50%]">
                    <div class="bg-gradient-to-r from-indigo-50 to-white px-4 py-3 border-b flex justify-between items-center">
                        <h2 class="font-semibold text-indigo-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Nota General
                        </h2>
                        <button class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Editar
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="bg-gray-50 p-4 rounded-lg min-h-[180px]">
                            <p class="text-gray-700 leading-relaxed">{{$bicicleta->detalles}}
                            </p>
                        </div>

                        <!-- Editor de nota (oculto por defecto) -->
                        <div class="mt-3 hidden">
                            <textarea rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">El cliente solicita revisión completa de la bicicleta. Prestar especial atención al cambio de marchas que no funciona correctamente.</textarea>
                            <div class="flex justify-end mt-2 gap-2">
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                                <button class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    {{-- ================= FECHA ESTIMADA DE RETIRO ================= --}}
    

    {{-- ================= PROCESOS Y NOTAS ================= --}}
    <div class="w-full flex flex-col md:flex-row gap-4">
        {{-- PROCESOS SELECCIONADOS --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500 w-full ">
            <div class="bg-gradient-to-r from-purple-50 to-white px-4 py-3 border-b">
                <h2 class="font-semibold text-purple-800 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    Procesos a Realizar
                </h2>
            </div>
            <div class="flex w-full">
                <!-- Proceso 1 -->
                @forelse ($procesos as $item)
                    <div class="border rounded-lg overflow-hidden   m-2 p-2">
                            <span class="font-medium text-gray-800">{{ $item->articulo . ' ' . $item->presentacion}}</span>
                    </div>                  
                @empty
                    <div class="border rounded-lg overflow-hidden   m-2 p-2">
                            <span class="font-medium text-gray-800">Sin procesos asignados</span>
                    </div>  
                @endforelse
               
            </div>

            <!-- Resumen de procesos -->
            <div class="bg-gray-50 px-4 py-3 border-t">
                <div class="flex justify-between items-center text-sm">
                    <span class="font-medium text-gray-600">Total de procesos:</span>
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-medium">3</span>
                </div>
                <div class="flex justify-between items-center text-sm mt-2">
                    <span class="font-medium text-gray-600">Precio total:</span>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">$8,500.00</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= ESTADO Y ACCIONES ================= --}}
    {{-- <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-gray-500">
        <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-3 border-b">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Estado y Acciones
            </h2>
        </div>
        <div class="p-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-1">
                    <span class="font-medium text-gray-700">Estado actual:</span>
                    <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pendiente" selected>⏳ Pendiente</option>
                        <option value="en_proceso">🔧 En proceso</option>
                        <option value="terminado">✅ Terminado</option>
                        <option value="entregado">🎯 Entregado</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button 
                    wire:click="imprimirComprobante"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generar Comprobantekkkk
                    </button>

        <div class="h-50% flex items-center justify-center mt-10">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg h-auto w-96 flex flex-col items-center justify-center border p-10">
                <a href="{{ route('imprimirIngreso',['nro_ingreso'=>$nro_ingreso]) }}" target="_blank" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">Imprimir Comprobante</a>

            </div>
        </div>



                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Marcar como Entregado
                    </button>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- ================= HISTORIAL DE CAMBIOS ================= --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-3 border-b">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Historial de Cambios
            </h2>
        </div>
        <div class="p-4">
            <div class="space-y-2 max-h-40 overflow-y-auto">
                <div class="text-sm border-l-2 border-blue-500 pl-3 py-1">
                    <span class="text-gray-500">15/02/2024 14:30</span>
                    <span class="text-gray-700"> - Ingreso creado</span>
                </div>
                <div class="text-sm border-l-2 border-yellow-500 pl-3 py-1">
                    <span class="text-gray-500">15/02/2024 15:45</span>
                    <span class="text-gray-700"> - Estado cambiado a: En proceso</span>
                </div>
                <div class="text-sm border-l-2 border-green-500 pl-3 py-1">
                    <span class="text-gray-500">16/02/2024 10:20</span>
                    <span class="text-gray-700"> - Fecha estimada actualizada: 25/02/2024</span>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 h-fit">
        <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-3 border-b">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Estado y Acciones
            </h2>
        </div>
        <div class="p-4">
            <div class=" md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-1">
                    <span class="font-medium text-gray-700">Estado actual:</span>
                    <input type="date" name="" id="" 
                    wire:model="fecha_retiro" 
                    wire:change="actualizarFechaRetiro"
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex gap-2">
                    <div class="h-50% flex items-center justify-center mt-10">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg h-auto w-96 flex flex-col items-center justify-center border p-10">
                            <a href="{{ route('imprimirIngreso',['nro_ingreso'=>$nro_ingreso]) }}" target="_blank" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">Imprimir Comprobante</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>