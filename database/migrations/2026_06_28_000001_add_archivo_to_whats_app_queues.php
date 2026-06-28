<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whats_app_queues', function (Blueprint $table) {
            $table->string('archivo')->nullable()->after('mensaje');        // ruta del PDF en storage/app
            $table->string('nombre_archivo')->nullable()->after('archivo'); // nombre con el que llega al WhatsApp
        });
    }

    public function down(): void
    {
        Schema::table('whats_app_queues', function (Blueprint $table) {
            $table->dropColumn(['archivo', 'nombre_archivo']);
        });
    }
};
