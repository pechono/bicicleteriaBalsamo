<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EgresoBici extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ingresoBici()
    {
        return $this->belongsTo(IngresoBici::class, 'ingreso_bici_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
