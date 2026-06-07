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
        Schema::table('proveedors', function (Blueprint $table) {
            $table->boolean('iva_incluido')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('proveedors', function (Blueprint $table) {
            $table->dropColumn('iva_incluido');
        });
    }
};
