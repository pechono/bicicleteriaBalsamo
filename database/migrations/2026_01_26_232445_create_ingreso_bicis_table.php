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
        Schema::create('ingreso_bicis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bici_id');
            $table->foreignId('articulo_id');
            $table->string('nro_ingreso');
            $table->enum('estado', ['Pendiente', 'Terminado', 'Entregado'])->default('Pendiente');

            $table->text('detalles')->nullable();
            $table->timestamps();
        });

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingreso_bicis');
    }
};
