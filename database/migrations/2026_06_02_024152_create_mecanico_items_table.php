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
        Schema::create('mecanico_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mecanico_id')->constrained('mecanicos');
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->unsignedBigInteger('nro_egreso_id')->nullable();
            $table->boolean('pagado')->default(false);
            $table->timestamp('liquidado_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mecanico_items');
    }
};
