<?php

namespace App\Livewire\Venta;

use App\Models\Operacion;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListVenta extends Component
{
    public $msj = 'Venta Diaria';
    public $activarOps = true;
    public $ac = 'display:none';
    public $acd = 'display:none';
    public $fechaI = '';
    public $fechaF = '';
    public $Dia = '';
    public $mes = '';
    public $meses;
    public $anio;
    public $totalGeneral = 0; // Nuevo: para mostrar total general

    public function render()
    {
        $this->meses = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];

        $operacions = Operacion::join('ventas', 'ventas.operacion', '=', 'operacions.id')
            ->join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->join('users', 'users.id', '=', 'operacions.usuario_id')
            ->join('clientes', 'clientes.id', '=', 'operacions.cliente_id')
            ->join('articulos', 'articulos.id', '=', 'ventas.articulo_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->select(
                'operacions.id',
                'ventas.articulo_id',
                'operacions.venta',
                'clientes.apellido',
                'clientes.nombre',
                'users.name',
                'operacions.created_at AS Fecha',
                'tipo_ventas.tipoVenta',
                'articulos.articulo',
                'ventas.precioF',
                'ventas.cantidad',
                'articulos.presentacion',
                'unidads.unidad',
                'categorias.categoria',
                'ventas.descuento',
                'articulos.unidadVenta',
                'operacions.created_at',
                DB::raw('MONTH(operacions.created_at) AS mes'),
                DB::raw('YEAR(operacions.created_at) AS anio')
            )
            ->when($this->Dia, function ($query) {
                return $query->whereDate('operacions.created_at', $this->Dia);
            })
            ->when($this->fechaI, function ($query) {
                return $query->whereDate('operacions.created_at', '>=', $this->fechaI);
            })
            ->when($this->fechaF, function ($query) {
                return $query->whereDate('operacions.created_at', '<=', $this->fechaF);
            })
            ->when($this->mes, function ($query) {
                return $query->whereMonth('operacions.created_at', $this->mes);
            })
            ->when($this->anio, function ($query) {
                return $query->whereYear('operacions.created_at', $this->anio);
            })
            ->orderBy('operacions.id', 'desc')
            ->get();

        // Calcular total general
        $this->totalGeneral = $operacions->sum('venta');

        $aniosUnicos = Operacion::selectRaw('YEAR(created_at) AS anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('livewire.venta.list-venta', compact('operacions', 'aniosUnicos'));
    }

    public function cancelarDE()
    {
        $this->Dia = '';
        $this->anio = '';
        $this->mes = '';
        
        if ($this->fechaI && $this->fechaF) {
            $this->msj = 'Venta entre los días ' . Carbon::parse($this->fechaI)->format('d/m/Y') . ' - ' . Carbon::parse($this->fechaF)->format('d/m/Y');
        } else {
            $this->msj = 'Seleccione un rango de fechas';
        }
    }

    public function cancelarD()
    {
        $this->fechaF = '';
        $this->fechaI = '';
        $this->anio = '';
        $this->mes = '';
        
        if ($this->Dia) {
            $this->msj = 'Venta en el día ' . Carbon::parse($this->Dia)->format('d/m/Y');
        } else {
            $this->msj = 'Seleccione un día';
        }
    }

    public function cancelarM()
    {
        $this->fechaF = '';
        $this->fechaI = '';
        $this->Dia = '';
        $this->anio = '';
        
        if ($this->mes) {
            $this->msj = 'Ventas en ' . $this->meses[$this->mes];
        } else {
            $this->msj = 'Seleccione un mes';
        }
    }

    public function cancelarA()
    {
        $this->fechaF = '';
        $this->fechaI = '';
        $this->Dia = '';
        $this->mes = '';
        
        if ($this->anio) {
            $this->msj = 'Ventas en el año ' . $this->anio;
        } else {
            $this->msj = 'Seleccione un año';
        }
    }

    // Nueva función: Limpiar todos los filtros
    public function limpiarFiltros()
    {
        $this->fechaI = '';
        $this->fechaF = '';
        $this->Dia = '';
        $this->mes = '';
        $this->anio = '';
        $this->msj = 'Venta Diaria';
        $this->dispatch('filtrosLimpiados'); // Evento para la vista
    }

    // Nueva función: Exportar a PDF (simulada)
    public function exportarPDF()
    {
        if ($this->totalGeneral > 0) {
            session()->flash('mensaje', 'Preparando exportación a PDF...');
            // Aquí iría la lógica de exportación
        } else {
            session()->flash('error', 'No hay datos para exportar');
        }
    }

    // Nueva función: Mostrar resumen rápido
    public function mostrarResumen()
    {
        $this->dispatch('mostrarResumen', [
            'total' => $this->totalGeneral,
            'registros' => $this->getCount(),
            'filtro' => $this->msj
        ]);
    }

    private function getCount()
    {
        return Operacion::when($this->Dia, function ($query) {
                return $query->whereDate('created_at', $this->Dia);
            })
            ->when($this->fechaI, function ($query) {
                return $query->whereDate('created_at', '>=', $this->fechaI);
            })
            ->when($this->fechaF, function ($query) {
                return $query->whereDate('created_at', '<=', $this->fechaF);
            })
            ->when($this->mes, function ($query) {
                return $query->whereMonth('created_at', $this->mes);
            })
            ->when($this->anio, function ($query) {
                return $query->whereYear('created_at', $this->anio);
            })
            ->count();
    }
}