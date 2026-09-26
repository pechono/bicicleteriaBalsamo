<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .head { border-bottom: 2px solid #16a34a; padding-bottom: 8px; margin-bottom: 10px; }
        .head .emp { font-size: 18px; font-weight: bold; color: #15803d; }
        .head .sub { font-size: 11px; color: #4b5563; }
        .meta { float: right; text-align: right; font-size: 11px; color: #374151; }
        .prov { font-size: 12px; margin: 6px 0 12px; }
        .prov b { color: #111827; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 7px; text-align: left; }
        th { background: #f3f4f6; }
        .r { text-align: right; } .c { text-align: center; }
        .tot td { font-weight: bold; background: #f9fafb; }
        .tag { display: inline-block; background: #dcfce7; color: #166534; font-size: 10px; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="head">
        <div class="meta">
            <div><b>Pedido N°</b> {{ str_pad($o->numero, 4, '0', STR_PAD_LEFT) }}</div>
            <div>{{ $o->created_at ? $o->created_at->format('d/m/Y H:i') : '' }}</div>
            <div class="tag">{{ $tipo === 'proveedor' ? 'Para el proveedor' : 'Interno' }}</div>
        </div>
        <div class="emp">{{ $emp->nombre ?? 'BICICLETERÍA BÁLSAMO' }}</div>
        <div class="sub">Pedido a proveedor</div>
    </div>

    <div class="prov">
        <b>Proveedor:</b> {{ $o->proveedor }}
        @if($o->telefono) &middot; Tel: {{ $o->telefono }}@endif
        @if($o->direccion) &middot; {{ $o->direccion }}@endif
        @if($o->localidad) ({{ $o->localidad }})@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Artículo</th>
                <th class="c">Cant.</th>
                @if($tipo === 'interno')
                    <th class="r">Costo</th>
                    <th class="r">Subtotal</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($items as $it)
                @php $sub = (int) $it->cantidad * (int) $it->precio_costo; $total += $sub; @endphp
                <tr>
                    <td>{{ $it->codigo }}</td>
                    <td>{{ $it->articulo }}</td>
                    <td class="c">{{ $it->cantidad }}</td>
                    @if($tipo === 'interno')
                        <td class="r">${{ number_format((int) $it->precio_costo, 0, ',', '.') }}</td>
                        <td class="r">${{ number_format($sub, 0, ',', '.') }}</td>
                    @endif
                </tr>
            @endforeach
            @if($tipo === 'interno')
                <tr class="tot">
                    <td colspan="4" class="r">Total estimado</td>
                    <td class="r">${{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
