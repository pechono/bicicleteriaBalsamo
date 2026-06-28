<x-app-layout>
    <x-slot name="header" class="flex justify-center pt-20">
       Imprimir Comprobante
    </x-slot>



    <div class="h-50% flex items-center justify-center mt-10">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg h-auto w-96 flex flex-col items-center justify-center border p-10">
            @if (session('mensaje'))
                <div class="mb-4 w-full text-center px-3 py-2 bg-green-100 text-green-800 rounded">{{ session('mensaje') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 w-full text-center px-3 py-2 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <a href="{{ route('comprobante',['operacion'=>$operacion]) }}" target="_blank" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">Imprimir Comprobante</a>

            <a href="{{ route('venta.reporte.whatsapp',['operacion'=>$operacion,'volver'=>$volver]) }}" class="mb-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Enviar por WhatsApp</a>

            <a href="{{ route($volver) }}"  class="px-4 py-2 bg-green-500 text-white rounded">Realizar Otra Operacion</a>
        </div>
    </div>

</x-app-layout>
