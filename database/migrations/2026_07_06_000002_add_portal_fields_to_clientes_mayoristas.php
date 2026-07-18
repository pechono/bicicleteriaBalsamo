<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Portal del cliente mayorista:
 * - token: identifica al cliente en el link público /portal/{token} (sin login).
 * - cuenta_corriente_habilitada: si el cliente puede ver su cuenta corriente en el portal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes_mayoristas', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes_mayoristas', 'token')) {
                $table->string('token', 64)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('clientes_mayoristas', 'cuenta_corriente_habilitada')) {
                $table->boolean('cuenta_corriente_habilitada')->default(false)->after('activo');
            }
        });

        // Backfill de tokens para los clientes ya existentes.
        foreach (DB::table('clientes_mayoristas')->whereNull('token')->pluck('id') as $id) {
            DB::table('clientes_mayoristas')->where('id', $id)->update(['token' => Str::random(40)]);
        }
    }

    public function down(): void
    {
        Schema::table('clientes_mayoristas', function (Blueprint $table) {
            if (Schema::hasColumn('clientes_mayoristas', 'cuenta_corriente_habilitada')) {
                $table->dropColumn('cuenta_corriente_habilitada');
            }
            if (Schema::hasColumn('clientes_mayoristas', 'token')) {
                $table->dropUnique(['token']);
                $table->dropColumn('token');
            }
        });
    }
};
