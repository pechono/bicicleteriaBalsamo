<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ClienteMayorista;
use App\Support\Busqueda;
use Illuminate\Http\Request;

/**
 * Portal público del cliente mayorista (se entra por /portal/{token}, sin login).
 * Muestra el stock disponible SIN precios, y la cuenta corriente solo si el
 * cliente la tiene habilitada.
 */
class PortalController extends Controller
{
    public function show(Request $request, string $token)
    {
        $cliente = ClienteMayorista::where('token', $token)->where('activo', true)->firstOrFail();

        $q = trim((string) $request->query('q', ''));

        // Stock disponible (stock > 0), SIN precios. Excluye servicios (categoria_id = 1).
        $articulos = Articulo::query()
            ->where('articulos.activo', 1)
            ->where('articulos.categoria_id', '<>', 1)
            ->join('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->where('stocks.stock', '>', 0)
            ->when($q !== '', fn ($query) => Busqueda::palabras(
                $query, $q, ['articulos.articulo', 'articulos.codigo', 'categorias.categoria']
            ))
            ->select('articulos.id', 'articulos.articulo', 'articulos.codigo', 'categorias.categoria', 'stocks.stock')
            ->orderBy('categorias.categoria')
            ->orderBy('articulos.articulo')
            ->paginate(30)
            ->withQueryString();

        $movimientos = collect();
        $saldo = 0.0;
        if ($cliente->cuenta_corriente_habilitada) {
            $movimientos = $cliente->cuentaCorriente()->orderByDesc('created_at')->limit(100)->get();
            $saldo = $cliente->saldoPendiente();
        }

        return view('portal.mayorista', compact('cliente', 'articulos', 'movimientos', 'saldo', 'q'));
    }
}
