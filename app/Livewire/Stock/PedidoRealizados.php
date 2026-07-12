<?php

namespace App\Livewire\Stock;

use App\Models\Pedido;
use App\Models\Proveedor;
use Livewire\Component;

class PedidoRealizados extends Component
{
    public function render()
    {
        $pedidos = Pedido::join('proveedors', 'proveedors.id', '=', 'pedidos.proveedor_id')
                            ->join('articulos', 'articulos.id', '=', 'pedidos.articulo_id')
                            ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
                            ->join('stocks','stocks.articulo_id','articulos.id')
                            ->select('pedidos.pedido','proveedors.nombre','proveedors.telefono','proveedors.localidad',
                            'proveedors.direccion','pedidos.created_at as Fecha', 'stocks.codigo_proveedor')
                            ->groupBy('pedidos.pedido','proveedors.nombre','proveedors.telefono','proveedors.localidad','proveedors.direccion','pedidos.created_at','stocks.codigo_proveedor')
                            ->get();
        return view('livewire.stock.pedido-realizados', compact('pedidos'));
    }

    public $verPedido=false;

    public $artPedido=[];
    public $proveedor;
    public $localidad;
    public $pedido;

    public function verPed($pedidoId){

        $this->artPedido = Pedido::join('proveedors', 'proveedors.id', '=', 'pedidos.proveedor_id')
                        ->join('articulos', 'articulos.id', '=', 'pedidos.articulo_id')
                        ->join('unidads', 'unidads.id', '=', 'articulos.unidad_id')
                        ->join('stocks','stocks.articulo_id','articulos.id')

                        ->select(
                            'articulos.articulo','articulos.presentacion','unidads.unidad','pedidos.cantidad',
                            'pedidos.pedido','proveedors.nombre','proveedors.telefono','proveedors.localidad',
                            'proveedors.direccion','pedidos.created_at as Fecha', 'stocks.codigo_proveedor','articulos.codigo')
                        ->where('pedidos.pedido','=',$pedidoId)
                        ->get();

        $proveedor=Pedido::join('proveedors','proveedors.id','=','pedidos.proveedor_id')
                            ->where('pedidos.pedido','=',$pedidoId)
                            ->select('proveedors.nombre','pedidos.pedido', 'proveedors.telefono','proveedors.localidad','proveedors.direccion')->first();

        $this->proveedor=$proveedor->nombre;
        $this->localidad=$proveedor->localidad;
        $this->pedido=$proveedor->pedido;



        $this->verPedido=true;
    }

    public function cerrarModal()
    {
        $this->verPedido = false;
        $this->artPedido = [];
        $this->proveedor = [];
    }


}
