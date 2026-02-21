<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingreso_bicis', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('ingreso_bicis', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado', 'entregado'])
                  ->default('pendiente')
                  ->after('nro_ingreso'); // o donde estaba antes
        });
    }
};
