<nav class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 flex flex-wrap items-center gap-1">

    <a href="{{ route('service.ingresarBike') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('service.ingresarBike')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        🚲 Ingresar Bicicleta
    </a>

    <a href="{{ route('service.egresoBici') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('service.egresoBici')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        🔧 Registro Servicio
    </a>

    <a href="{{ route('service.calendarioServicios') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('service.calendarioServicios')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        📅 Calendario
    </a>

    <a href="{{ route('service.cuentaMecanico') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('service.cuentaMecanico')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        💰 Cuenta Mecánico
    </a>

</nav>
