<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CierreCaja;
use App\Models\CuentaCorriente;
use App\Models\Operacion;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CierreMobileController extends Controller
{
    /**
     * GET /api/mobile/cierre/resumen
     * Totales del día del usuario autenticado, aún sin cerrar.
     */
    public function resumen(Request $request)
    {
        $user = $request->user();
        $hoy  = Carbon::today();

        // Totales por tipo de venta
        $porTipo = DB::table('operacions')
            ->join('tipo_ventas', 'tipo_ventas.id', '=', 'operacions.tipoVenta_id')
            ->select(
                'tipo_ventas.id',
                'tipo_ventas.tipoVenta',
                DB::raw('SUM(operacions.venta) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->whereDate('operacions.created_at', $hoy)
            ->where('operacions.cerrado', 0)
            ->where('operacions.usuario_id', $user->id)
            ->groupBy('tipo_ventas.id', 'tipo_ventas.tipoVenta')
            ->get();

        // Desglose productos vs mano de obra (categoria_id = 1)
        $lineas = DB::table('ventas as v')
            ->join('operacions as o', 'o.id', '=', 'v.operacion')
            ->join('articulos as a', 'a.id', '=', 'v.articulo_id')
            ->whereDate('o.created_at', $hoy)
            ->where('o.cerrado', 0)
            ->where('o.usuario_id', $user->id)
            ->selectRaw('a.categoria_id as cat, o.tipoVenta_id as tipo,
                SUM(v.cantidad * v.precioF * (1 - COALESCE(v.descuento,0)/100)) as monto')
            ->groupBy('a.categoria_id', 'o.tipoVenta_id')
            ->get();

        $totalMdO      = 0;
        $mdoTarjDeb    = 0;
        $totalProductos = 0;

        foreach ($lineas as $l) {
            $monto = (float) $l->monto;
            if ((int) $l->cat === 1) {
                $totalMdO += $monto;
                if (in_array((int) $l->tipo, [3, 4])) {
                    $mdoTarjDeb += $monto;
                }
            } else {
                $totalProductos += $monto;
            }
        }

        $totalProductos = (int) round($totalProductos);
        $totalMdO       = (int) round($totalMdO);
        $mdoTarjDeb     = (int) round($mdoTarjDeb);

        // Cuenta corriente minorista del día
        $cuentaCorriente = (int) CuentaCorriente::whereDate('created_at', $hoy)
            ->where('cierreCaja', 0)
            ->where('usuario_id', $user->id)
            ->sum('entrega');

        return response()->json([
            'fecha'           => Carbon::today()->isoFormat('dddd, D [de] MMMM [de] YYYY'),
            'por_tipo'        => $porTipo,
            'total_productos' => $totalProductos,
            'total_mdo'       => $totalMdO,
            'mdo_tarj_deb'    => $mdoTarjDeb,
            'total_dia'       => $totalProductos + $totalMdO,
            'total_contador'  => $totalProductos + $mdoTarjDeb,
            'cuenta_corriente'=> $cuentaCorriente,
        ]);
    }

    /**
     * POST /api/mobile/cierre
     * Registra el cierre y marca las operaciones como cerradas.
     * SOLO Admin.
     */
    public function cerrar(Request $request)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos.'], 403);
        }

        $user = $request->user();
        $hoy  = Carbon::today();

        // Leer totales actuales para guardarlos en el registro
        $efectivo      = (int) Operacion::where('tipoVenta_id', 1)->whereDate('created_at', $hoy)->where('cerrado', 0)->where('usuario_id', $user->id)->sum('venta');
        $transferencia = (int) Operacion::where('tipoVenta_id', 2)->whereDate('created_at', $hoy)->where('cerrado', 0)->where('usuario_id', $user->id)->sum('venta');
        $debito        = (int) Operacion::where('tipoVenta_id', 3)->whereDate('created_at', $hoy)->where('cerrado', 0)->where('usuario_id', $user->id)->sum('venta');
        $tarjeta       = (int) Operacion::where('tipoVenta_id', 4)->whereDate('created_at', $hoy)->where('cerrado', 0)->where('usuario_id', $user->id)->sum('venta');
        $cuentaCte     = (int) CuentaCorriente::whereDate('created_at', $hoy)->where('cierreCaja', 0)->where('usuario_id', $user->id)->sum('entrega');

        CierreCaja::create([
            'efectivo'        => $efectivo,
            'transferencia'   => $transferencia,
            'debito'          => $debito,
            'tarjeta'         => $tarjeta,
            'cuentaCorriente' => $cuentaCte,
            'usuario'         => $user->id,
        ]);

        // Marcar cuentas corrientes y operaciones como cerradas
        CuentaCorriente::whereDate('created_at', $hoy)
            ->where('cierreCaja', 0)
            ->where('usuario_id', $user->id)
            ->update(['cierreCaja' => 1]);

        Operacion::whereDate('created_at', $hoy)
            ->where('cerrado', 0)
            ->where('usuario_id', $user->id)
            ->update(['cerrado' => 1]);

        return response()->json(['message' => 'Cierre realizado.']);
    }

    /**
     * GET /api/mobile/ventas?fecha=hoy|ayer|YYYY-MM-DD&page=
     * Historial de ventas/operaciones del usuario autenticado.
     */
    public function historial(Request $request)
    {
        $fechaParam = $request->input('fecha', 'hoy');
        $fecha = match($fechaParam) {
            'hoy'  => Carbon::today(),
            'ayer' => Carbon::yesterday(),
            default => Carbon::parse($fechaParam)->startOfDay(),
        };

        $operaciones = Operacion::with(['tipoVenta', 'cliente'])
            ->where('usuario_id', $request->user()->id)
            ->whereDate('created_at', $fecha)
            ->orderByDesc('created_at')
            ->paginate(30);

        $items = collect($operaciones->items())->map(fn($op) => [
            'id'         => $op->id,
            'total'      => (int) $op->venta,
            'tipo'       => $op->tipoVenta?->tipoVenta ?? '—',
            'cliente'    => $op->cliente
                ? "{$op->cliente->apellido}, {$op->cliente->nombre}"
                : '—',
            'cerrado'    => (bool) $op->cerrado,
            'hora'       => $op->created_at?->format('H:i'),
        ]);

        $totalDia = (int) Operacion::where('usuario_id', $request->user()->id)
            ->whereDate('created_at', $fecha)
            ->sum('venta');

        return response()->json([
            'fecha'        => $fecha->isoFormat('D [de] MMMM'),
            'total_dia'    => $totalDia,
            'data'         => $items,
            'current_page' => $operaciones->currentPage(),
            'last_page'    => $operaciones->lastPage(),
            'total'        => $operaciones->total(),
        ]);
    }
}
