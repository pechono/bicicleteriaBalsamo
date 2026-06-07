<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaMayoristaItem extends Model
{
    protected $table = 'venta_mayorista_items';
    protected $guarded = [];

    public function venta()
    {
        return $this->belongsTo(VentaMayorista::class, 'venta_mayorista_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
