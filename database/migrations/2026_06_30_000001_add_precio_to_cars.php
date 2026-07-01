<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precio por línea del carrito. NULL = usa el precio del artículo (comportamiento normal).
     * Se usa para la mano de obra, cuyo precio se define en cada venta.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedBigInteger('precio')->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('precio');
        });
    }
};
