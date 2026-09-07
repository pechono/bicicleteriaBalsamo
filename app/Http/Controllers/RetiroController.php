<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RetiroController extends Controller
{
    /**
     * Vista pública y liviana del monto a retirar (link del WhatsApp de "bici lista").
     * Sin login, sin PDF. La ruta valida el hash antes de llamar acá.
     */
    public function show($nro)
    {
        $info = DB::table('ingreso_bicis as ib')
            ->join('bicis as b', 'b.id', '=', 'ib.bici_id')
            ->join('clientes as c', 'c.id', '=', 'b.cliente_id')
            ->join('marcas as m', 'm.id', '=', 'b.marca_id')
            ->leftJoin('nro_ingresos as ni', 'ni.id', '=', 'ib.nro_ingreso')
            ->where('ib.nro_ingreso', $nro)
            ->select('c.nombre', 'b.color', 'm.marca', 'ib.nro_ingreso', 'ni.estado')
            ->first();

        abort_if(!$info, 404);

        // Total del servicio (monto del egreso) para ese ingreso.
        $monto = DB::table('egreso_bicis as e')
            ->join('ingreso_bicis as i', 'i.bici_id', '=', 'e.ingreso_bici_id')
            ->join('nro_egresos as ne', 'ne.id', '=', 'e.nro_egreso')
            ->where('i.nro_ingreso', $nro)
            ->value('ne.monto');

        return view('retiro.monto', ['info' => $info, 'monto' => $monto]);
    }
}
