<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoCatalogoOrden extends Model
{
    protected $table = 'pedido_catalogo_ordenes';
    protected $guarded = [];
    protected $casts = ['enviado' => 'boolean'];

    public function items()
    {
        return $this->hasMany(PedidoCatalogoItem::class, 'pedido_catalogo_id');
    }
}
