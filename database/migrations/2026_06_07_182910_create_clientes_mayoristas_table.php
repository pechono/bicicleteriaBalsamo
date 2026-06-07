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
        Schema::create('clientes_mayoristas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('cuit')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('porcentaje_extra', 5, 2)->default(0); // % adicional sobre precio mayorista
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_mayoristas');
    }
};
