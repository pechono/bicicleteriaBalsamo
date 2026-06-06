<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NroIngreso extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Al crear un NroIngreso, genera automáticamente un token_mobile único.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->token_mobile)) {
                $model->token_mobile = Str::random(32);
            }
        });
    }

    // Relaciones
    public function ingresoBicis()
    {
        return $this->hasMany(IngresoBici::class, 'nro_ingreso');
    }

    public function egresosBici()
    {
        return $this->hasManyThrough(
            EgresoBici::class,
            IngresoBici::class,
            'nro_ingreso',   // FK en ingreso_bicis
            'ingreso_bici_id' // FK en egreso_bicis
        );
    }
}
