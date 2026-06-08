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
        if (!Schema::hasColumn('proveedors', 'abreviatura')) {
            Schema::table('proveedors', function (Blueprint $table) {
                $table->string('abreviatura', 10)->nullable()->after('nombre');
            });
        }
    }

    public function down(): void
    {
        Schema::table('proveedors', function (Blueprint $table) {
            $table->dropColumn('abreviatura');
        });
    }
};
