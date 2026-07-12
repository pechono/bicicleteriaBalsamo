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
        Schema::create('nro_egresos', function (Blueprint $table) {
    $table->id();

    $table->string('operacion')->nullable();
$table->decimal('monto', 15, 2)->nullable(); // 15 dígitos totales, 2 decimales
    $table->text('detalles')->nullable();
    $table->integer('mecanico_id');
    $table->timestamps();
});

    }

    /**
     * Reverse the migratio
     */
    public function down(): void
    {
        Schema::dropIfExists('nro_egresos');
    }
};
