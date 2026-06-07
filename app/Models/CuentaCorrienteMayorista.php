<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCorrienteMayorista extends Model
{
    protected $table = 'cuenta_corriente_mayorista';
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(ClienteMayorista::class, 'cliente_mayorista_id');
    }

    public function venta()
    {
        return $this->belongsTo(VentaMayorista::class, 'venta_mayorista_id');
    }
}
