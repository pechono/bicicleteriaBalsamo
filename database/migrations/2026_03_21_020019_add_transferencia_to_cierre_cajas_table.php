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
            Schema::table('cierre_cajas', function (Blueprint $table) {
             $table->float('transferencia', 10, 2)->nullable()->after('efectivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cierre_cajas', function (Blueprint $table) {
            $table->dropColumn('transferencia');
        });
    }
};
