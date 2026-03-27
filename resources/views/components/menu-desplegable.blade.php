<button onclick="openNav()" class="menu-button fixed top-4 left-4 z-50 group flex items-center justify-start rounded-full bg-green-500 hover:bg-blue-500 transition-all duration-300 shadow-md hover:shadow-lg cursor-pointer" style="width: 40px; height: 40px;">
    <div class="relative flex items-center justify-start w-full h-full">
        <!-- Icono -->
        <div class="absolute left-0 top-0 w-10 h-10 flex items-center justify-center">
            <svg class="w-5 h-5 text-white transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </div>
        
        <!-- Texto que aparece -->
        <div class="absolute left-10 whitespace-nowrap text-white font-medium text-sm">
            <span class="opacity-0 group-hover:opacity-100 transition-all duration-300 delay-75">
            Menú
            </span>
        </div>
    </div>
</button>

<style>
    .menu-button {
        transition: width 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        transform-origin: left center;
        will-change: width;
    }
    
    .menu-button:hover {
        width: 110px !important;
    }
    
    /* Evitar que el texto cause scroll */
    .menu-button .absolute {
        pointer-events: none;
    }
</style>
<!-- Menú Lateral -->
<div id="mySidebar" class="fixed inset-y-0 left-0 w-0 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 overflow-x-hidden transition-all duration-500 ease-out z-50 shadow-2xl border-r border-gray-200 dark:border-gray-800">
    <!-- Header con gradiente suave -->
    <div class="relative pt-8 pb-6 px-5 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        <button class="absolute top-8 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-all duration-200" onclick="closeNav()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Bicicletería</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Sistema de Gestión</p>
            </div>
        </div>
    </div>
    
    <nav class="py-5 px-3 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3 px-3 py-3 rounded-xl transition-all duration-200 group">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Dashboard</span>
        </a>

        <!-- Venta -->
        <div class="menu-group">
            <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="ventaSubMenu">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 dark:text-gray-400 dark:group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"></path>
                    </svg>
                    <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Venta</span>
                </div>
                <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="ventaSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                <a href="{{ route('venta.ventaExpress') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">🚀 Venta Express</a>
                <a href="{{ route('venta.ventaCard') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">💳 Venta Card</a>
                <a href="{{ route('venta.cuentaCorriente') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">📊 Cuenta Corriente</a>
                <a href="{{ route('venta.listCuentaCorriente') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">💰 Pago Cuenta Corriente</a>
            </div>
        </div>

        <!-- Servicio -->
        <div class="menu-group">
            <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="servicioSubMenu">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z"></path>
                    </svg>
                    <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Servicio</span>
                </div>
                <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="servicioSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                <a href="{{ route('service.ingresarBike') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">🚲 Ingresar Bicicleta</a>
                <a href="{{ route('service.egresoBici') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">🔧 Registro Servicio</a>
            </div>
        </div>

        <!-- Stock -->
        <div class="menu-group">
            <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="stockSubMenu">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-amber-600 dark:text-gray-400 dark:group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                    </svg>
                    <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Stock</span>
                </div>
                <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="stockSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                <a href="{{ route('stock.index') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">📦 Ver Stock</a>
                <a href="{{ route('stock.pedido') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">📝 Pedido a Proveedor</a>
                <a href="{{ route('stock.pedidoRealizado') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">✅ Pedidos Realizados</a>
                <a href="{{ route('stockImprimir') }}" target="_blank" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">🖨️ Imprimir Stock</a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="menu-group">
            <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="informesSubMenu">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 dark:text-gray-400 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"></path>
                    </svg>
                    <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Estadísticas</span>
                </div>
                <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="informesSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                <a href="{{ route('informes.masVendidos') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">📊 Más Vendidos</a>
            </div>
        </div>

        <!-- Operaciones -->
        <div class="menu-group">
            <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="operacionSubMenu">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-600 dark:text-gray-400 dark:group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.22-1.113-.615-1.518a5.25 5.25 0 00-4.224-1.44 5.25 5.25 0 00-4.476 4.825c-.065.545-.1 1.093-.1 1.641v.75"></path>
                    </svg>
                    <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Operaciones</span>
                </div>
                <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div id="operacionSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                <a href="{{ route('operacion.list') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">📋 Operaciones</a>
                <a href="{{ route('venta.list') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base transition-all duration-200">💰 Ventas</a>
            </div>
        </div>

        <!-- Separador Admin -->
        <x-admin>
            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white dark:bg-gray-900 px-2 text-xs text-gray-400 uppercase tracking-wider font-medium">Administración</span>
                </div>
            </div>

            <!-- Gestión -->
            <div class="menu-group">
                <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="gestionSubMenu">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"></path>
                        </svg>
                        <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Gestión</span>
                    </div>
                    <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="gestionSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                    <a href="{{ route('articulo.articuloGrupo') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">📦 Artículo</a>
                    <a href="{{ route('gestion.precio.precioCambiar') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">💰 Cambio Precio Artículos</a>
                    <a href="{{ route('gestion.precio.precioGrupo') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">📊 Cambio Precio Grupo</a>
                    <a href="{{ route('proveedor.proveedor') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">🏭 Proveedor</a>
                    <a href="{{ route('admin.gestionUsuario') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">👥 Gestionar Usuarios</a>
                </div>
            </div>

            <!-- Ofertas -->
            <div class="menu-group">
                <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="ofertaSubMenu">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-600 dark:text-gray-400 dark:group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"></path>
                        </svg>
                        <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Ofertas</span>
                    </div>
                    <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="ofertaSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                    <a href="{{ route('oferta.ofertaList') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">🏷️ Ofertas</a>
                    <a href="{{ route('oferta.ofertaCreate') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">✨ Crear</a>
                    <a href="{{ route('oferta.ofertaGestion') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">⚙️ Operaciones</a>
                </div>
            </div>

            <!-- Usuario -->
            
        </x-admin>
        <div class="menu-group">
                <button class="menu-toggle w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group" data-menu="userSubMenu">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-cyan-600 dark:text-gray-400 dark:group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                        </svg>
                        <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Usuario</span>
                    </div>
                    <svg class="chevron w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="userSubMenu" class="submenu pl-10 mt-1 space-y-1 hidden">
                    <a href="{{ route('profile') }}" class="sub-link flex items-center space-x-2 px-3 py-2.5 rounded-xl text-base">👥 Cambia Password</a>
                </div>
            </div>
        <!-- Cierre Caja -->
        <a href="{{ route('cierre.cierreCaja') }}" class="nav-link flex items-center space-x-3 px-3 py-3 rounded-xl transition-all duration-200 group mt-2">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-red-600 dark:text-gray-400 dark:group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"></path>
            </svg>
            <span class="text-base font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white">Cierre Caja</span>
        </a>
    </nav>
</div>

<script>
    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
    }

    function openNav() {
        document.getElementById("mySidebar").style.width = "300px";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.menu-toggle');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menuId = this.getAttribute('data-menu');
                const currentSubmenu = document.getElementById(menuId);
                const currentChevron = this.querySelector('.chevron');
                
                document.querySelectorAll('.submenu').forEach(submenu => {
                    if (submenu.id !== menuId && !submenu.classList.contains('hidden')) {
                        submenu.classList.add('hidden');
                        const otherToggle = document.querySelector(`.menu-toggle[data-menu="${submenu.id}"]`);
                        if (otherToggle) {
                            const otherChevron = otherToggle.querySelector('.chevron');
                            if (otherChevron) otherChevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });
                
                if (currentSubmenu.classList.contains('hidden')) {
                    currentSubmenu.classList.remove('hidden');
                    if (currentChevron) currentChevron.style.transform = 'rotate(90deg)';
                } else {
                    currentSubmenu.classList.add('hidden');
                    if (currentChevron) currentChevron.style.transform = 'rotate(0deg)';
                }
            });
        });
    });
</script>

<style>
    /* Estilos base mejorados */
    .nav-link, .menu-toggle, .sub-link {
        transition: all 0.2s ease;
    }
    
    .nav-link:hover, .menu-toggle:hover {
        background-color: #f3f4f6;
    }
    
    .dark .nav-link:hover, .dark .menu-toggle:hover {
        background-color: #1f2937;
    }
    
    .sub-link {
        color: #6b7280;
    }
    
    .sub-link:hover {
        background-color: #f9fafb;
        color: #111827;
        transform: translateX(4px);
    }
    
    .dark .sub-link {
        color: #9ca3af;
    }
    
    .dark .sub-link:hover {
        background-color: #1f2937;
        color: #f9fafb;
    }
    
    /* Animación del botón de menú */
    .menu-button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .menu-button:hover {
        transform: scale(1.05);
    }
    
    /* Animaciones */
    .submenu {
        transition: all 0.2s ease-out;
    }
    
    .submenu:not(.hidden) {
        animation: fadeSlide 0.2s ease-out;
    }
    
    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Scrollbar mejorada */
    #mySidebar::-webkit-scrollbar {
        width: 4px;
    }
    
    #mySidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .dark #mySidebar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    #mySidebar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }
    
    .dark #mySidebar::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    
    #mySidebar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

</style>


{{-- <div id="mySidebar" class="fixed inset-y-0 left-0 w-0 bg-gradient-to-b from-gray-900 to-gray-800 text-white overflow-x-hidden transition-all duration-500 z-50 shadow-2xl">
    <button class="absolute top-4 right-4 text-3xl text-gray-400 hover:text-white transition-colors duration-200" onclick="closeNav()">&times;</button>
    
    <nav class="pt-6 space-y-2">
        <!-- Home -->
        <div class="hoverDiv group">
            <a href="{{ route('dashboard') }}" class="py-3 px-4 hover:bg-gray-700/50 flex items-center transition-all duration-200 rounded-lg mx-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 4l9 5.75v11.25h-6v-6h-6v6H3V9.75z" />
                </svg>
                <span class="movableDiv transition-all duration-300 text-lg font-medium">Dashboard</span>
            </a>
        </div>

        <!-- Venta -->
        <div class="hoverDiv group">
            <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="ventaSubMenu">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-green-400 group-hover:text-green-300 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Venta</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div id="ventaSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                <a href="{{ route('venta.ventaExpress') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🚀 Venta Express</a>
                <a href="{{ route('venta.ventaCard') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">💳 Venta Card</a>
                <a href="{{ route('venta.cuentaCorriente') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📊 Cuenta Corriente</a>
                <a href="{{ route('venta.listCuentaCorriente') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">💰 Pago Cuenta Corriente</a>
            </div>
        </div>

        <!-- Servicio -->
        <div class="hoverDiv group">
            <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="servicioSubMenu">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-blue-400 group-hover:text-blue-300 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Servicio</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div id="servicioSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                <a href="{{ route('service.ingresarBike') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🚲 Ingresar Bicicleta</a>
                <a href="{{ route('service.egresoBici') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🔧 Registro Servicio</a>
            </div>
        </div>

        <!-- Stock -->
        <div class="hoverDiv group">
            <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="stockSubMenu">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-yellow-400 group-hover:text-yellow-300 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Stock</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div id="stockSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                <a href="{{ route('stock.index') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📦 Ver Stock</a>
                <a href="{{ route('stock.pedido') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📝 Pedido a Proveedor</a>
                <a href="{{ route('stock.pedidoRealizado') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">✅ Pedidos Realizados</a>
                <a href="{{ route('stockImprimir') }}" target="_blank" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🖨️ Imprimir Stock</a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="hoverDiv group">
            <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="informesSubMenu">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-indigo-400 group-hover:text-indigo-300 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Estadísticas</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div id="informesSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                <a href="{{ route('informes.masVendidos') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📊 Más Vendidos</a>
            </div>
        </div>

        <!-- Operaciones -->
        <div class="hoverDiv group">
            <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="operacionSubMenu">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-orange-400 group-hover:text-orange-300 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Operaciones</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div id="operacionSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                <a href="{{ route('operacion.list') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📋 Operaciones</a>
                <a href="{{ route('venta.list') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">💰 Ventas</a>
            </div>
        </div>

        <!-- Sección Admin (solo visible para administradores) -->
        <x-admin>
            <!-- Gestión -->
            <div class="hoverDiv group">
                <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="gestionSubMenu">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-gray-400 group-hover:text-gray-300 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Gestión</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <div id="gestionSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                    <a href="{{ route('articulo.articuloGrupo') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📦 Artículo</a>
                    <a href="{{ route('gestion.precio.precioCambiar') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">💰 Cambio de Precio - Artículos</a>
                    <a href="{{ route('gestion.precio.precioGrupo') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">📊 Cambio de Precio - Grupo</a>
                    <a href="{{ route('proveedor.proveedor') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🏭 Proveedor</a>
                </div>
            </div>

            <!-- Ofertas -->
            <div class="hoverDiv group">
                <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="ofertaSubMenu">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-purple-400 group-hover:text-purple-300 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Ofertas</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <div id="ofertaSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                    <a href="{{ route('oferta.ofertaList') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">🏷️ Ofertas</a>
                    <a href="{{ route('oferta.ofertaCreate') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">✨ Crear</a>
                    <a href="{{ route('oferta.ofertaGestion') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">⚙️ Operaciones</a>
                </div>
            </div>

            <!-- Usuario (Administración) -->
            <div class="hoverDiv group">
                <a href="javascript:void(0)" class="toggle-submenu py-3 px-4 hover:bg-gray-700/50 flex items-center justify-between transition-all duration-200 rounded-lg mx-2" data-menu="userSubMenu">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-cyan-400 group-hover:text-cyan-300 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Administración</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-transform duration-300 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <div id="userSubMenu" class="submenu pl-8 hidden space-y-2 mt-1">
                    <a href="{{ route('admin.gestionUsuario') }}" class="block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">👥 Gestionar Usuarios</a>
                </div>
            </div>
        </x-admin>

        <!-- Cierre Caja -->
        <div class="hoverDiv group">
            <a href="{{ route('cierre.cierreCaja') }}" class="py-3 px-4 hover:bg-gray-700/50 flex items-center transition-all duration-200 rounded-lg mx-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-red-400 group-hover:text-red-300 transition-colors">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span class="movableDiv transition-all duration-300 text-white text-lg font-medium">Cierre Caja</span>
            </a>
        </div>
    </nav>
</div>

<script>
    // Función para cerrar el menú
    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
    }

    // Función para abrir el menú (llamar desde otro lugar)
    function openNav() {
        document.getElementById("mySidebar").style.width = "300px";
    }

    // Funcionalidad de acordeón: al abrir un submenú, se cierran los demás
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.toggle-submenu');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menuId = this.getAttribute('data-menu');
                const currentSubmenu = document.getElementById(menuId);
                const currentChevron = this.querySelector('.chevron-icon');
                
                // Cerrar todos los demás submenús
                document.querySelectorAll('.submenu').forEach(submenu => {
                    if (submenu.id !== menuId && !submenu.classList.contains('hidden')) {
                        submenu.classList.add('hidden');
                        // Rotar el chevron del otro menú
                        const otherToggle = document.querySelector(`.toggle-submenu[data-menu="${submenu.id}"]`);
                        if (otherToggle) {
                            const otherChevron = otherToggle.querySelector('.chevron-icon');
                            if (otherChevron) {
                                otherChevron.style.transform = 'rotate(0deg)';
                            }
                        }
                    }
                });
                
                // Toggle del submenú actual
                if (currentSubmenu.classList.contains('hidden')) {
                    currentSubmenu.classList.remove('hidden');
                    if (currentChevron) {
                        currentChevron.style.transform = 'rotate(90deg)';
                    }
                } else {
                    currentSubmenu.classList.add('hidden');
                    if (currentChevron) {
                        currentChevron.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });
    });

    // Efecto hover para los elementos del menú
    document.querySelectorAll('.hoverDiv').forEach(div => {
        const movableDiv = div.querySelector('.movableDiv');
        if (movableDiv) {
            div.addEventListener('mouseenter', () => {
                movableDiv.classList.add('move-left');
            });
            div.addEventListener('mouseleave', () => {
                movableDiv.classList.remove('move-left');
            });
        }
    });
</script>

<style>
    .move-left {
        transform: translateX(-8px);
    }
    
    /* Estilo para la barra de desplazamiento */
    #mySidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #mySidebar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    #mySidebar::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 5px;
    }
    
    #mySidebar::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
    
    /* Animación para los submenús */
    .submenu {
        transition: all 0.2s ease-in-out;
    }
</style> --}}


{{-- 8-10 --}}
{{-- <div id="mySidebar" class="fixed inset-y-0 left-0 w-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white overflow-x-hidden transition-all duration-500 ease-out z-50 shadow-2xl backdrop-blur-sm">
    <button class="absolute top-4 right-4 text-3xl text-gray-400 hover:text-white hover:rotate-90 transition-all duration-300" onclick="closeNav()">&times;</button>
    
    <nav class="pt-6 space-y-1.5">
        <!-- Home -->
        <div class="hoverDiv group relative">
            <a href="{{ route('dashboard') }}" class="nav-item py-3 px-4 flex items-center transition-all duration-300 rounded-xl mx-2 relative overflow-hidden">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-400 group-hover:text-white group-hover:scale-110 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 4l9 5.75v11.25h-6v-6h-6v6H3V9.75z" />
                </svg>
                <span class="movableDiv transition-all duration-300 text-lg font-medium tracking-wide">Dashboard</span>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 group-hover:w-full transition-all duration-500"></span>
            </a>
        </div>

        <!-- Venta -->
        <div class="hoverDiv group relative">
            <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="ventaSubMenu">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-green-400 group-hover:text-green-300 group-hover:scale-110 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Venta</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-400 group-hover:w-full transition-all duration-500"></span>
            </a>
            <div id="ventaSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                <a href="{{ route('venta.ventaExpress') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">🚀 Venta Express</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('venta.ventaCard') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">💳 Venta Card</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('venta.cuentaCorriente') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">📊 Cuenta Corriente</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('venta.listCuentaCorriente') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">💰 Pago Cuenta Corriente</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Servicio -->
        <div class="hoverDiv group relative">
            <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="servicioSubMenu">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-blue-400 group-hover:text-blue-300 group-hover:scale-110 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Servicio</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 group-hover:w-full transition-all duration-500"></span>
            </a>
            <div id="servicioSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                <a href="{{ route('service.ingresarBike') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">🚲 Ingresar Bicicleta</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('service.egresoBici') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">🔧 Registro Servicio</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Stock -->
        <div class="hoverDiv group relative">
            <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="stockSubMenu">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-yellow-400 group-hover:text-yellow-300 group-hover:scale-110 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Stock</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-amber-400 group-hover:w-full transition-all duration-500"></span>
            </a>
            <div id="stockSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                <a href="{{ route('stock.index') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">📦 Ver Stock</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('stock.pedido') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">📝 Pedido a Proveedor</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('stock.pedidoRealizado') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">✅ Pedidos Realizados</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('stockImprimir') }}" target="_blank" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">🖨️ Imprimir Stock</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="hoverDiv group relative">
            <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="informesSubMenu">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-indigo-400 group-hover:text-indigo-300 group-hover:scale-110 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Estadísticas</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-indigo-400 to-purple-400 group-hover:w-full transition-all duration-500"></span>
            </a>
            <div id="informesSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                <a href="{{ route('informes.masVendidos') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">📊 Más Vendidos</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Operaciones -->
        <div class="hoverDiv group relative">
            <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="operacionSubMenu">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-orange-400 group-hover:text-orange-300 group-hover:scale-110 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Operaciones</span>
                </div>
                <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-orange-400 to-red-400 group-hover:w-full transition-all duration-500"></span>
            </a>
            <div id="operacionSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                <a href="{{ route('operacion.list') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">📋 Operaciones</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('venta.list') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                    <span class="relative z-10 flex items-center">💰 Ventas</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Sección Admin (solo visible para administradores) -->
        <x-admin>
            <div class="relative my-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-800 px-3 text-xs text-gray-400 uppercase tracking-wider">Administración</span>
                </div>
            </div>

            <!-- Gestión -->
            <div class="hoverDiv group relative">
                <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="gestionSubMenu">
                    <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-gray-400 group-hover:text-gray-300 group-hover:scale-110 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Gestión</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-gray-400 to-gray-500 group-hover:w-full transition-all duration-500"></span>
                </a>
                <div id="gestionSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                    <a href="{{ route('articulo.articuloGrupo') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">📦 Artículo</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="{{ route('gestion.precio.precioCambiar') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">💰 Cambio de Precio - Artículos</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="{{ route('gestion.precio.precioGrupo') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">📊 Cambio de Precio - Grupo</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="{{ route('proveedor.proveedor') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">🏭 Proveedor</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>

            <!-- Ofertas -->
            <div class="hoverDiv group relative">
                <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="ofertaSubMenu">
                    <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-purple-400 group-hover:text-purple-300 group-hover:scale-110 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Ofertas</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-400 to-pink-400 group-hover:w-full transition-all duration-500"></span>
                </a>
                <div id="ofertaSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                    <a href="{{ route('oferta.ofertaList') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">🏷️ Ofertas</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="{{ route('oferta.ofertaCreate') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">✨ Crear</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="{{ route('oferta.ofertaGestion') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">⚙️ Operaciones</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>

            <!-- Usuario (Administración) -->
            <div class="hoverDiv group relative">
                <a href="javascript:void(0)" class="toggle-submenu nav-item py-3 px-4 flex items-center justify-between transition-all duration-300 rounded-xl mx-2 relative overflow-hidden" data-menu="userSubMenu">
                    <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-cyan-400 group-hover:text-cyan-300 group-hover:scale-110 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Administración</span>
                    </div>
                    <svg class="chevron-icon h-5 w-5 transition-all duration-300 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-400 to-blue-400 group-hover:w-full transition-all duration-500"></span>
                </a>
                <div id="userSubMenu" class="submenu pl-8 hidden space-y-1.5 mt-1">
                    <a href="{{ route('admin.gestionUsuario') }}" class="submenu-item block py-2.5 px-4 text-base text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 rounded-lg relative overflow-hidden group/sub">
                        <span class="relative z-10 flex items-center">👥 Gestionar Usuarios</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-transparent opacity-0 group-hover/sub:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>
        </x-admin>

        <!-- Cierre Caja -->
        <div class="hoverDiv group relative">
            <a href="{{ route('cierre.cierreCaja') }}" class="nav-item py-3 px-4 flex items-center transition-all duration-300 rounded-xl mx-2 relative overflow-hidden">
                <div class="nav-glow absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 mr-3 text-red-400 group-hover:text-red-300 group-hover:scale-110 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span class="movableDiv transition-all duration-300 text-white text-lg font-medium tracking-wide">Cierre Caja</span>
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-red-400 to-pink-400 group-hover:w-full transition-all duration-500"></span>
            </a>
        </div>
    </nav>
</div>

<script>
    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
    }

    function openNav() {
        document.getElementById("mySidebar").style.width = "320px";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.toggle-submenu');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menuId = this.getAttribute('data-menu');
                const currentSubmenu = document.getElementById(menuId);
                const currentChevron = this.querySelector('.chevron-icon');
                
                document.querySelectorAll('.submenu').forEach(submenu => {
                    if (submenu.id !== menuId && !submenu.classList.contains('hidden')) {
                        submenu.classList.add('hidden');
                        const otherToggle = document.querySelector(`.toggle-submenu[data-menu="${submenu.id}"]`);
                        if (otherToggle) {
                            const otherChevron = otherToggle.querySelector('.chevron-icon');
                            if (otherChevron) otherChevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });
                
                if (currentSubmenu.classList.contains('hidden')) {
                    currentSubmenu.classList.remove('hidden');
                    if (currentChevron) currentChevron.style.transform = 'rotate(90deg)';
                } else {
                    currentSubmenu.classList.add('hidden');
                    if (currentChevron) currentChevron.style.transform = 'rotate(0deg)';
                }
            });
        });
    });

    document.querySelectorAll('.hoverDiv').forEach(div => {
        const movableDiv = div.querySelector('.movableDiv');
        if (movableDiv) {
            div.addEventListener('mouseenter', () => movableDiv.classList.add('move-left'));
            div.addEventListener('mouseleave', () => movableDiv.classList.remove('move-left'));
        }
    });
</script>

<style>
    .move-left {
        transform: translateX(-8px);
    }
    
    #mySidebar::-webkit-scrollbar {
        width: 4px;
    }
    
    #mySidebar::-webkit-scrollbar-track {
        background: #1f2937;
        border-radius: 4px;
    }
    
    #mySidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #4b5563, #6b7280);
        border-radius: 4px;
    }
    
    #mySidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #6b7280, #9ca3af);
    }
    
    .submenu {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .nav-item {
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .nav-item:hover {
        background: linear-gradient(90deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 100%);
        transform: translateX(4px);
    }
    
    .submenu-item {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .submenu:not(.hidden) .submenu-item {
        animation: slideIn 0.2s ease-out forwards;
    }
    
    .submenu-item:nth-child(1) { animation-delay: 0.05s; }
    .submenu-item:nth-child(2) { animation-delay: 0.1s; }
    .submenu-item:nth-child(3) { animation-delay: 0.15s; }
    .submenu-item:nth-child(4) { animation-delay: 0.2s; }
</style> --}}