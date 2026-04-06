<?php

namespace App\Livewire\Print;

use App\Models\Empresa;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Operacion;
use Illuminate\Support\Facades\DB;

class PrintOperacion extends Component
{
    public $datos;
    public $operaciones;
    public $totalVentas = 0;
    public $cantidadOperaciones = 0;
    public $titulo;
    public $subtitulo;
    
    public function mount($datos = null)
    {
        if ($datos) {
            $this->datos = json_decode(urldecode($datos), true);
            $this->cargarDatos();
        }
    }
    
    public function cargarDatos()
    {
        $opcion = $this->datos['opcion'] ?? null;
        $valor1 = $this->datos['valor1'] ?? null;
        $valor2 = $this->datos['valor2'] ?? null;
        
        // Consulta principal - solo operaciones con tipo de venta
        $query = Operacion::join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->select(
                'operacions.id',
                'operacions.venta as monto',
                'operacions.created_at as fecha',
                'tipo_ventas.tipoVenta'
            )
            ->orderBy('operacions.created_at', 'desc');
        
        // Aplicar filtros según la opción
        switch ($opcion) {
            case 'dia':
                if ($valor1) {
                    $query->whereDate('operacions.created_at', $valor1);
                    $this->titulo = 'REPORTE DE VENTAS POR DÍA';
                    $this->subtitulo = 'Fecha: ' . Carbon::parse($valor1)->format('d/m/Y');
                }
                break;
                
            case 'rango':
                if ($valor1 && $valor2) {
                    $query->whereBetween('operacions.created_at', [$valor1, $valor2]);
                    $this->titulo = 'REPORTE DE VENTAS POR RANGO DE FECHAS';
                    $this->subtitulo = 'Desde: ' . Carbon::parse($valor1)->format('d/m/Y') . ' Hasta: ' . Carbon::parse($valor2)->format('d/m/Y');
                }
                break;
                
            case 'mes':
                if ($valor1) {
                    $query->whereMonth('operacions.created_at', $valor1);
                    if ($valor2) {
                        $query->whereYear('operacions.created_at', $valor2);
                        $this->subtitulo = 'Mes: ' . $this->getNombreMes($valor1) . ' - Año: ' . $valor2;
                    } else {
                        $this->subtitulo = 'Mes: ' . $this->getNombreMes($valor1);
                    }
                    $this->titulo = 'REPORTE DE VENTAS POR MES';
                }
                break;
                
            case 'año':
                if ($valor1) {
                    $query->whereYear('operacions.created_at', $valor1);
                    $this->titulo = 'REPORTE DE VENTAS POR AÑO';
                    $this->subtitulo = 'Año: ' . $valor1;
                }
                break;
                
            default:
                $this->titulo = 'REPORTE GENERAL DE VENTAS';
                $this->subtitulo = 'Todas las ventas registradas';
                break;
        }
        
        // Ejecutar la consulta
        $this->operaciones = $query->get();
        
        // Calcular totales
        $this->totalVentas = $this->operaciones->sum('monto');
        $this->cantidadOperaciones = $this->operaciones->count();
    }
    
    private function getNombreMes($numero)
    {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        return $meses[$numero] ?? '';
    }
    
    public function generateReport()
    {
        $empresa = Empresa::first();
        $fechaGeneracion = Carbon::now();
        
        // Si no hay datos, cargarlos
        if (empty($this->operaciones)) {
            $this->cargarDatos();
        }
        
        $data = [
            'empresa' => $empresa,
            'titulo' => $this->titulo,
            'subtitulo' => $this->subtitulo,
            'operaciones' => $this->operaciones,
            'totalVentas' => $this->totalVentas,
            'cantidadOperaciones' => $this->cantidadOperaciones,
            'fechaGeneracion' => $fechaGeneracion,
        ];
        
        $pdf = Pdf::loadView('livewire.print.print-operacion', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream("reporte-ventas-{$fechaGeneracion->format('Ymd_His')}.pdf");
    }
    
    public function render()
    {
        return view('livewire.print.print-operacion');
    }
}