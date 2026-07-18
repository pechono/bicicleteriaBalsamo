<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Portal — Bicicletería Bálsamo</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">

    {{-- Encabezado --}}
    <header class="bg-gradient-to-br from-green-600 to-green-700 text-white">
        <div class="max-w-5xl mx-auto px-4 py-5 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-2xl">🚲</div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Bicicletería Bálsamo</h1>
                <p class="text-green-100 text-sm">Hola, {{ $cliente->nombre }} {{ $cliente->apellido }} 👋</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6 space-y-6">

        {{-- Cuenta corriente (solo si está habilitada) --}}
        @if ($cliente->cuenta_corriente_habilitada)
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h2 class="font-bold text-gray-800">💳 Tu cuenta corriente</h2>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 uppercase">Saldo</div>
                        <div class="text-xl font-bold {{ $saldo > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            ${{ number_format($saldo, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-gray-400">{{ $saldo > 0 ? 'Debés este importe' : 'Sin deuda' }}</div>
                    </div>
                </div>
                @if ($movimientos->isEmpty())
                    <p class="px-5 py-6 text-center text-gray-400 text-sm">Todavía no hay movimientos.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-2 text-left">Fecha</th>
                                    <th class="px-5 py-2 text-left">Detalle</th>
                                    <th class="px-5 py-2 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($movimientos as $m)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-500 whitespace-nowrap">{{ $m->created_at->format('d/m/Y') }}</td>
                                        <td class="px-5 py-2">
                                            @if ($m->tipo === 'venta')
                                                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700">Compra</span>
                                            @else
                                                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Pago</span>
                                            @endif
                                            @if ($m->observaciones)<span class="text-gray-500 ml-2">{{ $m->observaciones }}</span>@endif
                                        </td>
                                        <td class="px-5 py-2 text-right font-semibold {{ $m->tipo === 'venta' ? 'text-red-600' : 'text-emerald-600' }}">
                                            {{ $m->tipo === 'venta' ? '+' : '−' }}${{ number_format($m->monto, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif

        {{-- Stock disponible (sin precios) --}}
        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 mb-3">📦 Productos disponibles</h2>
                <form method="GET" class="flex gap-2">
                    <input type="search" name="q" value="{{ $q }}" placeholder="Buscar producto o código…"
                        class="flex-1 rounded-xl border-gray-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                    <button class="px-4 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold">Buscar</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-2 text-left">Producto</th>
                            <th class="px-5 py-2 text-left">Categoría</th>
                            <th class="px-5 py-2 text-right">Disponible</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($articulos as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-2">
                                    <div class="text-gray-800">{{ $a->articulo }}</div>
                                    @if ($a->codigo)<div class="text-xs text-gray-400 font-mono">{{ $a->codigo }}</div>@endif
                                </td>
                                <td class="px-5 py-2 text-gray-500">{{ $a->categoria }}</td>
                                <td class="px-5 py-2 text-right">
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">{{ $a->stock }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">
                                @if ($q) No hay productos que coincidan con «{{ $q }}». @else No hay productos disponibles. @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($articulos->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">{{ $articulos->links() }}</div>
            @endif
        </section>

        <p class="text-center text-xs text-gray-400 pb-4">Precios y disponibilidad sujetos a confirmación. Consultanos por WhatsApp.</p>
    </main>

</body>
</html>
