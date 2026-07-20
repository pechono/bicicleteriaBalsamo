<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\CuentaCorriente;
use App\Models\Operacion;
use Illuminate\Http\Request;

class ClienteMobileController extends Controller
{
    /**
     * GET /api/mobile/clientes?q=texto
     * Busca clientes minoristas por nombre, apellido o DNI.
     */
    public function buscar(Request $request)
    {
        $q = trim($request->input('q', ''));

        $clientes = Cliente::where('activo', 1)
            ->where(function ($query) use ($q) {
                foreach (array_filter(explode(' ', $q)) as $palabra) {
                    $query->where(fn($sub) =>
                        $sub->where('nombre',   'like', "%{$palabra}%")
                            ->orWhere('apellido', 'like', "%{$palabra}%")
                            ->orWhere('dni',      'like', "%{$palabra}%")
                    );
                }
            })
            ->select('id', 'nombre', 'apellido', 'dni', 'telefono')
            ->orderBy('apellido')
            ->limit(20)
            ->get();

        // Adjuntar saldo de cuenta corriente a cada cliente
        $ids = $clientes->pluck('id');
        $saldos = CuentaCorriente::whereIn('cliente_id', $ids)
            ->where('cerrado', 0)
            ->selectRaw('cliente_id, SUM(deuda) - SUM(entrega) as saldo')
            ->groupBy('cliente_id')
            ->pluck('saldo', 'cliente_id');

        return response()->json(
            $clientes->map(fn($c) => array_merge($c->toArray(), [
                'saldo' => (int) round((float) ($saldos[$c->id] ?? 0)),
            ]))
        );
    }

    /**
     * GET /api/mobile/clientes/{id}/cuenta
     * Movimientos y saldo de cuenta corriente de un cliente.
     */
    public function cuenta(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $movimientos = CuentaCorriente::where('cliente_id', $id)
            ->where('cerrado', 0)
            ->orderByDesc('created_at')
            ->select('id', 'operacion_id', 'entrega', 'deuda', 'created_at')
            ->limit(50)
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'operacion_id'=> $m->operacion_id,
                'entrega'     => (int) $m->entrega,
                'deuda'       => (int) $m->deuda,
                'fecha'       => $m->created_at?->format('d/m/Y H:i'),
                'tipo'        => $m->deuda > 0 ? 'deuda' : 'pago',
            ]);

        $saldo = (int) round(
            CuentaCorriente::where('cliente_id', $id)->where('cerrado', 0)
                ->selectRaw('SUM(deuda) - SUM(entrega) as saldo')
                ->value('saldo') ?? 0
        );

        return response()->json([
            'cliente'      => [
                'id'       => $cliente->id,
                'nombre'   => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'dni'      => $cliente->dni,
                'telefono' => $cliente->telefono,
            ],
            'saldo'        => $saldo,
            'movimientos'  => $movimientos,
        ]);
    }

    /**
     * POST /api/mobile/clientes/{id}/pago
     * Registra un pago a cuenta corriente.
     * SOLO Admin.
     */
    public function registrarPago(Request $request, $id)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos.'], 403);
        }

        $request->validate(['monto' => 'required|integer|min:1']);

        Cliente::findOrFail($id);

        CuentaCorriente::create([
            'cliente_id'   => $id,
            'usuario_id'   => $request->user()->id,
            'operacion_id' => 0,
            'entrega'      => $request->monto,
            'deuda'        => 0,
            'cerrado'      => 0,
            'cierreCaja'   => 0,
        ]);

        $saldo = (int) round(
            CuentaCorriente::where('cliente_id', $id)->where('cerrado', 0)
                ->selectRaw('SUM(deuda) - SUM(entrega) as saldo')
                ->value('saldo') ?? 0
        );

        return response()->json([
            'message' => 'Pago registrado.',
            'saldo'   => $saldo,
        ]);
    }
}
