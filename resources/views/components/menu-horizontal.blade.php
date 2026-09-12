{{-- Menú horizontal en UNA línea (se embebe junto al logo). Los submenús FLOTAN
     por encima del contenido (no empujan nada). El menú lateral viejo queda intacto. --}}
@php
    $grupos = [
        ['id' => 'venta', 'icon' => '🛒', 'label' => 'Venta', 'items' => [
            ['route' => 'venta.ventaExpress',        'label' => '🚀 Venta Express'],
            ['route' => 'venta.ventaCard',           'label' => '💳 Venta Card'],
            ['route' => 'venta.cuentaCorriente',     'label' => '📊 Cuenta Corriente'],
            ['route' => 'venta.listCuentaCorriente', 'label' => '💰 Pago Cuenta Corriente'],
        ]],
        ['id' => 'servicio', 'icon' => '🔧', 'label' => 'Servicio', 'items' => [
            ['route' => 'service.ingresarBike',        'label' => '🚲 Ingresar Bicicleta'],
            ['route' => 'service.egresoBici',          'label' => '🔧 Registro Servicio'],
            ['route' => 'service.calendarioServicios', 'label' => '📅 Calendario'],
            ['route' => 'service.cuentaMecanico',      'label' => '💰 Cuenta Mecánico'],
        ]],
        ['id' => 'stock', 'icon' => '📦', 'label' => 'Stock', 'items' => [
            ['route' => 'stock.index',            'label' => '📦 Ver Stock'],
            ['route' => 'stock.pedido',           'label' => '📝 Pedido a Proveedor'],
            ['route' => 'stock.pedidoCatalogo',   'label' => '🗂️ Pedido desde Catálogo'],
            ['route' => 'stock.pedidoRealizado',  'label' => '✅ Pedidos Realizados'],
            ['route' => 'stockImprimir',          'label' => '🖨️ Imprimir Stock', 'blank' => true],
            ['route' => 'stock.actualizarFactura','label' => '🧾 Actualizar desde factura'],
        ]],
        ['id' => 'informes', 'icon' => '📈', 'label' => 'Estadísticas', 'items' => [
            ['route' => 'informes.masVendidos', 'label' => '📊 Más Vendidos'],
        ]],
        ['id' => 'operacion', 'icon' => '📋', 'label' => 'Operaciones', 'items' => [
            ['route' => 'operacion.list', 'label' => '📋 Operaciones'],
            ['route' => 'venta.list',     'label' => '💰 Ventas'],
        ]],
        ['id' => 'mayorista', 'icon' => '🏬', 'label' => 'Mayorista', 'items' => [
            ['route' => 'mayorista.index',          'label' => '🛒 Venta Mayorista'],
            ['route' => 'mayorista.clientes',       'label' => '👤 Clientes Mayorista'],
            ['route' => 'mayorista.cuentaCorriente','label' => '📋 Cuenta Corriente'],
        ]],
    ];
    $gruposAdmin = [
        ['id' => 'gestion', 'icon' => '⚙️', 'label' => 'Gestión', 'items' => [
            ['route' => 'articulo.articuloGrupo',        'label' => '📦 Artículo'],
            ['route' => 'articulo.importarLista',        'label' => '📥 Importar Lista Precios'],
            ['route' => 'articulo.catalogo',             'label' => '🗂️ Catálogo de listas'],
            ['route' => 'articulo.categorias',           'label' => '📂 Categorías'],
            ['route' => 'articulo.manoDeObra',           'label' => '🔧 Mano de Obra'],
            ['route' => 'gestion.precio.precioCambiar',  'label' => '💰 Cambio Precio Artículos'],
            ['route' => 'gestion.precio.precioGrupo',    'label' => '📊 Cambio Precio Grupo'],
            ['route' => 'proveedor.proveedor',           'label' => '🏭 Proveedor'],
            ['route' => 'admin.gestionUsuario',          'label' => '👥 Gestionar Usuarios'],
        ]],
        ['id' => 'oferta', 'icon' => '🏷️', 'label' => 'Ofertas', 'items' => [
            ['route' => 'oferta.ofertaList',    'label' => '🏷️ Ofertas'],
            ['route' => 'oferta.ofertaCreate',  'label' => '✨ Crear'],
            ['route' => 'oferta.ofertaGestion', 'label' => '⚙️ Operaciones'],
        ]],
    ];
    $activoDe = fn($g) => collect($g['items'])->contains(fn($i) => request()->routeIs($i['route']));
