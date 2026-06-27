<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de stock</title>
    <style>
        @page { margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; color: #1f2937; font-size: 10px; }

        .top { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .top td { vertical-align: top; }
        .empresa { font-size: 18px; font-weight: bold; color: #1d4ed8; }
        .empresa-sub { color: #6b7280; font-size: 10px; line-height: 1.5; }
        .doc-box { text-align: right; }
        .doc-title { display: inline-block; background: #1d4ed8; color: #fff; font-size: 13px;
                     font-weight: bold; padding: 6px 14px; border-radius: 4px; letter-spacing: .5px; }
        .doc-meta { margin-top: 8px; font-size: 10px; color: #374151; }
        .doc-meta b { color: #111827; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead th { background: #1d4ed8; color: #fff; font-size: 10px; text-transform: uppercase;
                               letter-spacing: .3px; padding: 7px 6px; text-align: left; }
        table.items tbody td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; }
        table.items tbody tr:nth-child(even) td { background: #f9fafb; }
        .center { text-align: center; }
        .code { font-family: 'DejaVu Sans Mono', monospace; color: #1d4ed8; font-weight: bold; }
        .blank { background: #fff !important; }

        footer { margin-top: 18px; padding-top: 8px; border-top: 1px solid #e5e7eb;
                 text-align: center; color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>

    <table class="top">
        <tr>
            <td>
                <div class="empresa">{{ $emp->empresa ?? 'Bicicletería Bálsamo' }}</div>
                <div class="empresa-sub">
                    @if($emp?->direccion) {{ $emp->direccion }} @endif
                    @if($emp?->telefono) · Tel: {{ $emp->telefono }} @endif
                </div>
            </td>
            <td class="doc-box">
                <span class="doc-title">CONTROL DE STOCK</span>
                <div class="doc-meta">
                    <b>Fecha:</b> {{ date('d/m/Y') }}<br>
                    <b>Artículos:</b> {{ $articulos->count() }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:36px;">Id</th>
                <th style="width:90px;">Código</th>
                <th>Artículo</th>
                <th style="width:38px;" class="center">Mín.</th>
                <th style="width:46px;" class="center">Stock</th>
                <th style="width:46px;" class="center">Real</th>
                <th style="width:46px;" class="center">Dif.</th>
                <th style="width:90px;">Obs.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articulos as $articulo)
                <tr>
                    <td>{{ $articulo->id }}</td>
                    <td class="code">{{ $articulo->codigo_proveedor ? $articulo->codigo_proveedor.'-' : '' }}{{ $articulo->codigo }}</td>
                    <td>{{ trim($articulo->articulo.' '.$articulo->presentacion) }}{{ $articulo->unidad ? '-'.$articulo->unidad : '' }} <span style="color:#9ca3af;">{{ $articulo->categoria }}</span></td>
                    <td class="center">{{ $articulo->stockMinimo }}</td>
                    <td class="center">@if($articulo->suelto==1) S-{{ $articulo->stock }} @else {{ $articulo->stock }} @endif</td>
                    <td class="blank"></td>
                    <td class="blank"></td>
                    <td class="blank"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        {{ $emp->empresa ?? 'Bicicletería Bálsamo' }} · Planilla de control de stock — {{ date('d/m/Y H:i') }}
    </footer>

</body>
</html>
