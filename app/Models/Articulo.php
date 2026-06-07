<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;
    protected $fillable = [
        'articulo',
        'codigo',
        'categoria_id',
        'presentacion',
        'unidad_id',
        'descuento',
        'unidadVenta',
        'precioF',
        'precioI',
        'caducidad',
        'detalles',
        'suelto',
        'activo',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Calcula el precio mayorista dado un % y si el proveedor incluye IVA.
     * Fórmula: precioI (+ 21% si no incluye IVA) × (1 + porcentaje/100)
     */
    public function calcularPrecioMayorista(float $porcentaje, bool $ivaIncluido): float
    {
        $base = $this->precioI;
        if (!$ivaIncluido) {
            $base *= 1.21;
        }
        return round($base * (1 + $porcentaje / 100), 2);
    }
}
