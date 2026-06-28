<?php

namespace App\Livewire\Print;

use App\Models\Empresa;
use Livewire\Component;
use App\Models\Operacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportVentaO extends Component
{
    public $datos=[];
    public $total;
    public $emp=[];


    private function build($operacion)
    {


        $ventaOp=Operacion::join('ventas','ventas.operacion','=','operacions.id')
        ->join('tipo_ventas','tipo_ventas.id','=','operacions.tipoVenta_id')
        ->join('users','users.id','=','operacions.usuario_id')
        ->join('clientes','clientes.id','=','operacions.cliente_id')
        ->join('articulos','articulos.id','=', 'ventas.articulo_id')
        ->join('unidads','unidads.id','=','articulos.unidad_id')
        ->select('operacions.id','operacions.venta','clientes.apellido','clientes.nombre',
        'users.name','operacions.created_at AS Fecha', 'tipo_ventas.tipoVenta','ventas.articulo_id',
        'articulos.articulo', 'ventas.precioF','ventas.cantidad', 'articulos.presentacion','unidads.unidad','ventas.descuento')
        ->where('operacions.id',$operacion)->get();

        $datos=Operacion::join('ventas','ventas.operacion','=','operacions.id')
        ->join('tipo_ventas','tipo_ventas.id','=','operacions.tipoVenta_id')
        ->join('users','users.id','=','operacions.usuario_id')
        ->join('clientes','clientes.id','=','operacions.cliente_id')
        ->join('articulos','articulos.id','=', 'ventas.articulo_id')
        ->join('unidads','unidads.id','=','articulos.unidad_id')
        ->select('operacions.id','operacions.venta','clientes.apellido','clientes.nombre', 'clientes.telefono',
        'users.name','operacions.created_at AS Fecha', 'tipo_ventas.tipoVenta',
        'articulos.articulo', 'ventas.precioF','ventas.cantidad', 'articulos.presentacion','unidads.unidad','ventas.descuento')
        ->where('operacions.id',$operacion)->first();

        $emp=Empresa::first();


        return Pdf::loadView('livewire.print.report-venta-o', compact('ventaOp','emp','datos'));
    }

    public function generateReport($operacion)
    {
        return $this->build($operacion)->stream();
    }

    /** Devuelve el comprobante como bytes (para enviarlo por WhatsApp). */
    public function pdfBytes($operacion)
    {
        return $this->build($operacion)->output();
    }
}
