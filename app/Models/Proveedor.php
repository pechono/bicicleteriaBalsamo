<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = ['iva_incluido' => 'boolean', 'activo' => 'boolean'];

    public function grupos()
    {
        return $this->hasMany(Grupos::class, 'proveedor_id');
    }
}
