<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\IngresoBici;
use App\Models\NroIngreso;
use App\Models\Bici;

class IngresoImp extends Component
{
    public $nro_ingreso;
    public $ingreso;
   
    public function mount($nro_ingreso)  // Este mount nunca se llama
    {
        $this->nro_ingreso = $nro_ingreso;

    }
    public function render()
    {
        $procesos =NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->leftJoin('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->select(
                'nro_ingresos.id',
                'nro_ingresos.detalles as detalles_operacion',
                'nro_ingresos.estado',
                'nro_ingresos.fecha_retiro',
                'ingreso_bicis.articulo_id',
                'ingreso_bicis.detalles as detalles_articulo',
                'ingreso_bicis.bici_id',
                'articulos.articulo',
                'articulos.presentacion',
                'categorias.categoria'
            )->where('nro_ingresos.id', $this->nro_ingreso)
            ->get();

            $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
                ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
                ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
                ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
                ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
                ->where('ingreso_bicis.nro_ingreso', $this->nro_ingreso)  // ← Cambiar el 4 por la variable
                ->select(
                    'clientes.id as cliente_id','clientes.apellido','clientes.nombre','clientes.dni','clientes.telefono','tipo_bikes.tipo',
                    'marcas.marca','bicis.color','ingreso_bicis.nro_ingreso','nro_ingresos.detalles'
                )
                ->first();

        return view('livewire.service.ingreso-imp', compact('procesos', 'bicicleta'));
    }
    public $fecha_retiro;
    public function actualizarFechaRetiro()
    {
        $nroIngreso = NroIngreso::find($this->nro_ingreso);
        if ($nroIngreso) {
            $nroIngreso->fecha_retiro = $this->fecha_retiro;
            $nroIngreso->save();
        }
    }






    public function imprimirComprobante()  // Este método nunca se ejecuta
    {
        return redirect()->route('service.reporteIngreso', ['nro_ingreso' => $this->nro_ingreso]);
    }
}