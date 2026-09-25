<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pedidos armados desde el catálogo (lista_articulos). NO tocan el stock al
     * crearse: el artículo se da de ingreso a stock cuando la mercadería LLEGA
     * (recepción, ítem por ítem). Es un sistema aparte del pedido normal (`pedidos`),
     * que sí exige que el artículo ya esté en stock.
     */
    public function up(): void
    {
        Schema::create('pedido_catalogo_ordenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero')->index();       // N° de pedido correlativo
            $table->unsignedBigInteger('proveedor_id')->index();
            $table->string('estado', 20)->default('pendiente'); // pendiente | parcial | recibido
            $table->boolean('enviado')->default(false);         // se avisó al proveedor
            $table->timestamps();
        });

        Schema::create('pedido_catalogo_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_catalogo_id')->index();
            $table->unsignedBigInteger('lista_articulo_id')->index();
            $table->unsignedInteger('cantidad')->default(1);
            $table->unsignedBigInteger('precio_costo')->default(0); // costo al momento del pedido
            $table->boolean('recibido')->default(false);
            $table->unsignedBigInteger('articulo_id')->nullable(); // artículo de stock (al recibir)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_catalogo_items');
        Schema::dropIfExists('pedido_catalogo_ordenes');
    }
};
