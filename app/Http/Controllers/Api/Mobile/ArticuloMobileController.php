<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloMobileController extends Controller
{
    /**
     * GET /api/mobile/articulos/qr/{codigo}
     * Busca un artículo por código QR. Solo devuelve precio final (precioF).
     */
    public function porQr($codigo)
    {
        $articulo = Articulo::where('codigo', $codigo)
            ->where('activo', true)
            ->select('id', 'articulo', 'codigo', 'presentacion', 'precioF', 'categoria_id')
            ->with('categoria:id,categoria')
            ->first();

        if (!$articulo) {
            return response()->json(['message' => 'Artículo no encontrado.'], 404);
        }

        return response()->json($articulo);
    }

    /**
     * GET /api/mobile/articulos/buscar?q=texto
     * Búsqueda por nombre (para cuando el QR está roto o sucio).
     */
    public function buscar(Request $request)
    {
        $q = $request->input('q', '');

        $articulos = Articulo::where('activo', true)
            ->where(function ($query) use ($q) {
                $query->where('articulo', 'like', "%{$q}%")
                      ->orWhere('codigo', 'like', "%{$q}%");
            })
            ->select('id', 'articulo', 'codigo', 'presentacion', 'precioF')
            ->limit(20)
            ->get();

        return response()->json($articulos);
    }
}
