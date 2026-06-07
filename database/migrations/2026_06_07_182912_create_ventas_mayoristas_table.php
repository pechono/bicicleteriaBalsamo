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
        Schema::create('ventas_mayoristas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_mayorista_id')->constrained('clientes_mayoristas');
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('tipo_pago', ['efectivo', 'transferencia', 'cuenta_corriente'])->default('efectivo');
            $table->boolean('pagado')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_mayoristas');
    }
};
