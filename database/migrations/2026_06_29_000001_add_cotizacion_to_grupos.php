<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Cotización del dólar usada en la última importación de este grupo (listas en USD).
            $table->decimal('cotizacion', 12, 2)->nullable()->after('porsentaje');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('cotizacion');
        });
    }
};
