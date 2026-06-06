<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bici extends Model
{
    use HasFactory;
    protected $fillable = [
        'cliente_id',
        'color',
        'tipo_id',
        'marca_id',
        'detalles',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function tipoBike()
    {
        return $this->belongsTo(TipoBike::class, 'tipo_id');
    }

    public function ingresoBicis()
    {
        return $this->hasMany(IngresoBici::class);
    }
}
