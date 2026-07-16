<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Convierte la tabla `sueltos` en la tabla de vínculo caja→suelto:
 * - articulo_id (ya existía) = el artículo SUELTO (unidad).
 * - caja_id = el artículo CAJA cerrada del que proviene.
 * - cantidad = unidades por caja (cuántas unidades entran en una caja).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sueltos', function (Blueprint $table) {
            if (!Schema::hasColumn('sueltos', 'caja_id')) {
                $table->unsignedBigInteger('caja_id')->nullable()->after('articulo_id');
            }
            if (!Schema::hasColumn('sueltos', 'cantidad')) {
                $table->integer('cantidad')->nullable()->after('caja_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sueltos', function (Blueprint $table) {
            if (Schema::hasColumn('sueltos', 'cantidad')) {
                $table->dropColumn('cantidad');
            }
            if (Schema::hasColumn('sueltos', 'caja_id')) {
                $table->dropColumn('caja_id');
            }
        });
    }
};
