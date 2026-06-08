<nav class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 flex flex-wrap items-center gap-1">

    <a href="{{ route('stock.index') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('stock.index')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        📦 Stock
    </a>

    <a href="{{ route('stock.pedido') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('stock.pedido')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        🛒 Pedido a Proveedor
    </a>

    <a href="{{ route('stock.pedidoRealizado') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              {{ request()->routeIs('stock.pedidoRealizado')
                 ? 'bg-indigo-600 text-white shadow-sm'
                 : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
        📋 Pedidos Realizados
    </a>

    <a href="{{ route('stockImprimir') }}" target="_blank"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
              text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
        🖨️ Imprimir Stock
    </a>

</nav>
