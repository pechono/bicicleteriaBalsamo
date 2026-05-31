<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Models\IngresoBici;
use App\Models\NroIngreso;
use App\Models\Bici;
use App\Livewire\Traits\WithWhatsApp; // 👈 AGREGADO

use Illuminate\Support\Facades\Log;   // 👈 AGREGADO

class IngresoImp extends Component
{
 use WithWhatsApp;
    public $nro_ingreso;
    public $ingreso;
   
    public function mount($nro_ingreso)
    {
        $this->nro_ingreso = $nro_ingreso;
    }

    public function render()
    {
        $procesos = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
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
            ->where('ingreso_bicis.nro_ingreso', $this->nro_ingreso)
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

    public $botonSalir = false;

    /**
     * 🚀 MÉTODO MODIFICADO: Imprimir + WhatsApp
     */
    public function imprimirComprobante()
    {
        // Validar que haya fecha de retiro
        if (!$this->fecha_retiro) {
            $this->dispatch('notify', 'Primero seleccioná una fecha estimada de entrega', 'warning');
            return;
        }

        // Guardar fecha
        $this->actualizarFechaRetiro();

        // Obtener datos de la bici para el WhatsApp
        $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
            ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
            ->where('ingreso_bicis.nro_ingreso', $this->nro_ingreso)
            ->select(
                'clientes.id as cliente_id',
                'clientes.apellido',
                'clientes.nombre',
                'clientes.telefono',
                'tipo_bikes.tipo',
                'marcas.marca',
                'bicis.color',
                'ingreso_bicis.nro_ingreso',
                'nro_ingresos.detalles',
                'nro_ingresos.fecha_retiro'
            )
            ->first();

        // Enviar WhatsApp si hay teléfono
        if ($bicicleta && $bicicleta->telefono) {
            $this->enviarWhatsAppIngreso($bicicleta);
        }

        // Redirigir al reporte
         $this->botonSalir=true;
        return redirect()->route('service.reporteIngreso', ['nro_ingreso' => $this->nro_ingreso]);
        // $this->botonSalir = true;
        // return redirect()->route('service.reporteIngreso', ['nro_ingreso' => $this->nro_ingreso]);
    }

    /**
     * 📱 NUEVO MÉTODO: Enviar WhatsApp de ingreso
     */
    protected function enviarWhatsAppIngreso($bicicleta)
    {
        $nombre        = $bicicleta->nombre;
        $nroFormateado = str_pad($bicicleta->nro_ingreso, 4, '0', STR_PAD_LEFT);
        $marca         = $bicicleta->marca  ?? '';
        $color         = $bicicleta->color  ?? '';
        $tipo          = $bicicleta->tipo   ?? '';

        $encabezado = "🔧 *BICICLETERÍA BALSAMO* 🔧\n----------------------------\nHola {$nombre} 👋\nTu bicicleta ingresó al taller.\n\nN° de ingreso: *#{$nroFormateado}*\n🚲 Marca: {$marca}\n🎨 Color: {$color}\n📋 Tipo: {$tipo}";

        if ($this->fecha_retiro) {
            $fecha = \Carbon\Carbon::parse($this->fecha_retiro);
            \Carbon\Carbon::setLocale('es');
            $fechaFormateada = ucfirst($fecha->isoFormat('dddd D [de] MMMM [de] YYYY'));
            $mensaje = "{$encabezado}\n\n📅 Fecha estimada de retiro:\n    {$fechaFormateada}\n----------------------------\n¡Gracias por elegirnos! 🙏";
        } else {
            $mensaje = "{$encabezado}\n\nEn cuanto esté lista te avisamos.\n----------------------------\n¡Gracias por elegirnos! 🙏";
        }

        $this->sendWhatsAppMessage($bicicleta->telefono, $mensaje);
    }

    public function ver()
    {
        return redirect()->route('service.ingresarBike');
    }



     public function enviarWhatsApp()
{
    if (!$this->fecha_retiro) {
        $this->dispatch('notify', 'Primero seleccioná una fecha estimada de entrega', 'warning');
        return;
    }
    
    $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
        ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
        ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
        ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
        ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
        ->where('ingreso_bicis.nro_ingreso', $this->nro_ingreso)
        ->first();
    
    if ($bicicleta && $bicicleta->telefono) {
        $this->enviarWhatsAppIngreso($bicicleta);
        $this->dispatch('notify', '✅ WhatsApp enviado correctamente', 'success');
    } else {
        $this->dispatch('notify', 'El cliente no tiene teléfono registrado', 'warning');
    }
}
}