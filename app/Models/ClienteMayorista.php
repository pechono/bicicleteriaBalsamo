<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClienteMayorista extends Model
{
    protected $table = 'clientes_mayoristas';
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $cliente) {
            if (empty($cliente->token)) {
                $cliente->token = Str::random(40);
            }
        });
    }

    /** URL pública del portal del cliente. */
    public function portalUrl(): string
    {
        return url('/portal/' . $this->token);
    }

    public function ventasMayoristas()
    {
        return $this->hasMany(VentaMayorista::class, 'cliente_mayorista_id');
    }

    public function cuentaCorriente()
    {
        return $this->hasMany(CuentaCorrienteMayorista::class, 'cliente_mayorista_id');
    }

    public function saldoPendiente(): float
    {
        return (float) $this->cuentaCorriente()
            ->selectRaw("SUM(CASE WHEN tipo = 'venta' THEN monto ELSE -monto END) as saldo")
            ->value('saldo') ?? 0;
    }
}
