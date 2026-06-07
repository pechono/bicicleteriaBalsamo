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
        Schema::create('cuenta_corriente_mayorista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_mayorista_id')->constrained('clientes_mayoristas');
            $table->enum('tipo', ['venta', 'pago']); // venta = deuda, pago = abono
            $table->decimal('monto', 12, 2);
            $table->foreignId('venta_mayorista_id')->nullable()->constrained('ventas_mayoristas');
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_corriente_mayorista');
    }
};
