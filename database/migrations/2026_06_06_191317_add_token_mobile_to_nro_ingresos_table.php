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
        Schema::table('nro_ingresos', function (Blueprint $table) {
            $table->string('token_mobile', 64)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('nro_ingresos', function (Blueprint $table) {
            $table->dropColumn('token_mobile');
        });
    }
};
