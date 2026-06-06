<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoBici extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bici()
    {
        return $this->belongsTo(Bici::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function nroIngreso()
    {
        return $this->belongsTo(NroIngreso::class, 'nro_ingreso');
    }

    public function egresos()
    {
        return $this->hasMany(EgresoBici::class, 'ingreso_bici_id');
    }
}
