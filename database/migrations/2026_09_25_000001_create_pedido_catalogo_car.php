<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carrito (borrador) del pedido desde catálogo. Ahora en tabla (antes en sesión).
     * Guarda el proveedor de cada ítem, así puede haber varios proveedores a la vez;
     * al filtrar por uno se ve solo lo suyo. Se borra por proveedor al "Borrar".
     */
    public function up(): void
    {
        Schema::create('pedido_catalogo_car', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proveedor_id')->index();
            $table->unsignedBigInteger('lista_articulo_id')->index();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
            $table->unique('lista_articulo_id'); // un renglón por artículo de catálogo
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_catalogo_car');
    }
};
