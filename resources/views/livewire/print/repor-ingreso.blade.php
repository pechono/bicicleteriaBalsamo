<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nota Ingreso Rodado - Comprobante A5</title>

<style>
@page {
    size: A5 portrait;
    margin: 5mm 20mm 5mm 5mm;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Courier New', monospace;
    font-size: 14px; /* 🔥 Tamaño general */
    line-height: 1.4;
    color: #000;
    width: 128mm;
    height: 210mm;
    margin: 0 auto;
    padding: 5mm 0 5mm 5mm;
}

.titulo {
    font-size: 14px;
    font-weight: bold;
    border-bottom: 2px dotted #000;
    padding-bottom: 3px;
    margin-bottom: 8px;
    text-align: center;
}

.fila-punteada {
    border-bottom: 1px dotted #999;
    padding: 4px 0;
    margin: 3px 0;
    display: flex;
    justify-content: space-between;
}

.relleno {
    flex-grow: 1;
    margin-left: 5px;
}

.linea-punteada {
    border-bottom: 2px dotted #666;
    margin: 5px 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 5px 0;
}

th, td {
    border: 1px dotted #333;
    padding: 3px 4px;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin: 5px 0;
}

.borde-puntos {
    border: 1px dotted #333;
    padding: 5px;
}

.lista-puntos {
    list-style: none;
}

.lista-puntos li {
    border-bottom: 1px dotted #999;
    padding: 2px 0;
}

.linea-corte {
    text-align: center;
    margin: 6px 0;
    border-top: 1px dashed #333;
    border-bottom: 1px dashed #333;
    padding: 3px 0;
}

.footer {
    position: fixed;
    bottom: 0;
    width: 128mm;
    border-top: 2px solid #000;
    padding-top: 5px;
}

.recuadro-punteado {
    border: 1px dotted #333;
    padding: 5px;
    margin-top: 5px;
}

.condicion-item {
    border-bottom: 1px dotted #999;
    padding: 3px 0;
    font-size: 9px;
}
.datos-cliente {
    display: flex;
    justify-content: space-between;
    gap: 10px; /* separación entre columnas */
}

.datos-cliente > div {
    width: 48%;
}
</style>
</head>

<body>

<div class="titulo">· INGRESO RODADO ·</div>

<div class="fila-punteada">
    <div class="relleno"><h2>{{ $emp->empresa }}</h2></div>
    Datos bicicleta:
    <div class="relleno">
        {{ $bicicleta->marca }} - {{ $bicicleta->tipo }} - {{ $bicicleta->color }}
    </div>
</div>

<div class="linea-punteada"></div>

<table>
<tr>
    <th>Nro</th>
    <th>Fecha ingreso</th>
    <th>Fecha estimada</th>
    <th>Cliente</th>
</tr>
<tr>
    <td>{{ $bicicleta->nro_ingreso }}</td>
    <td>{{ $bicicleta->fecha_ingreso }}</td>
    <td>{{ $bicicleta->fecha_retiro }}</td>
    <td>
        Ap y Nom: {{ $bicicleta->apellido . ' ' . $bicicleta->nombre }}<br>
        Dni: {{ $bicicleta->dni }}<br>
        Telefono: {{ $bicicleta->telefono }}
    </td>
</tr>
</table>

<div class="grid-2">
<div class="borde-puntos">
<ul class="lista-puntos">
@forelse ($procesos as $item)
    <li>{{ $item->articulo . ' ' . $item->presentacion }}</li>
@empty
    <li>No hay procesos asignados</li>
@endforelse
</ul>
</div>

<div class="borde-puntos">
Nota: {{ $bicicleta->detalles }}
</div>
</div>

<div class="linea-corte">✂️ ······ CORTE ······ ✂️</div>

<div class="footer">

<div class="titulo">· NOTA INGRESO, Nro {{ $bicicleta->nro_ingreso }} ·</div>
    {{-- <div class="datos-cliente"> 
        <div>
            <div class="fila-punteada">
                Empresa:
                <div class="relleno">{{ $emp->nombre }}</div>
            </div>

            <div class="fila-punteada">
                Teléfono:
                <div class="relleno">3826-541085</div>
            </div>
        </div>
        <div class="fila-punteada">
                Cliente:
            <div class="relleno">
                Ap y Nom: {{ $bicicleta->apellido . ' ' . $bicicleta->nombre }}<br>
                Dni: {{ $bicicleta->dni }}<br>
                Telefono: {{ $bicicleta->telefono }}
            </div>
        </div>
    </div> --}}
<div>
    <table>
        <tr>
            <th>Empresa</th>
            <th>Cliente</th>
        </tr>
        <tr>
            <td>
                {{ $emp->empresa }}<br>
                Tel: {{ $emp->telefono }}
            </td>
            <td>
                Ap y Nom: {{ $bicicleta->apellido . ' ' . $bicicleta->nombre }}<br>
                Dni: {{ $bicicleta->dni }}<br>
                Telefono: {{ $bicicleta->telefono }}
            </td>
    </table>
</div>
    
<div class="linea-punteada"></div>

<div class="recuadro-punteado">
    <div>CONDICIONES</div>
    <div class="condicion-item">
    PLAZO: 7 DÍAS HÁBILES ({{ $bicicleta->fecha_retiro }}). NO RESPONSABLES POR ROBO, HURTO O DAÑOS.
   VERIFICAR ESTADO AL RETIRAR - NO RECLAMOS
    </div>
</div>

</div>

</body>
</html>