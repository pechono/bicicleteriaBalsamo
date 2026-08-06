{{-- Acceso rápido: atajos a lo más usado, sin abrir el menú lateral. Discreto y horizontal. --}}
@php
    $accesos = [
        ['route' => 'venta.ventaExpress',   'icon' => '🚀', 'label' => 'Venta Express'],
        ['route' => 'service.ingresarBike', 'icon' => '🚲', 'label' => 'Recibir Bici'],
        ['route' => 'service.egresoBici',   'icon' => '🔧', 'label' => 'Registro Servicio'],
        ['route' => 'venta.ventaCard',      'icon' => '💳', 'label' => 'Venta Card'],
        ['route' => 'cierre.cierreCaja',    'icon' => '🧾', 'label' => 'Cierre Caja'],
    ];
@endphp
<div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/60 px-3 sm:px-4 py-1.5">
    <div class="flex items-center gap-1.5 overflow-x-auto">
        <span class="hidden sm:inline text-[11px] uppercase tracking-wide text-gray-400 font-semibold mr-1 shrink-0">Rápido</span>

        @foreach($accesos as $a)
            <a href="{{ route($a['route']) }}"
               class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition
                      {{ request()->routeIs($a['route'])
                         ? 'bg-brand-600 text-white shadow-sm'
                         : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 border border-gray-200 dark:border-gray-600 hover:bg-brand-50 hover:text-brand-700 dark:hover:bg-gray-600' }}">
                <span>{{ $a['icon'] }}</span>
                <span class="whitespace-nowrap">{{ $a['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
