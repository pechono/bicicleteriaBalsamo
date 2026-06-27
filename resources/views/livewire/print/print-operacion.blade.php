<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'Reporte de ventas' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; color: #1f2937; font-size: 12px; }
        .wrap { padding: 26px 30px; }

        .top { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .top td { vertical-align: top; }
        .empresa { font-size: 20px; font-weight: bold; color: #1d4ed8; }
        .empresa-sub { color: #6b7280; font-size: 11px; line-height: 1.5; }
        .doc-box { text-align: right; }
        .doc-title { display: inline-block; background: #1d4ed8; color: #fff; font-size: 14px;
                     font-weight: bold; padding: 6px 14px; border-radius: 4px; letter-spacing: .5px; }
        .doc-meta { margin-top: 8px; font-size: 11px; color: #374151; }
        .doc-meta b { color: #111827; }

        .info { width: 100%; border-collapse: collapse; margin: 6px 0 18px; }
        .info td { padding: 10px 12px; background: #f3f4f6; border: 1px solid #e5e7eb; }
        .info .label { font-size: 10px; text-transform: uppercase; color: #6b7280; letter-spacing: .5px; }
        .info .val { font-size: 15px; color: #111827; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead th { background: #1d4ed8; color: #fff; font-size: 11px; text-transform: uppercase;
                               letter-spacing: .4px; padding: 9px 10px; text-align: left; }
        table.items tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.items tbody tr:nth-child(even) td { background: #f9fafb; }
        .center { text-align: center; } .right { text-align: right; }

        .tot { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .tot .total td { font-size: 15px; font-weight: bold; color: #1d4ed8;
                         border-top: 2px solid #1d4ed8; background: #eef2ff; padding: 8px 10px; }
        .tot .k { text-align: right; } .tot .v { text-align: right; width: 140px; }

        footer { margin-top: 26px; padding-top: 10px; border-top: 1px solid #e5e7eb;
                 text-align: center; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="top">
        <tr>
            <td>
                <div class="empresa">{{ $empresa->empresa ?? 'Bicicletería Bálsamo' }}</div>
                <div class="empresa-sub">
                    {{ $empresa->direccion ?? '' }}<br>
                    @if($empresa?->telefono) Tel: {{ $empresa->telefono }} @endif
                    @if($empresa?->mail) · {{ $empresa->mail }} @endif
                </div>
            </td>
            <td class="doc-box">
                <span class="doc-title">{{ $titulo ?? 'REPORTE DE VENTAS' }}</span>
                <div class="doc-meta">
                    {{ $subtitulo ?? '' }}<br>
                    <b>Generado:</b> {{ optional($fechaGeneracion)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr>
            <td style="width:50%;">
                <div class="label">Cantidad de operaciones</div>
                <div class="val">{{ $cantidadOperaciones ?? $operaciones->count() }}</div>
            </td>
            <td>
                <div class="label">Total</div>
                <div class="val">${{ number_format($totalVentas ?? 0, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    @if($operaciones->count() > 0)
        <table class="items">
            <thead>
                <tr>
                    <th style="width:60px;">N°</th>
                    <th style="width:130px;">Fecha</th>
                    <th>Tipo de venta</th>
                    <th style="width:120px;" class="right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operaciones as $op)
                    <tr>
                        <td>{{ $op->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($op->fecha)->format('d/m/Y H:i') }}</td>
                        <td>{{ $op->tipoVenta }}</td>
                        <td class="right">${{ number_format($op->monto, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="tot">
            <tr class="total">
                <td class="k">TOTAL GENERAL</td>
                <td class="v">${{ number_format($totalVentas ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>
    @else
        <p class="center" style="padding:40px; color:#9ca3af;">No se encontraron operaciones para el período seleccionado.</p>
    @endif

    <footer>
        {{ $empresa->empresa ?? 'Bicicletería Bálsamo' }} · Reporte generado el {{ optional($fechaGeneracion)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
    </footer>

</div>
</body>
</html>
