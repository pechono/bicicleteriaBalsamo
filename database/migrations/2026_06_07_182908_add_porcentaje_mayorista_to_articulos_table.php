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
        Schema::table('articulos', function (Blueprint $table) {
            // Override de % por artículo particular (null = usa el % del grupo)
            $table->decimal('porcentaje_mayorista', 5, 2)->nullable()->after('precioF');
        });
    }

    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn('porcentaje_mayorista');
        });
    }
};
