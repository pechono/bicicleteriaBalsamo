<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Stock;
use Illuminate\Http\Request;

class ArticuloMobileController extends Controller
{
    /**
     * GET /api/mobile/articulos?q=&categoria_id=&page=
     * Listado de artículos con stock y cantidades (misma consulta que StockLivewire web).
     * Mecánico: ve precioF y stock. Admin: además precioI y stockMinimo.
     */
    public function index(Request $request)
    {
        $isAdmin = $request->user()->user_type === 'Admin';
        $q           = $request->input('q', '');
        $categoriaId = $request->input('categoria_id');

        $articulos = Articulo::where('articulos.activo', 1)
            ->when($q, fn($query) =>
                $query->where(fn($sub) =>
                    $sub->where('articulos.articulo', 'like', "%{$q}%")
                        ->orWhere('articulos.detalles', 'like', "%{$q}%")
                        ->orWhere('articulos.codigo',   'like', "%{$q}%")
                )
            )
            ->when($categoriaId, fn($query) =>
                $query->where('articulos.categoria_id', $categoriaId)
            )
            ->join('categorias', 'categorias.id', '=', 'articulos.categoria_id')
            ->join('stocks',     'stocks.articulo_id', '=', 'articulos.id')
            ->orderBy('articulos.articulo')
            ->select(
                'articulos.id', 'articulos.codigo', 'articulos.articulo',
                'articulos.presentacion', 'articulos.precioF', 'articulos.precioI',
                'articulos.categoria_id', 'categorias.categoria',
                'stocks.stock', 'stocks.stockMinimo'
            )
            ->paginate(30);

        $items = collect($articulos->items())->map(function ($a) use ($isAdmin) {
            $data = [
                'id'           => $a->id,
                'codigo'       => $a->codigo,
                'articulo'     => $a->articulo,
                'presentacion' => $a->presentacion,
                'categoria'    => $a->categoria,
                'categoria_id' => $a->categoria_id,
                'precioF'      => $a->precioF,
                'stock'        => $a->stock,
            ];
            if ($isAdmin) {
                $data['precioI']     = $a->precioI;
                $data['stockMinimo'] = $a->stockMinimo;
            }
            return $data;
        });

        return response()->json([
            'data'         => $items,
            'current_page' => $articulos->currentPage(),
            'last_page'    => $articulos->lastPage(),
            'total'        => $articulos->total(),
        ]);
    }

    /**
     * GET /api/mobile/categorias
     * Categorías para el filtro del listado.
     */
    public function categorias()
    {
        return response()->json(
            Categoria::orderBy('categoria')->select('id', 'categoria')->get()
        );
    }

    /**
     * PATCH /api/mobile/articulos/{id}/stock
     * Actualiza stock y stock mínimo. SOLO Admin (igual que @admin en stock-livewire web).
     */
    public function actualizarStock(Request $request, $id)
    {
        if ($request->user()->user_type !== 'Admin') {
            return response()->json(['message' => 'Sin permisos. Solo el administrador puede modificar el stock.'], 403);
        }

        $request->validate([
            'stock'       => 'required|numeric|min:0',
            'stockMinimo' => 'nullable|numeric|min:0',
        ]);

        $stock = Stock::where('articulo_id', $id)->firstOrFail();
        $payload = ['stock' => $request->stock];
        if ($request->filled('stockMinimo')) {
            $payload['stockMinimo'] = $request->stockMinimo;
        }
        $stock->update($payload);

        return response()->json([
            'message'     => 'Stock actualizado.',
            'stock'       => $stock->stock,
            'stockMinimo' => $stock->stockMinimo,
        ]);
    }

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

        $articulos = Articulo::where('articulos.activo', true)
            ->where(function ($query) use ($q) {
                $query->where('articulos.articulo', 'like', "%{$q}%")
                      ->orWhere('articulos.codigo', 'like', "%{$q}%");
            })
            ->leftJoin('stocks', 'stocks.articulo_id', '=', 'articulos.id')
            ->select(
                'articulos.id', 'articulos.articulo', 'articulos.codigo',
                'articulos.presentacion', 'articulos.precioF', 'stocks.stock'
            )
            ->limit(20)
            ->get();

        return response()->json($articulos);
    }
}
