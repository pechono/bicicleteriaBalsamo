<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .invoice-box {
            width: 100%;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 24px;
            color: #555;
        }
        
        header {
            text-align: center;
            margin-bottom: 20px;
            background: #007bff;
            color: white;
            padding: 15px;
        }
        
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        header h3 {
            margin: 10px 0 0;
            font-size: 16px;
        }
        
        .company-info {
            margin-top: 10px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-details {
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 12px;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f4f4f4;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .info-box {
            flex: 1;
        }
        
        .info-box p {
            margin: 5px 0;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .invoice-table thead {
            background: #007bff;
            color: white;
        }
        
        .invoice-table th, .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .invoice-table th {
            font-weight: bold;
        }
        
        .invoice-table tfoot td {
            font-weight: bold;
            background: #f4f4f4;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <header>
            <h1>{{ $titulo }}</h1>
            <div class="company-info">
                <div class="company-name">{{ $empresa->empresa ?? 'Sistema de Ventas' }}</div>
                <div class="company-details">
                    <div>Dirección: {{ $empresa->direccion ?? '' }}</div>
                    <div>Teléfono: {{ $empresa->telefono ?? '' }}</div>
                    <div>Email: {{ $empresa->mail ?? '' }}</div>
                </div>
            </div>
        </header>
        
        <div class="report-info">
            <div class="info-box">
                <p><strong>Fecha de emisión:</strong> {{ $fechaEmision->format('d/m/Y H:i:s') }}</p>
                <p><strong>Cantidad de operaciones:</strong> {{ $cantidadOperaciones }}</p>
                <p><strong>Total general:</strong> $ {{ number_format($totalGeneral, 2) }}</p>
            </div>
            <div class="info-box">
                @if(isset($datos))
                    <p><strong>Criterio aplicado:</strong></p>
                    @if($datos['opcion'] == 1)
                        <p>Día: {{ \Carbon\Carbon::parse($datos['dia'])->format('d/m/Y') }}</p>
                    @elseif($datos['opcion'] == 2)
                        <p>Desde: {{ \Carbon\Carbon::parse($datos['fechaI'])->format('d/m/Y') }}</p>
                        <p>Hasta: {{ \Carbon\Carbon::parse($datos['fechaF'])->format('d/m/Y') }}</p>
                    @elseif($datos['opcion'] == 3)
                        <p>Mes: {{ $datos['mes'] }}</p>
                        <p>Año: {{ $datos['anio'] ?? date('Y') }}</p>
                    @elseif($datos['opcion'] == 4)
                        <p>Año: {{ $datos['anio'] }}</p>
                    @endif
                @endif
            </div>
        </div>
        
        @if($operaciones->count() > 0)
        <table class="invoice-table">
            <thead>
                 <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Tipo Venta</th>
                    <th class="text-right">Total</th>
                  </tr>
            </thead>
            <tbody>
                @foreach($operaciones as $op)
                 <tr>
                    <td>{{ $op->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($op->Fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $op->apellido }}, {{ $op->nombre }}</td>
                    <td>{{ $op->name }}</td>
                    <td>{{ $op->tipoVenta }}</td>
                    <td class="text-right">$ {{ number_format($op->venta, 2) }}</td>
                 </tr>
                @endforeach
            </tbody>
            <tfoot>
                 <tr>
                    <td colspan="5" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                    <td class="text-right"><strong>$ {{ number_format($totalGeneral, 2) }}</strong></td>
                 </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align: center; color: red; padding: 40px;">
            No se encontraron operaciones para el criterio seleccionado.
        </p>
        @endif
        
        <footer>
            <p>&copy; {{ date('Y') }} {{ $empresa->empresa ?? 'Sistema de Ventas' }}. Todos los derechos reservados.</p>
            <p>Documento generado electrónicamente - {{ $fechaEmision->format('d/m/Y H:i:s') }}</p>
        </footer>
    </div>
</body>
</html>