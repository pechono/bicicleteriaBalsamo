<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('stocks', function (Blueprint $table) {
        $table->string('codigo_proveedor')->nullable()->after('proveedor_id');

        // Opcional pero MUY recomendable:
        $table->unique(['proveedor_id', 'codigo_proveedor']);
    });
}

public function down()
{
    Schema::table('stocks', function (Blueprint $table) {
        $table->dropUnique(['proveedor_id', 'codigo_proveedor']);
        $table->dropColumn('codigo_proveedor');
    });
}
};