@endphp

<nav x-data="{ open: null }" x-on:click.window="open = null" x-on:keydown.escape="open = null" class="contents">

    @foreach($grupos as $g)
        <div class="relative">
            <button type="button" x-on:click.stop="open = open === '{{ $g['id'] }}' ? null : '{{ $g['id'] }}'"
                    :class="open === '{{ $g['id'] }}' && 'menuh-open'"
                    class="menuh-link {{ $activoDe($g) ? 'menuh-active' : '' }}">
                <span>{{ $g['icon'] }}</span><span>{{ $g['label'] }}</span>
                <svg class="w-3 h-3 opacity-70" :class="open === '{{ $g['id'] }}' && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open === '{{ $g['id'] }}'" x-cloak x-transition x-on:click.stop class="menuh-drop">
                @foreach($g['items'] as $it)
                    <a href="{{ route($it['route']) }}" @isset($it['blank']) target="_blank" @endisset
                       class="menuh-item {{ request()->routeIs($it['route']) ? 'menuh-item-active' : '' }}">{{ $it['label'] }}</a>
                @endforeach
            </div>
        </div>
    @endforeach

    <x-admin>
        <span class="hidden sm:inline w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>
        @foreach($gruposAdmin as $g)
            <div class="relative">
                <button type="button" x-on:click.stop="open = open === '{{ $g['id'] }}' ? null : '{{ $g['id'] }}'"
                        :class="open === '{{ $g['id'] }}' && 'menuh-open'"
                        class="menuh-link {{ $activoDe($g) ? 'menuh-active' : '' }}">
                    <span>{{ $g['icon'] }}</span><span>{{ $g['label'] }}</span>
                    <svg class="w-3 h-3 opacity-70" :class="open === '{{ $g['id'] }}' && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === '{{ $g['id'] }}'" x-cloak x-transition x-on:click.stop class="menuh-drop">
                    @foreach($g['items'] as $it)
                        <a href="{{ route($it['route']) }}" class="menuh-item {{ request()->routeIs($it['route']) ? 'menuh-item-active' : '' }}">{{ $it['label'] }}</a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </x-admin>

    {{-- Cierre de Caja (link directo) --}}
    <a href="{{ route('cierre.cierreCaja') }}" class="menuh-link {{ request()->routeIs('cierre.cierreCaja') ? 'menuh-active' : '' }}">
        🧾 <span>Cierre</span>
    </a>
</nav>

<style>
    [x-cloak] { display: none !important; }
    .menuh-link {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .35rem .6rem; border-radius: .5rem;
        font-size: .8rem; font-weight: 600; color: #374151;
        white-space: nowrap; cursor: pointer; transition: background .15s, color .15s;
    }
    .menuh-link:hover { background: #e5e7eb; }
    .menuh-active, .menuh-open { background: #16a34a !important; color: #fff !important; }
    .menuh-drop {
        position: absolute; left: 0; top: 100%; margin-top: .35rem;
        min-width: 230px; max-height: 75vh; overflow-y: auto;
        background: #fff; border: 1px solid #e5e7eb; border-radius: .7rem;
        box-shadow: 0 12px 30px rgba(0,0,0,.15); padding: .35rem; z-index: 60;
    }
    .menuh-item {
        display: flex; align-items: center; gap: .4rem;
        padding: .5rem .6rem; border-radius: .45rem;
        font-size: .82rem; color: #374151; white-space: nowrap;
    }
    .menuh-item:hover { background: #f0fdf4; color: #065f46; }
    .menuh-item-active { background: #dcfce7; color: #065f46; font-weight: 600; }
    .rotate-180 { transform: rotate(180deg); }
    .dark .menuh-link { color: #d1d5db; }
    .dark .menuh-link:hover { background: #374151; }
    .dark .menuh-drop { background: #1f2937; border-color: #374151; }
    .dark .menuh-item { color: #d1d5db; }
    .dark .menuh-item:hover { background: #064e3b; color: #d1fae5; }
    .dark .menuh-item-active { background: #065f46; color: #d1fae5; }
</style>
