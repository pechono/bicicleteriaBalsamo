<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * DNI opcional: la columna pasa a aceptar NULL (clientes que no quieren darlo).
     * Se guarda NULL cuando viene vacío para no chocar con el índice unique.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE clientes MODIFY dni VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE clientes MODIFY dni VARCHAR(255) NOT NULL');
    }
};
