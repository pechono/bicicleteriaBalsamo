<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido #{{ str_pad($o->numero, 4, '0', STR_PAD_LEFT) }} — Bicicletería Balsamo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, "Segoe UI", Roboto, sans-serif; }
        body { color: #1f2937; padding: 24px; max-width: 800px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #16a34a; padding-bottom: 12px; margin-bottom: 16px; }
        .top h1 { font-size: 20px; color: #15803d; }
        .top .meta { text-align: right; font-size: 13px; color: #4b5563; }
        .prov { font-size: 14px; margin-bottom: 14px; }
        .prov b { color: #111827; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        thead th { background: #f3f4f6; }
        .r { text-align: right; } .c { text-align: center; }
        tfoot td { font-weight: 700; border-top: 2px solid #d1d5db; }
        .total { color: #15803d; }
        .btn { margin: 16px 0; padding: 8px 16px; background: #16a34a; color: #fff; border: 0; border-radius: 8px; font-size: 14px; cursor: pointer; }
        @media print { .btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="btn" onclick="window.print()">🖨️ Imprimir</button>

    <div class="top">
        <div>
            <h1>🚲 BICICLETERÍA BÁLSAMO</h1>
            <div style="font-size:13px;color:#4b5563">Pedido a proveedor</div>
        </div>
        <div class="meta">
            <div><b>Pedido N°</b> {{ str_pad($o->numero, 4, '0', STR_PAD_LEFT) }}</div>
            <div>{{ $o->created_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="prov">
        <b>Proveedor:</b> {{ $o->proveedor }}
        @if($o->telefono) · Tel: {{ $o->telefono }}@endif
        @if($o->direccion) · {{ $o->direccion }}@endif
        @if($o->localidad) ({{ $o->localidad }})@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Artículo</th>
                <th class="c">Cant.</th>
                <th class="r">Costo</th>
                <th class="r">Subtotal</th>
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
                    <td class="r">${{ number_format((int) $it->precio_costo, 0, ',', '.') }}</td>
                    <td class="r">${{ number_format($sub, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="r">Total estimado</td>
                <td class="r total">${{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
