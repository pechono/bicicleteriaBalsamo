<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoCatalogoItem extends Model
{
    protected $table = 'pedido_catalogo_items';
    protected $guarded = [];
    protected $casts = ['recibido' => 'boolean'];

    public function orden()
    {
        return $this->belongsTo(PedidoCatalogoOrden::class, 'pedido_catalogo_id');
    }
}
