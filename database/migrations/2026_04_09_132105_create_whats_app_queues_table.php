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
        Schema::create('whats_app_queues', function (Blueprint $table) {
           $table->id();
            $table->string('telefono', 20);
            $table->text('mensaje');
            $table->boolean('enviado')->default(false);
            $table->timestamp('enviado_en')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            
            // Agregar índices
            $table->index('enviado');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_queues');
    }
};
