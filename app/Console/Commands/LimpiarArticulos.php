<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpia la tabla `articulos`.
 *
 *  php artisan catalogo:limpiar            -> SIMULACRO modo seguro (no borra, solo informa)
 *  php artisan catalogo:limpiar --force    -> modo seguro: borra solo artículos SIN ventas ni taller
 *  php artisan catalogo:limpiar --todo            -> SIMULACRO reset total
 *  php artisan catalogo:limpiar --todo --force    -> RESET TOTAL (borra TODO, incluso ventas y taller)
 */
class LimpiarArticulos extends Command
{
    protected $signature = 'catalogo:limpiar {--todo : Reset total (borra tambien ventas y taller)} {--force : Ejecuta de verdad (sin esto es solo simulacro)}';
    protected $description = 'Limpia la tabla articulos. Por defecto borra solo los que nunca se vendieron ni pasaron por taller.';

    /** Tablas hijas con articulo_id (inventario / referencias). */
    private array $tablasHijas = [
        'stocks', 'grupos_articulos', 'historias_precios', 'sueltos',
        'pedidos', 'pedido_cars', 'ofertas', 'cars', 'venta_mayorista_items',
    ];

    /** Tablas de historial con articulo_id (ventas y taller). */
    private array $tablasHistorial = ['ventas', 'ingreso_bicis', 'egreso_bicis'];

    public function handle(): int
    {
        $todo  = (bool) $this->option('todo');
        $force = (bool) $this->option('force');

        $totalArticulos = DB::table('articulos')->count();

        if ($todo) {
            $aBorrar = $totalArticulos;
            $this->warn("RESET TOTAL: se borrarian TODOS los {$totalArticulos} articulos + su inventario + ventas + taller.");
        } else {
            // Artículos que NO tienen ventas ni pasaron por taller.
            $idsConservar = $this->idsConHistorial();
            $aBorrar = DB::table('articulos')->whereNotIn('id', $idsConservar ?: [0])->count();
            $conservar = count($idsConservar);
            $this->info("Modo seguro:");
            $this->line("  - Articulos totales:        {$totalArticulos}");
            $this->line("  - Se CONSERVAN (con ventas/taller): {$conservar}");
            $this->line("  - Se BORRARIAN:             {$aBorrar}");
        }

        if (!$force) {
            $this->newLine();
            $this->warn('SIMULACRO: no se borro nada. Para ejecutar de verdad agrega --force');
            $this->line('  Modo seguro:  php artisan catalogo:limpiar --force');
            $this->line('  Reset total:  php artisan catalogo:limpiar --todo --force');
            return self::SUCCESS;
        }

        if ($todo) {
            $this->resetTotal(); // maneja su propia limpieza (ALTER hace commit implicito en MySQL)
        } else {
            DB::transaction(fn () => $this->borradoSeguro());
        }

        $this->newLine();
        $this->info('Listo. Articulos restantes: ' . DB::table('articulos')->count());
        return self::SUCCESS;
    }

    private function idsConHistorial(): array
    {
        $ids = [];
        foreach ($this->tablasHistorial as $t) {
            if (Schema::hasTable($t)) {
                $ids = array_merge($ids, DB::table($t)->whereNotNull('articulo_id')->pluck('articulo_id')->all());
            }
        }
        return array_values(array_unique($ids));
    }

    private function borradoSeguro(): void
    {
        $ids = DB::table('articulos')->whereNotIn('id', $this->idsConHistorial() ?: [0])->pluck('id')->all();
        if (empty($ids)) {
            return;
        }

        foreach (array_chunk($ids, 1000) as $chunk) {
            foreach ($this->tablasHijas as $t) {
                if (Schema::hasTable($t)) {
                    DB::table($t)->whereIn('articulo_id', $chunk)->delete();
                }
            }
            // El catalogo vuelve a marcar esos como "pendientes".
            if (Schema::hasTable('lista_articulos')) {
                DB::table('lista_articulos')->whereIn('articulo_id', $chunk)->update(['articulo_id' => null]);
            }
            DB::table('articulos')->whereIn('id', $chunk)->delete();
        }
    }

    private function resetTotal(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach (array_merge($this->tablasHistorial, $this->tablasHijas, ['articulos']) as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->delete();
            }
        }
        if (Schema::hasTable('lista_articulos')) {
            DB::table('lista_articulos')->update(['articulo_id' => null]);
        }
        // Reinicia el contador de IDs.
        DB::statement('ALTER TABLE articulos AUTO_INCREMENT = 1');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
