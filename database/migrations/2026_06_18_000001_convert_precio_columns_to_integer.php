<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columnas de PRECIO/total del sistema. Estaban en double(8,2) (máx 999.999,99),
     * insuficiente para artículos/ventas de precio alto (ej. ruedas de carbono > $1.000.000).
     *
     * En Argentina hoy no se usan centavos → se pasan a BIGINT (precios enteros, sin techo
     * práctico). Los valores decimales existentes se redondean. NO se tocan cantidades ni
     * porcentajes (esos pueden ser fraccionarios).
     *
     * Se usa SQL crudo porque doctrine/dbal no maneja bien estos cambios de tipo en MySQL.
     */
    private array $columnas = [
        'articulos'            => ['precioI', 'precioF'],
        'art_cuenta_corrientes' => ['precioI', 'precioF'],
        'cierre_cajas'         => ['efectivo', 'debito', 'tarjeta', 'transferencia', 'cuentaCorriente'],
        'cuenta_corrientes'    => ['deuda', 'entrega'],
        'egreso_bicis'         => ['precio_inicial', 'precio_final'],
        'historias_precios'    => ['precioIcial', 'precioFinal'],
        'ofertas'              => ['precio'],
        'oferta_art'           => ['precioO'],
        'oferta_articulos'     => ['precioI', 'precioF', 'precioO'],
        'operacions'           => ['venta'],
        'op_cuenta_corrientes' => ['total'],
        'ventas'               => ['precioI', 'precioF'],
    ];

    public function up(): void
    {
        $this->aplicar('BIGINT');
    }

    public function down(): void
    {
        $this->aplicar('DOUBLE(8,2)');
    }

    /** Modifica cada columna al tipo indicado, conservando NULL/NOT NULL y DEFAULT. */
    private function aplicar(string $tipo): void
    {
        $db = DB::getDatabaseName();

        foreach ($this->columnas as $tabla => $cols) {
            foreach ($cols as $col) {
                if (!Schema::hasColumn($tabla, $col)) {
                    continue;
                }

                $meta = DB::selectOne(
                    'SELECT IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$db, $tabla, $col]
                );

                $nullClause = ($meta && $meta->IS_NULLABLE === 'YES') ? 'NULL' : 'NOT NULL';

                // COLUMN_DEFAULT puede venir como NULL real o como el string "NULL" (según MySQL).
                $default = $meta->COLUMN_DEFAULT ?? null;
                $tieneDefault = $default !== null && strtoupper(trim($default)) !== 'NULL';
                $defaultClause = $tieneDefault
                    ? ' DEFAULT ' . (is_numeric($default) ? $default : "'" . $default . "'")
                    : '';

                DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$col}` {$tipo} {$nullClause}{$defaultClause}");
            }
        }
    }
};
