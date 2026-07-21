<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpieza única de egreso_bicis (datos de prueba).
 *
 * Los egreso_bicis viejos guardaban en `ingreso_bici_id` un `bicis.id`
 * (convención vieja), lo que obligaba a leer con un join OR (id o bici_id).
 * Como son datos de prueba y descartables, se borran para dejar UNA sola
 * convención: a partir de ahora `ingreso_bici_id` = `ingreso_bicis.id`
 * (lo escribe EgresoTerminar) y las lecturas usan solo ese join.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('egreso_bicis')->delete();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Datos de prueba: no se restauran.
    }
};
