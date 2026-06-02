<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MecanicoItem extends Model
{
    protected $fillable = [
        'mecanico_id',
        'descripcion',
        'monto',
        'nro_egreso_id',
        'pagado',
        'liquidado_en',
    ];

    protected $casts = [
        'pagado'       => 'boolean',
        'liquidado_en' => 'datetime',
        'monto'        => 'decimal:2',
    ];

    public function mecanico()
    {
        return $this->belongsTo(Mecanico::class);
    }
}
