<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Livewire\Print\ReportVentaO;
use App\Models\Operacion;
use App\Support\WhatsApp;
use Illuminate\Http\Request;

class ReportVentaController extends Controller
{
    public function pasar($operacion, $volver)
    {
    return view('venta.reporte',compact('operacion','volver'));
    }

    public function enviarWhatsApp($operacion, $volver)
    {
        $op = Operacion::join('clientes', 'clientes.id', '=', 'operacions.cliente_id')
            ->where('operacions.id', $operacion)
            ->select('clientes.nombre', 'clientes.telefono')
            ->first();

        if (!$op || empty(trim((string) $op->telefono))) {
            return redirect()->route('venta.reporte', ['operacion' => $operacion, 'volver' => $volver])
                ->with('error', 'El cliente no tiene teléfono registrado');
        }

        $bytes   = app(ReportVentaO::class)->pdfBytes($operacion);
        $nro     = str_pad($operacion, 4, '0', STR_PAD_LEFT);
        $caption = "🧾 *BICICLETERÍA BALSAMO*\nGracias por tu compra. Adjuntamos tu comprobante N° {$nro}.";

        WhatsApp::encolarPdf($op->telefono, $bytes, "comprobante-{$nro}.pdf", $caption);

        return redirect()->route('venta.reporte', ['operacion' => $operacion, 'volver' => $volver])
            ->with('mensaje', 'Comprobante en cola para WhatsApp ✓');
    }
}
