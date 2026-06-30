<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaArticulo extends Model
{
    protected $table = 'lista_articulos';
    protected $guarded = [];

    protected $casts = [
        'precio_costo'   => 'integer',
        'precio_publico' => 'integer',
        'costo_usd'      => 'decimal:2',
        'publico_usd'    => 'decimal:2',
        'cotizacion'     => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupos::class, 'grupo_id');
    }
}
