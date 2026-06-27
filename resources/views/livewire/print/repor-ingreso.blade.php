<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso N° {{ $bicicleta->nro_ingreso }}</title>
<style>
    @page { size: A5 portrait; margin: 8mm; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #1f2937; font-size: 10px; margin: 0; }

    .top { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .top td { vertical-align: top; }
    .empresa { font-size: 15px; font-weight: bold; color: #1d4ed8; }
    .empresa-sub { color: #6b7280; font-size: 9px; }
    .doc-box { text-align: right; }
    .doc-title { display: inline-block; background: #1d4ed8; color: #fff; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 3px; }
    .doc-meta { margin-top: 5px; font-size: 9px; color: #374151; }

    .box { width: 100%; border-collapse: collapse; margin: 4px 0; }
    .box td { border: 1px solid #e5e7eb; padding: 5px 7px; background: #f9fafb; vertical-align: top; }
    .label { font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: .3px; }
    .val { font-size: 11px; color: #111827; }

    .sec { font-size: 11px; font-weight: bold; color: #1d4ed8; margin: 9px 0 2px; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }

    table.items { width: 100%; border-collapse: collapse; }
    table.items td { border-bottom: 1px solid #e5e7eb; padding: 4px 7px; font-size: 10px; }

    .nota { border: 1px solid #e5e7eb; border-radius: 3px; padding: 6px; font-size: 10px; color: #374151; }

    .qr { width: 100%; border-collapse: collapse; margin-top: 8px; border: 1px dashed #9ca3af; border-radius: 3px; }
    .qr td { padding: 6px; vertical-align: middle; }
    .qr img { width: 92px; height: 92px; }
    .qr .t strong { display: block; font-size: 10px; color: #111827; margin-bottom: 2px; }
    .qr .t { font-size: 8px; color: #374151; line-height: 1.4; }

    .cond { border: 1px solid #e5e7eb; border-radius: 3px; padding: 6px; margin-top: 8px; font-size: 8px; color: #6b7280; }
</style>
</head>
<body>

    <table class="top">
        <tr>
            <td>
                <div class="empresa">{{ $emp->empresa ?? 'Bicicletería Bálsamo' }}</div>
                <div class="empresa-sub">
                    @if($emp?->telefono) Tel: {{ $emp->telefono }} @endif
                    @if($emp?->direccion) · {{ $emp->direccion }} @endif
                </div>
            </td>
            <td class="doc-box">
                <span class="doc-title">INGRESO DE RODADO</span>
                <div class="doc-meta">
                    <b>N°:</b> {{ $bicicleta->nro_ingreso }}<br>
                    <b>Ingreso:</b> {{ $bicicleta->fecha_ingreso }}<br>
                    <b>Retiro est.:</b> {{ $bicicleta->fecha_retiro ?: '-' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td style="width:60%;">
                <div class="label">Cliente</div>
                <div class="val">{{ $bicicleta->apellido }} {{ $bicicleta->nombre }}</div>
            </td>
            <td>
                <div class="label">Teléfono</div>
                <div class="val">{{ $bicicleta->telefono ?: '-' }}</div>
            </td>
        </tr>
        @if($bicicleta->dni)
        <tr>
            <td colspan="2"><div class="label">DNI</div><div class="val">{{ $bicicleta->dni }}</div></td>
        </tr>
        @endif
    </table>

    <table class="box">
        <tr>
            <td><div class="label">Marca</div><div class="val">{{ $bicicleta->marca }}</div></td>
            <td><div class="label">Tipo</div><div class="val">{{ $bicicleta->tipo }}</div></td>
            <td><div class="label">Color</div><div class="val">{{ $bicicleta->color }}</div></td>
        </tr>
    </table>

    <div class="sec">Trabajos / procesos</div>
    <table class="items">
        <tbody>
            @forelse($procesos as $item)
                <tr><td>{{ $item->articulo ? trim($item->articulo.' '.$item->presentacion) : ($item->detalles_articulo ?: 'Proceso') }}</td></tr>
            @empty
                <tr><td style="color:#9ca3af;">Sin procesos asignados</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($bicicleta->detalles)
        <div class="sec">Nota</div>
        <div class="nota">{{ $bicicleta->detalles }}</div>
    @endif

    @if(!empty($qrBase64))
        <table class="qr">
            <tr>
                <td style="width:104px;"><img src="{{ $qrBase64 }}"></td>
                <td class="t">
                    <strong>Escaneá con la app del taller</strong>
                    Accedé a esta orden desde el celular: ver los procesos a realizar y cargar los artículos que uses en la reparación.<br>
                    Ingreso N° {{ $bicicleta->nro_ingreso }}
                </td>
            </tr>
        </table>
    @endif

    <div class="cond">
        <b>CONDICIONES:</b> Plazo 7 días hábiles (retiro estimado {{ $bicicleta->fecha_retiro ?: 's/f' }}). No nos responsabilizamos por robo, hurto o daños. Verificar el estado al retirar — no se aceptan reclamos posteriores.
    </div>

</body>
</html>
