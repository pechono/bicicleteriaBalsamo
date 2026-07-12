<?php
// app/Livewire/Informes/MasVendidos.php

namespace App\Livewire\Informes;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\TipoVenta;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasVendidos extends Component
{
    public $articulosMasVendidos;
    public $fechaInicio;
    public $fechaFin;
    public $categoriaId = '';
    public $tipoVentaId = '';
    public $periodo = 'mes';
    public $topCantidad = 10;
    public $metricoTipo = 'ingresos';
    
    // Métricas
    public $totalIngresos = 0;
    public $totalVentas = 0;
    public $totalProductos = 0;
    public $ticketPromedio = 0;
    public $tendencia = 0;
    
    public $categorias = [];
    public $tiposVenta = [];
    
    public function mount()
    {
        $this->categorias = Categoria::all();
        $this->tiposVenta = TipoVenta::all();
        $this->setPeriodo('mes');
        $this->actualizarDatos();
    }
    
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;
        
        switch($periodo) {
            case 'semana':
                $this->fechaInicio = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->fechaFin = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'mes':
                $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->fechaFin = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'anio':
                $this->fechaInicio = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->fechaFin = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'personalizado':
                // No cambiar fechas
                break;
        }
        
        $this->actualizarDatos();
    }
    
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['fechaInicio', 'fechaFin', 'categoriaId', 'tipoVentaId', 'topCantidad', 'metricoTipo', 'periodo'])) {
            $this->actualizarDatos();
        }
    }
    
    public function actualizarDatos()
    {
        $this->calcularMetricas();
        $this->cargarTopProductos();
    }
    
    public function calcularMetricas()
    {
        // Consulta base para ventas
        $query = Venta::query();
        
        // Filtro de fechas
        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween('created_at', [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59']);
        }
        
        // Calcular ingresos totales
        $this->totalIngresos = $query->sum(DB::raw('cantidad * precioF'));
        
        // Total de ventas (operaciones únicas)
        $this->totalVentas = $query->distinct('operacion')->count('operacion');
        
        // Total de productos vendidos (unidades)
        $this->totalProductos = $query->sum('cantidad');
        
        // Ticket promedio
        $this->ticketPromedio = $this->totalVentas > 0 ? $this->totalIngresos / $this->totalVentas : 0;
        
        // Calcular tendencia (vs período anterior)
        $this->calcularTendencia();
    }
    
    public function calcularTendencia()
    {
        if (!$this->fechaInicio || !$this->fechaFin) {
            $this->tendencia = 0;
            return;
        }
        
        $inicio = Carbon::parse($this->fechaInicio);
        $fin = Carbon::parse($this->fechaFin);
        $diasPeriodo = $inicio->diffInDays($fin);
        
        $inicioAnterior = $inicio->copy()->subDays($diasPeriodo + 1);
        $finAnterior = $inicio->copy()->subDay();
        
        $ingresosAnterior = Venta::whereBetween('created_at', [$inicioAnterior, $finAnterior])
            ->sum(DB::raw('cantidad * precioF'));
            
        if ($ingresosAnterior > 0) {
            $this->tendencia = (($this->totalIngresos - $ingresosAnterior) / $ingresosAnterior) * 100;
        } else {
            $this->tendencia = $this->totalIngresos > 0 ? 100 : 0;
        }
    }
    
    public function cargarTopProductos()
    {
        $query = Venta::select(
                'articulos.id',
                'articulos.articulo',
                'categorias.categoria',
                DB::raw('SUM(ventas.cantidad) as total_cantidad'),
                DB::raw('SUM(ventas.cantidad * ventas.precioF) as total_ingresos'),
                DB::raw('SUM(ventas.cantidad * (ventas.precioF - ventas.precioI)) as total_ganancia'),
                DB::raw('AVG(ventas.precioF) as precio_promedio'),
                DB::raw('COUNT(DISTINCT ventas.operacion) as numero_ventas')
            )
            ->join('articulos', 'ventas.articulo_id', '=', 'articulos.id')
            ->join('categorias', 'articulos.categoria_id', '=', 'categorias.id');
        
        // Filtro de fechas
        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween('ventas.created_at', [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59']);
        }
        
        // Filtro de categoría
        if ($this->categoriaId) {
            $query->where('articulos.categoria_id', $this->categoriaId);
        }
        
        // Filtro de tipo de venta
        if ($this->tipoVentaId) {
            $query->join('operacions', 'ventas.operacion', '=', 'operacions.id')
                  ->where('operacions.tipoVenta_id', $this->tipoVentaId);
        }
        
        // Ordenar según métrica seleccionada
        switch($this->metricoTipo) {
            case 'ingresos':
                $query->orderBy('total_ingresos', 'desc');
                break;
            case 'ganancia':
                $query->orderBy('total_ganancia', 'desc');
                break;
            case 'cantidad':
            default:
                $query->orderBy('total_cantidad', 'desc');
                break;
        }
        
        $this->articulosMasVendidos = $query->groupBy('articulos.id', 'articulos.articulo', 'categorias.categoria')
            ->take($this->topCantidad)
            ->get();
    }
    
    public function exportarExcel()
    {
        session()->flash('message', 'Exportando a Excel...');
    }
    
    public function exportarPDF()
    {
        session()->flash('message', 'Generando PDF...');
    }
    
    public function render()
    {
        return view('livewire.informes.mas-vendidos', [
            'articulosMasVendidos' => $this->articulosMasVendidos,
            'categorias' => $this->categorias,
            'tiposVenta' => $this->tiposVenta
        ]);
    }
}