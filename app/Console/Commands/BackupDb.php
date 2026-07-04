<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backup PHP-nativo de la base (sin mysqldump, porque Hostinger tiene exec() deshabilitado).
 * Genera un .sql.gz en storage/app/backups y conserva los últimos N días.
 *
 *   php artisan backup:db            -> backup + limpieza (conserva 14 días)
 *   php artisan backup:db --keep=30  -> conserva 30 días
 */
class BackupDb extends Command
{
    protected $signature = 'backup:db {--keep=14 : Días de backups a conservar}';
    protected $description = 'Copia de seguridad de la base de datos (.sql.gz), sin mysqldump.';

    public function handle(): int
    {
        @set_time_limit(0);

        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $archivo = $dir . '/backup_' . now()->format('Y-m-d_His') . '.sql.gz';
        $gz = gzopen($archivo, 'w6');
        if (!$gz) {
            $this->error('No se pudo crear el archivo de backup.');
            return self::FAILURE;
        }

        $pdo = DB::getPdo();
        gzwrite($gz, "-- Backup " . now()->toDateTimeString() . "\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        $tablas = array_map(fn ($r) => array_values((array) $r)[0], DB::select('SHOW TABLES'));

        foreach ($tablas as $t) {
            $create = (array) DB::select("SHOW CREATE TABLE `{$t}`")[0];
            $createSql = $create['Create Table'] ?? $create['Create View'] ?? null;
            if (!$createSql) {
                continue;
            }
            gzwrite($gz, "DROP TABLE IF EXISTS `{$t}`;\n{$createSql};\n\n");

            $columnas = null;
            $valores = [];
            foreach (DB::table($t)->cursor() as $fila) {
                $fila = (array) $fila;
                if ($columnas === null) {
                    $columnas = '`' . implode('`,`', array_keys($fila)) . '`';
                }
                $valores[] = '(' . implode(',', array_map(
                    fn ($v) => is_null($v) ? 'NULL' : $pdo->quote((string) $v),
                    array_values($fila)
                )) . ')';

                if (count($valores) >= 200) {
                    gzwrite($gz, "INSERT INTO `{$t}` ({$columnas}) VALUES\n" . implode(",\n", $valores) . ";\n");
                    $valores = [];
                }
            }
            if (!empty($valores)) {
                gzwrite($gz, "INSERT INTO `{$t}` ({$columnas}) VALUES\n" . implode(",\n", $valores) . ";\n");
            }
            gzwrite($gz, "\n");
        }

        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
        gzclose($gz);

        $mb = round(filesize($archivo) / 1048576, 2);
        $this->info("Backup creado: " . basename($archivo) . " ({$mb} MB)");

        // Limpieza: borrar backups más viejos que --keep días.
        $keep = (int) $this->option('keep');
        $limite = now()->subDays($keep)->timestamp;
        $borrados = 0;
        foreach (glob($dir . '/backup_*.sql.gz') as $f) {
            if (filemtime($f) < $limite) {
                @unlink($f);
                $borrados++;
            }
        }
        if ($borrados) {
            $this->line("Backups viejos eliminados: {$borrados}");
        }

        return self::SUCCESS;
    }
}
