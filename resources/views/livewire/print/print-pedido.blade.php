<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido N° {{ $ver }}</title>
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

        .prov { width: 100%; border-collapse: collapse; margin: 6px 0 18px; }
        .prov td { padding: 10px 12px; background: #f3f4f6; border: 1px solid #e5e7eb; }
        .prov .label { font-size: 10px; text-transform: uppercase; color: #6b7280; letter-spacing: .5px; }
        .prov .val { font-size: 13px; color: #111827; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead th { background: #1d4ed8; color: #fff; font-size: 11px; text-transform: uppercase;
                               letter-spacing: .4px; padding: 9px 10px; text-align: left; }
        table.items tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.items tbody tr:nth-child(even) td { background: #f9fafb; }
        .cod { font-family: 'DejaVu Sans Mono', monospace; color: #374151; font-size: 11px; }
        .cant { text-align: center; font-weight: bold; }
        .right { text-align: right; }
        .center { text-align: center; }

        .total-row td { padding: 10px; font-weight: bold; background: #eef2ff; border-top: 2px solid #1d4ed8; }

        footer { margin-top: 26px; padding-top: 10px; border-top: 1px solid #e5e7eb;
                 text-align: center; color: #9ca3af; font-size: 10px; }
        .firma { margin-top: 40px; width: 100%; }
        .firma td { width: 50%; text-align: center; padding-top: 28px; font-size: 11px; color: #6b7280; }
        .firma .line { border-top: 1px solid #9ca3af; margin: 0 30px 4px; }
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
                <span class="doc-title">PEDIDO A PROVEEDOR</span>
                <div class="doc-meta">
                    <b>N°:</b> {{ $ver }}<br>
                    <b>Fecha:</b> {{ optional($pedidos->first())->Fecha ? \Carbon\Carbon::parse($pedidos->first()->Fecha)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="prov">
        <tr>
            <td style="width:55%;">
                <div class="label">Proveedor</div>
                <div class="val">{{ $proveedor->nombre ?? '-' }}</div>
            </td>
            <td>
                <div class="label">Teléfono</div>
                <div class="val">{{ $proveedor->telefono ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Dirección</div>
                <div class="val">{{ $proveedor->direccion ?? '-' }}</div>
            </td>
            <td>
                <div class="label">Localidad</div>
                <div class="val">{{ $proveedor->localidad ?? '-' }}</div>
            </td>
        </tr>
    </table>

    @if($pedidos->count() > 0)
        <table class="items">
            <thead>
                <tr>
                    <th style="width:32px;" class="center">#</th>
                    <th style="width:130px;">Código</th>
                    <th>Artículo</th>
                    <th style="width:80px;" class="center">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $i => $p)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="cod">{{ $p->codigo_proveedor }}{{ $p->codigo ? '-'.$p->codigo : '' }}</td>
                        <td>{{ $p->articulo }} {{ $p->presentacion !== '-' ? $p->presentacion : '' }} {{ $p->unidad }}</td>
                        <td class="cant">{{ $p->cantidad }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="right">TOTAL DE ARTÍCULOS</td>
                    <td class="center">{{ $pedidos->count() }}</td>
                </tr>
            </tbody>
        </table>

        <table class="firma">
            <tr>
                <td><div class="line"></div>Solicita</td>
                <td><div class="line"></div>Recibe / Proveedor</td>
            </tr>
        </table>
    @else
        <p class="center" style="padding:40px; color:#9ca3af;">Este pedido no tiene artículos.</p>
    @endif

    <footer>
        {{ $emp->empresa ?? 'Bicicletería Bálsamo' }} · Pedido generado el {{ now()->format('d/m/Y H:i') }}
    </footer>

</div>
</body>
</html>
