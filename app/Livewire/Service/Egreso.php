<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\Bici;
use App\Models\Cliente;
use App\Models\EgresoBici;
use App\Models\NroIngreso;
use Illuminate\Support\Facades\DB;

class Egreso extends Component
{
    public $clienteBici;
    public $procesos;
    public $nro_ingreso = 8;
    public $msj = '';
    public $cliente;
    public $bicicleta;
    
    public $searchIngreso = '';     // Búsqueda por número de ingreso
    public $searchCliente = '';
    public $active=1;
    public $q;
    public function render()
{
    $clientes = collect();
    
    $clientes = Cliente::where('clientes.activo', $this->active)
            ->join('bicis', 'bicis.cliente_id', '=', 'clientes.id')
            ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
            ->when($this->searchIngreso != '', function ($query) {
                // Filtro por número de ingreso (exacto o como like)
                return $query->where('ingreso_bicis.nro_ingreso', 'LIKE', '%' . $this->searchIngreso . '%');
            })
            ->when($this->searchCliente != '', function ($query) {
                // Filtro por datos del cliente (nombre, apellido, DNI, teléfono)
                return $query->where(function ($subquery) {
                    $subquery->where('clientes.nombre', 'LIKE', '%' . $this->searchCliente . '%')
                        ->orWhere('clientes.apellido', 'LIKE', '%' . $this->searchCliente . '%')
                        ->orWhere('clientes.dni', 'LIKE', '%' . $this->searchCliente . '%')
                        ->orWhere('clientes.telefono', 'LIKE', '%' . $this->searchCliente . '%');
                });
            })
            ->select(
                'ingreso_bicis.nro_ingreso',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.dni',
                'clientes.telefono',
                'bicis.color',
                'marcas.marca',
                'tipo_bikes.tipo',
                'nro_ingresos.estado'

            )
            ->distinct()
            ->paginate(10);
        
        return view('livewire.service.egreso', compact('clientes'));
    }

    public $ver=false;
    public function verCliente($nro_ingreso)
    {
        // Aquí puedes agregar la lógica para mostrar el detalle del ingreso
        $this->ver = $nro_ingreso;
        $this->ingresoProceso($nro_ingreso);
    $this->mostrarProcesosTerminado($nro_ingreso);

        $this->nroDetalles($nro_ingreso);
        session()->flash('message', 'Mostrando detalle para el ingreso: ' . $nro_ingreso);
    }
    public function cerrarDetalles(){
        $this->ver=false;
    }
   

    public function ingresoProceso($nro_ingreso)
    {
        $this->procesos = Bici::join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
            ->join('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->where('ingreso_bicis.nro_ingreso', $nro_ingreso)
            ->select(
                'articulos.id',
                'stocks.codigo_proveedor',
                'articulos.codigo',
                'articulos.articulo'
            )->get();

        

    }
    public $nDetalles;
    public function nroDetalles($nro_ingreso)
    {
        $this->nDetalles=NroIngreso::find($nro_ingreso);
    }
    public function terminarProceso($nro_ingreso)
    {
        // Aquí puedes agregar la lógica para terminar el proceso del ingreso
       return redirect()->route('service.egresoTerminar', ['nro_ingreso' => $nro_ingreso]);
    }
    public $procesosTerminado;
    public function mostrarProcesosTerminado($nro)
    {
        $this->procesosTerminado = EgresoBici::select(
                'egreso_bicis.id',  // Importante para distinguir
                'ingreso_bicis.nro_ingreso',
                'stocks.codigo_proveedor',
                'articulos.codigo',
                'articulos.articulo',
                'egreso_bicis.precio_inicial',
                'egreso_bicis.precio_final',
                'egreso_bicis.cantidad',
                'ingreso_bicis.bici_id'
            )
            ->join('articulos', 'articulos.id', '=', 'egreso_bicis.articulo_id')
            ->leftJoin('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'egreso_bicis.ingreso_bici_id')
            ->where('ingreso_bicis.nro_ingreso', $nro)
            ->distinct('egreso_bicis.id')  // Distinct por ID de egreso
            ->get();
    }

   public function terminarProcesoVenta($nro)
   {
        return redirect()->route('service.terminarVentaProceso', ['nro_ingreso' => $nro]);
   }
}