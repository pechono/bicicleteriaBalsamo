<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Orden de Trabajo #{{ $nroIngreso->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            font-size: 15px;
            padding-bottom: 30px;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 20px 16px 16px;
            text-align: center;
        }
        .header h1 { font-size: 20px; font-weight: 700; }
        .header .nro { font-size: 28px; font-weight: 900; letter-spacing: 1px; margin-top: 4px; }
        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-pendiente  { background: #fbbf24; color: #78350f; }
        .badge-terminado  { background: #34d399; color: #064e3b; }
        .badge-entregado  { background: #6b7280; color: white; }

        .card {
            background: white;
            border-radius: 12px;
            margin: 12px 12px 0;
            padding: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .card-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .info-label { color: #6b7280; font-size: 13px; }
        .info-value { font-weight: 600; font-size: 13px; text-align: right; max-width: 60%; }

        .proceso-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .proceso-item:last-child { border-bottom: none; }
        .proceso-dot {
            width: 8px; height: 8px;
            background: #2563eb;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .proceso-nombre { font-weight: 500; }
        .proceso-sub { font-size: 11px; color: #9ca3af; }

        .egreso-item {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .egreso-item:last-child { border-bottom: none; }
        .egreso-top { display: flex; justify-content: space-between; align-items: center; }
        .egreso-nombre { font-weight: 500; }
        .egreso-precio {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
        }
        .egreso-cantidad { font-size: 12px; color: #6b7280; margin-top: 2px; }

        .nota-box {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 13px;
            color: #78350f;
            line-height: 1.5;
        }

        .fecha-retiro {
            text-align: center;
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            border-radius: 8px;
            padding: 10px;
            margin-top: 8px;
        }
        .fecha-retiro .label { font-size: 11px; color: #059669; font-weight: 600; text-transform: uppercase; }
        .fecha-retiro .fecha { font-size: 18px; font-weight: 700; color: #065f46; }

        .empty { text-align: center; color: #9ca3af; font-size: 13px; padding: 16px 0; }

        .app-hint {
            margin: 16px 12px 0;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px;
            font-size: 12px;
            color: #1e40af;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>

{{-- ── Header ────────────────────────────────────────────── --}}
<div class="header">
    <h1>🔧 Orden de Trabajo</h1>
    <div class="nro"># {{ str_pad($nroIngreso->id, 4, '0', STR_PAD_LEFT) }}</div>
    @php
        $badgeClass = match($nroIngreso->estado) {
            'Terminado' => 'badge-terminado',
            'Entregado' => 'badge-entregado',
            default     => 'badge-pendiente',
        };
    @endphp
    <span class="badge {{ $badgeClass }}">{{ $nroIngreso->estado }}</span>
</div>

@php
    $primerIngreso = $nroIngreso->ingresoBicis->first();
    $bici          = $primerIngreso?->bici;
    $cliente       = $bici?->cliente;
@endphp

{{-- ── Cliente y bici ────────────────────────────────────── --}}
@if($bici && $cliente)
<div class="card">
    <div class="card-title">🚲 Bicicleta · Cliente</div>

    <div class="info-row">
        <span class="info-label">Cliente</span>
        <span class="info-value">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Marca / Tipo</span>
        <span class="info-value">{{ $bici->marca?->marca }} · {{ $bici->tipoBike?->tipo }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Color</span>
        <span class="info-value">{{ $bici->color ?? '—' }}</span>
    </div>

    @if($nroIngreso->fecha_retiro)
    <div class="fecha-retiro">
        <div class="label">📅 Fecha estimada de entrega</div>
        <div class="fecha">{{ \Carbon\Carbon::parse($nroIngreso->fecha_retiro)->format('d/m/Y') }}</div>
    </div>
    @endif
</div>
@endif

{{-- ── Nota general ──────────────────────────────────────── --}}
@if($nroIngreso->detalles)
<div class="card">
    <div class="card-title">📋 Nota general</div>
    <div class="nota-box">{{ $nroIngreso->detalles }}</div>
</div>
@endif

{{-- ── Trabajos a realizar ───────────────────────────────── --}}
@php
    $procesos = \App\Models\IngresoBici::where('nro_ingreso', $nroIngreso->id)
        ->with('articulo:id,articulo,presentacion')
        ->get();
@endphp
<div class="card">
    <div class="card-title">🛠️ Trabajos a realizar</div>
    @forelse($procesos as $p)
        <div class="proceso-item">
            <div class="proceso-dot"></div>
            <div>
                <div class="proceso-nombre">{{ $p->articulo?->articulo }}</div>
                @if($p->articulo?->presentacion)
                    <div class="proceso-sub">{{ $p->articulo->presentacion }}</div>
                @endif
                @if($p->detalles)
                    <div class="proceso-sub">{{ $p->detalles }}</div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty">Sin procesos asignados</div>
    @endforelse
</div>

{{-- ── Artículos / repuestos aplicados ─────────────────────── --}}
@php
    $egresos = \App\Models\EgresoBici::whereHas(
            'ingresoBici', fn($q) => $q->where('nro_ingreso', $nroIngreso->id)
        )
        ->with('articulo:id,articulo,presentacion')
        ->get();
@endphp
@if($egresos->count() > 0)
<div class="card">
    <div class="card-title">📦 Repuestos / artículos aplicados</div>
    @foreach($egresos as $eg)
        <div class="egreso-item">
            <div class="egreso-top">
                <span class="egreso-nombre">{{ $eg->articulo?->articulo }}</span>
                <span class="egreso-precio">${{ number_format($eg->precio_final, 2, ',', '.') }}</span>
            </div>
            <div class="egreso-cantidad">
                Cantidad: {{ $eg->cantidad }}
                @if($eg->articulo?->presentacion) · {{ $eg->articulo->presentacion }} @endif
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- ── Hint de la app ───────────────────────────────────── --}}
<div class="app-hint">
    📱 Para agregar artículos o cambiar el estado,<br>
    usá la <strong>app del taller</strong> e iniciá sesión.
</div>

</body>
</html>
