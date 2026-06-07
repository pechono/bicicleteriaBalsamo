<?php

namespace App\Livewire\Print;

use Livewire\Component;
use App\Models\Bici;
use App\Models\NroIngreso;
use App\Models\Empresa;
use Carbon\Carbon;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporIngreso extends Component

{
    
    public function generateReport($nro)
    {
    

        $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
                ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
                ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
                ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
                ->join('nro_ingresos', 'nro_ingresos.id', '=', 'ingreso_bicis.nro_ingreso')
                ->where('ingreso_bicis.nro_ingreso', $nro)  // ← Cambiar el 4 por la variable
                ->select(
                    'clientes.id as cliente_id','clientes.apellido','clientes.nombre','clientes.dni','clientes.telefono','tipo_bikes.tipo',
                    'marcas.marca','bicis.color','ingreso_bicis.nro_ingreso','nro_ingresos.detalles','nro_ingresos.created_at AS fecha_ingreso','nro_ingresos.fecha_retiro'
                )
                ->first();
                
   

            if ($bicicleta) {
                $bicicleta->fecha_ingreso = Carbon::parse($bicicleta->fecha_ingreso)
                    ->format('d/m/Y');

                if ($bicicleta->fecha_retiro) {
                    $bicicleta->fecha_retiro = Carbon::parse($bicicleta->fecha_retiro)
                        ->format('d/m/Y');
                }
            }



        $procesos = NroIngreso::join('ingreso_bicis', 'ingreso_bicis.nro_ingreso', '=', 'nro_ingresos.id')
            ->leftJoin('articulos', 'articulos.id', '=', 'ingreso_bicis.articulo_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'articulos.categoria_id')
                ->where('ingreso_bicis.nro_ingreso', $nro)  // ← Cambiar el 4 por la variable
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
            )
            ->get();
            
        $emp = Empresa::first();

        // ── QR para la app móvil ──────────────────────────────────────
        $nroIngreso = NroIngreso::find($nro);
        if ($nroIngreso && !$nroIngreso->token_mobile) {
            $nroIngreso->token_mobile = \Illuminate\Support\Str::random(32);
            $nroIngreso->save();
        }

        $qrUrl = $nroIngreso?->token_mobile
            ? url('/mobile/ingreso/' . $nroIngreso->token_mobile)
            : null;

        // dompdf no soporta SVG — generamos PNG en base64
        $qrBase64 = null;
        if ($qrUrl) {
            try {
                // Intentar con Imagick (mejor calidad)
                $renderer = new ImageRenderer(
                    new RendererStyle(150),
                    new ImagickImageBackEnd()
                );
                $writer     = new Writer($renderer);
                $qrPng      = $writer->writeString($qrUrl);
                $qrBase64   = 'data:image/png;base64,' . base64_encode($qrPng);
            } catch (\Throwable $e) {
                // Fallback: SVG embebido como imagen via data URI no funciona en dompdf,
                // usamos GD si está disponible
                try {
                    $renderer = new ImageRenderer(
                        new RendererStyle(150),
                        new \BaconQrCode\Renderer\Image\EpsImageBackEnd()
                    );
                } catch (\Throwable $e2) {
                    $qrBase64 = null;
                }
            }
        }
        // ─────────────────────────────────────────────────────────────

        $pdf = Pdf::loadView(
            'livewire.print.repor-ingreso',
            compact('bicicleta', 'emp', 'procesos', 'qrBase64', 'qrUrl')
        )->setPaper('a5', 'portrait');

        return $pdf->stream();

    }
}
