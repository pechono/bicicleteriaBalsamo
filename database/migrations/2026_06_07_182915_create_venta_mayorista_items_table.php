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
        Schema::create('venta_mayorista_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_mayorista_id')->constrained('ventas_mayoristas')->cascadeOnDelete();
            $table->foreignId('articulo_id')->constrained('articulos');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_costo', 12, 2);      // precioI al momento de la venta
            $table->decimal('precio_mayorista', 12, 2);  // precio calculado (con IVA + %)
            $table->decimal('porcentaje_aplicado', 5, 2); // % usado para calcular
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_mayorista_items');
    }
};
