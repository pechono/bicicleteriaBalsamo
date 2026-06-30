<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de listas de proveedor. Es la "lista cruda" importada: NO ocupa
     * lugar en `articulos`. Desde acá se "pasa a artículos" lo que se va a tener
     * con stock (eso recién crea el Articulo real).
     */
    public function up(): void
    {
        Schema::create('lista_articulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proveedor_id')->index();
            $table->string('codigo', 50)->index();
            $table->string('articulo');
            $table->unsignedBigInteger('precio_costo')->default(0);   // ARS
            $table->unsignedBigInteger('precio_publico')->default(0); // ARS
            $table->string('moneda', 5)->default('ARS');              // ARS | USD
            $table->decimal('costo_usd', 12, 2)->nullable();          // valor original si moneda=USD
            $table->decimal('publico_usd', 12, 2)->nullable();
            $table->decimal('cotizacion', 12, 2)->nullable();         // cotización usada al importar
            // Articulo real creado al "pasar a artículos" (null = todavía no se pasó).
            $table->unsignedBigInteger('articulo_id')->nullable()->index();
            $table->timestamps();

            // Dedup por proveedor + código de producto (permite upsert).
            $table->unique(['proveedor_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_articulos');
    }
};
