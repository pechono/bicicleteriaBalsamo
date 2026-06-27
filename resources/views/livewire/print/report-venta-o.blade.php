<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante N° {{ $datos->id ?? '' }}</title>
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
        .info .val { font-size: 13px; color: #111827; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead th { background: #1d4ed8; color: #fff; font-size: 11px; text-transform: uppercase;
                               letter-spacing: .4px; padding: 9px 10px; text-align: left; }
        table.items tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.items tbody tr:nth-child(even) td { background: #f9fafb; }
        .center { text-align: center; } .right { text-align: right; }

        .tot { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .tot td { padding: 6px 10px; }
        .tot .k { text-align: right; color: #6b7280; }
        .tot .v { text-align: right; width: 130px; }
        .tot .total td { font-size: 15px; font-weight: bold; color: #1d4ed8;
                         border-top: 2px solid #1d4ed8; background: #eef2ff; }

        footer { margin-top: 26px; padding-top: 10px; border-top: 1px solid #e5e7eb;
                 text-align: center; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="top">
        <tr>
            <td>
                <div class="empresa">{{ $emp->empresa ?? 'Bicicletería Bálsamo' }}</div>
                <div class="empresa-sub">
                    {{ $emp->direccion ?? '' }}<br>
                    @if($emp?->telefono) Tel: {{ $emp->telefono }} @endif
                    @if($emp?->mail) · {{ $emp->mail }} @endif
                </div>
            </td>
            <td class="doc-box">
                <span class="doc-title">COMPROBANTE DE VENTA</span>
                <div class="doc-meta">
                    <b>N°:</b> {{ $datos->id ?? '' }}<br>
                    <b>Fecha:</b> {{ optional($datos)->Fecha ? \Carbon\Carbon::parse($datos->Fecha)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr>
            <td style="width:55%;">
                <div class="label">Cliente</div>
                <div class="val">{{ $datos->apellido ?? '' }}, {{ $datos->nombre ?? '' }}</div>
            </td>
            <td>
                <div class="label">Teléfono</div>
                <div class="val">{{ $datos->telefono ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Vendedor</div>
                <div class="val">{{ $datos->name ?? '-' }}</div>
            </td>
            <td>
                <div class="label">Tipo de venta</div>
                <div class="val">{{ $datos->tipoVenta ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="width:70px;" class="center">Cant.</th>
                <th style="width:90px;" class="right">P. Unit.</th>
                <th style="width:60px;" class="center">Desc.</th>
                <th style="width:100px;" class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventaOp as $op)
                @php $sub = ($op->precioF * $op->cantidad) - ($op->precioF * $op->cantidad * $op->descuento / 100); @endphp
                <tr>
                    <td>{{ $op->articulo }} {{ $op->presentacion !== '-' ? $op->presentacion : '' }} {{ $op->unidad }}</td>
                    <td class="center">{{ $op->cantidad }}</td>
                    <td class="right">${{ number_format($op->precioF, 0, ',', '.') }}</td>
                    <td class="center">{{ $op->descuento }}%</td>
                    <td class="right">${{ number_format($sub, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="tot">
        <tr class="total">
            <td class="k">TOTAL</td>
            <td class="v">${{ number_format($datos->venta ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <footer>
        {{ $emp->empresa ?? 'Bicicletería Bálsamo' }} · Comprobante generado el {{ now()->format('d/m/Y H:i') }}
    </footer>

</div>
</body>
</html>
