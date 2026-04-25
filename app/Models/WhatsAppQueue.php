<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppQueue extends Model
{
    use HasFactory;
    protected $fillable = [
        'telefono',
        'mensaje',
        'enviado',
        'enviado_en',
        'error'
    ];
     protected $casts = [
        'enviado' => 'boolean',
        'enviado_en' => 'datetime'
    ];
}
