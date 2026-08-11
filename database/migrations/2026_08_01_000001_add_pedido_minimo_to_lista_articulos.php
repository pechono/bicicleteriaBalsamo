<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pedido mínimo del proveedor (columna "vtaminima" de Dal Santo).
     * Solo informativo: se muestra en Pedido a Proveedores. null = sin dato.
     */
    public function up(): void
    {
        Schema::table('lista_articulos', function (Blueprint $table) {
            $table->unsignedInteger('pedido_minimo')->nullable()->after('publico_usd');
        });
    }

    public function down(): void
    {
        Schema::table('lista_articulos', function (Blueprint $table) {
            $table->dropColumn('pedido_minimo');
        });
    }
};
