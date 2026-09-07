<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tu bici está lista — Bicicletería Balsamo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; width: 100%; max-width: 420px; border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,.08); overflow: hidden; }
        .top { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; padding: 22px 20px; text-align: center; }
        .top .logo { font-size: 34px; }
        .top h1 { font-size: 17px; font-weight: 700; margin-top: 4px; letter-spacing: .3px; }
        .body { padding: 22px 20px 26px; text-align: center; }
        .hola { font-size: 15px; color: #374151; }
        .bici { font-size: 15px; color: #374151; margin-top: 6px; }
        .bici b { color: #111827; }
        .lista { display: inline-block; margin-top: 12px; background: #dcfce7; color: #166534; font-size: 13px; font-weight: 600; padding: 5px 12px; border-radius: 999px; }
        .label { margin-top: 22px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; }
        .monto { font-size: 44px; font-weight: 800; color: #16a34a; line-height: 1.1; margin-top: 2px; }
        .nota { margin-top: 16px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 12.5px; line-height: 1.5; border-radius: 12px; padding: 12px 14px; text-align: left; }
        .pie { margin-top: 20px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="top">
            <div class="logo">🚲</div>
            <h1>BICICLETERÍA BALSAMO</h1>
        </div>
        <div class="body">
            <div class="hola">Hola {{ $info->nombre }} 👋</div>
            <div class="bici">Tu bici <b>{{ $info->marca }}</b>@if($info->color) · {{ $info->color }}@endif (N° {{ str_pad($info->nro_ingreso, 4, '0', STR_PAD_LEFT) }})</div>
            <span class="lista">✅ Lista para retirar</span>

            <div class="label">Monto a abonar</div>
            <div class="monto">${{ $monto ? number_format((float) $monto, 0, ',', '.') : '—' }}</div>

            <div class="nota">
                ⚠️ El precio puede variar según la estadía del rodado en el taller.
                La bici puede quedar hasta 7 días sin cargo desde que está lista; pasado ese plazo
                se aplica un recargo por almacenamiento. Tampoco nos responsabilizamos por daños
                del clima, robo o hurto una vez cumplido el plazo.
            </div>

            <div class="pie">¡Te esperamos! 📍</div>
        </div>
    </div>
</body>
</html>
