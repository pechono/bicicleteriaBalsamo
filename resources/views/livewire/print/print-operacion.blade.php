<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        
        .header h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        /* Título del reporte */
        .report-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .report-title h2 {
            font-size: 16px;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .report-title p {
            font-size: 12px;
            color: #666;
        }
        
        /* Resumen */
        .summary {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-around;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        
        /* Tabla */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #0056b3;
        }
        
        .table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
        }
        
        .table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        /* Totales finales */
        .total {
            background: #d4edda;
            padding: 15px;
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .table th {
                background: #007bff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>REPORTE DE VENTAS</h1>
            <div class="company-name">{{ $empresa->empresa ?? 'Sistema de Ventas' }}</div>
            <div class="company-details">
                @if($empresa)
                    {{ $empresa->direccion ?? '' }} | Tel: {{ $empresa->telefono ?? '' }} | {{ $empresa->mail ?? '' }}
                @endif
            </div>
        </div>
        
        <!-- Título del reporte -->
        <div class="report-title">
            <h2>{{ $titulo }}</h2>
            <p>{{ $subtitulo }}</p>
            <p style="font-size: 10px; margin-top: 5px;">
                Generado: {{ $fechaGeneracion->format('d/m/Y H:i:s') }}
            </p>
        </div>
        
        <!-- Resumen -->
        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Operaciones</div>
                <div class="summary-value">{{ number_format($cantidadOperaciones, 0) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Facturado</div>
                <div class="summary-value">${{ number_format($totalVentas, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Monto Promedio</div>
                <div class="summary-value">
                    ${{ $cantidadOperaciones > 0 ? number_format($totalVentas / $cantidadOperaciones, 2) : '0.00' }}
                </div>
            </div>
        </div>
        
        <!-- Tabla de ventas -->
        <table class="table">
            <thead>
                <tr>
                    <th>N° Operación</th>
                    <th>Fecha</th>
                    <th>Tipo de Venta</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($operaciones as $op)
                <tr>
                    <td class="text-center">{{ $op->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($op->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $op->tipoVenta }}</td>
                    <td class="text-right">${{ number_format($op->monto, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No hay ventas para mostrar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Total general -->
        <div class="total">
            TOTAL GENERAL: ${{ number_format($totalVentas, 2) }}
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $empresa->empresa ?? 'Sistema de Ventas' }} - Documento generado electrónicamente</p>
        </div>
    </div>
</body>
</html>