<?php

namespace App\Livewire\Operacion;

use App\Livewire\Venta\CuentaCorriente;
use App\Models\ArtCuentaCorriente;
use App\Models\Articulo;
use App\Models\Operacion;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListOperacionlivewire extends Component
{
    use WithPagination;
    
    public $meses;
    public $verOperacion = false;
    public $ventaOp = [];
    public $operacion;
    public $cliente;
    public $fecha;
    public $usuario;
    public $tipo;
    public $tipoId;
    public $venta;
    public $suma;
    public $os = [];
    public $idOp;
    public $listArt;
    public $sumTotal = 0;
    public $detalles = false;
    public $fechaI;
    public $fechaF;
    public $Dia;
    public $anio;
    public $mes;
    public $msj;
    public $datos = [
        'opcion' => null,
        'valor1' => null,
        'valor2' => null
    ];
    public $q = ''; // Para búsqueda
    public $sortBy = 'id';
    public $sortAsc = true;

    public function sortby($field)
    {
        if ($this->sortBy === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortBy = $field;
            $this->sortAsc = true;
        }
    }

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

        $query = Operacion::join('ventas', 'ventas.operacion', '=', 'operacions.id')
            ->join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->join('users', 'users.id', '=', 'operacions.usuario_id')
            ->join('clientes', 'clientes.id', '=', 'operacions.cliente_id')
            ->select('operacions.id', 'operacions.venta', 'clientes.apellido', 'clientes.nombre',
                'users.name', 'operacions.created_at AS Fecha', 'tipo_ventas.tipoVenta')
            ->distinct();

        // Aplicar filtros según la opción seleccionada
        if ($this->datos['opcion'] && $this->datos['valor1']) {
            switch ($this->datos['opcion']) {
                case 'dia':
                    $query->whereDate('operacions.created_at', $this->datos['valor1']);
                    break;
                case 'rango':
                    $query->whereBetween('operacions.created_at', [$this->datos['valor1'], $this->datos['valor2']]);
                    break;
                case 'mes':
                    if ($this->datos['valor2']) {
                        $query->whereYear('operacions.created_at', $this->datos['valor2'])
                              ->whereMonth('operacions.created_at', $this->datos['valor1']);
                    } else {
                        $query->whereMonth('operacions.created_at', $this->datos['valor1']);
                    }
                    break;
                case 'año':
                    $query->whereYear('operacions.created_at', $this->datos['valor1']);
                    break;
            }
        }

        // Aplicar búsqueda
        if ($this->q) {
            $query->where(function($q) {
                $q->where('operacions.id', 'LIKE', '%' . $this->q . '%')
                  ->orWhere('clientes.apellido', 'LIKE', '%' . $this->q . '%')
                  ->orWhere('clientes.nombre', 'LIKE', '%' . $this->q . '%')
                  ->orWhere('users.name', 'LIKE', '%' . $this->q . '%');
            });
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortAsc ? 'asc' : 'desc');
        
        $ops = $query->paginate(10);

        $aniosUnicos = Operacion::selectRaw('YEAR(created_at) AS anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('livewire.operacion.list-operacionlivewire', compact('ops', 'aniosUnicos'));
    }

    public function verOp($oper)
    {
        $this->idOp = $oper;
        
        $this->ventaOp = Operacion::join('ventas', 'ventas.operacion', '=', 'operacions.id')
            ->join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->join('users', 'users.id', '=', 'operacions.usuario_id')
            ->join('clientes', 'clientes.id', '=', 'operacions.cliente_id')
            ->join('articulos', 'articulos.id', '=', 'ventas.articulo_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->select('operacions.id', 'operacions.venta', 'clientes.apellido', 'clientes.nombre',
                'users.name', 'operacions.created_at AS Fecha', 'tipo_ventas.tipoVenta',
                'articulos.articulo', 'ventas.precioF', 'ventas.cantidad', 'articulos.presentacion', 
                'unidads.unidad', 'ventas.descuento')
            ->where('operacions.id', $this->idOp)
            ->get();

        $this->os = Operacion::select(
                'operacions.id',
                'operacions.venta',
                'clientes.apellido',
                'clientes.nombre',
                'users.name',
                'operacions.created_at AS Fecha',
                'tipo_ventas.tipoVenta',
                'tipo_ventas.id as tipoId',
                DB::raw('(SELECT SUM(ventas.precioF * ventas.cantidad) FROM ventas WHERE ventas.operacion = operacions.id) AS sumaVentas')
            )
            ->join('ventas', 'ventas.operacion', '=', 'operacions.id')
            ->join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->join('users', 'users.id', '=', 'operacions.usuario_id')
            ->join('clientes', 'clientes.id', '=', 'operacions.cliente_id')
            ->where('operacions.id', $this->idOp)
            ->first();

      if ($this->os) {
    $this->operacion = $this->os['id'];
    $this->cliente = $this->os['apellido'] . " " . $this->os['Fecha'];
    $this->usuario = $this->os['name'];
    $this->tipo = $this->os['tipoVenta'];
    $this->tipoId = $this->os['tipoId'];
    $this->venta = $this->os['venta'];
    $this->suma = $this->os['sumaVentas'];
}
        $this->verOperacion = true;
    }

    public function cancelarCuenta($operacion)
    {
        $this->sumTotal = 0;
        
        // Obtener los artículos de la venta
        $this->listArt = Venta::join('articulos', 'articulos.id', '=', 'ventas.articulo_id')
            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
            ->select('ventas.*', 'articulos.articulo', 'articulos.presentacion', 'unidads.unidad')
            ->where('ventas.operacion', $operacion)
            ->get();
        
        foreach($this->listArt as $v) {
            $this->sumTotal += $v->cantidad * $v->precioF;
        }
        
        $this->detalles = true;
    }
    
    public function confirmarCancelacion()
    {
        DB::beginTransaction();
        try {
            // Actualizar cada venta
            foreach($this->listArt as $v) {
                Venta::where('id', $v->id)->update([
                    'precioF' => $v->precioF,
                    'precioI' => $v->precioI,
                ]);
            }
            
            // Actualizar la operación
            Operacion::where('id', $this->idOp)->update([
                'venta' => $this->sumTotal
            ]);
            
            // Crear cuenta corriente (ajusta según tu lógica)
            // CuentaCorriente::create([...]);
            
            DB::commit();
            $this->detalles = false;
            $this->verOperacion = false;
            session()->flash('message', 'Cuenta cancelada exitosamente');
            $this->dispatch('refreshComponent');
            
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error al cancelar la cuenta: ' . $e->getMessage());
        }
    }

    public function cancelarDE()
    {
        $this->resetFiltros(['Dia', 'anio', 'mes']);
        
        if ($this->fechaI && $this->fechaF) {
            $this->msj = 'Venta entre los días ' . Carbon::parse($this->fechaI)->format('d/m/Y') . ' - ' . Carbon::parse($this->fechaF)->format('d/m/Y');
            $this->prepDatos('rango', $this->fechaI, $this->fechaF);
        } else {
            $this->msj = 'Seleccione un rango de fechas';
        }
        $this->resetPage();
    }

    public function cancelarD()
    {
        $this->resetFiltros(['fechaF', 'fechaI', 'anio', 'mes']);
        
        if ($this->Dia) {
            $this->msj = 'Venta en el día ' . Carbon::parse($this->Dia)->format('d/m/Y');
            $this->prepDatos('dia', $this->Dia, null);
        } else {
            $this->msj = 'Seleccione un día';
        }
        $this->resetPage();
    }

    public function cancelarM()
    {
        $this->resetFiltros(['fechaF', 'fechaI', 'Dia', 'anio']);
        
        if ($this->mes) {
            $this->msj = 'Ventas en ' . $this->meses[$this->mes];
            $this->prepDatos('mes', $this->mes, $this->anio);
        } else {
            $this->msj = 'Seleccione un mes';
        }
        $this->resetPage();
    }

    public function cancelarA()
    {
        $this->resetFiltros(['fechaF', 'fechaI', 'Dia', 'mes']);
        
        if ($this->anio) {
            $this->msj = 'Ventas en el año ' . $this->anio;
            $this->prepDatos('año', $this->anio, null);
        } else {
            $this->msj = 'Seleccione un año';
        }
        $this->resetPage();
    }
    
    public function resetFiltros($campos)
    {
        foreach($campos as $campo) {
            $this->$campo = '';
        }
    }
    
    public function prepDatos($opcion, $v1, $v2 = null)
    {
        $this->datos = [
            'opcion' => $opcion,
            'valor1' => $v1,
            'valor2' => $v2
        ];
    }
    
    public function limpiarFiltros()
    {
        $this->datos = ['opcion' => null, 'valor1' => null, 'valor2' => null];
        $this->q = '';
        $this->Dia = '';
        $this->fechaI = '';
        $this->fechaF = '';
        $this->mes = '';
        $this->anio = '';
        $this->msj = '';
        $this->resetPage();
    }
}