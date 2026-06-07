<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteMayorista extends Model
{
    protected $table = 'clientes_mayoristas';
    protected $guarded = [];

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
