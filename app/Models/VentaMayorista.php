<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaMayorista extends Model
{
    protected $table = 'ventas_mayoristas';
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(ClienteMayorista::class, 'cliente_mayorista_id');
    }

    public function items()
    {
        return $this->hasMany(VentaMayoristaItem::class, 'venta_mayorista_id');
    }
}
