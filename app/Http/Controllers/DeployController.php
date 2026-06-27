<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{
    /**
     * Hook de deploy por HTTP (lo llama la GitHub Action por HTTPS, evitando el SSH
     * que Hostinger bloquea). Protegido por token secreto (config deploy.token / .env DEPLOY_TOKEN).
     */
    public function deploy(string $token)
    {
        $secret = (string) config('deploy.token');
        abort_unless($secret !== '' && hash_equals($secret, $token), 403, 'Token inválido');

        if (!function_exists('exec')) {
            return response("ERROR: exec() está deshabilitado en el hosting; el hook no puede correr.\n", 500)
                ->header('Content-Type', 'text/plain');
        }

        @set_time_limit(300);
        $root = base_path();
        $publicDest = config('deploy.public_path') ?: dirname($root) . '/public_html';

        $log = [];
        $run = function (string $cmd) use ($root, &$log): int {
            $out = []; $code = 0;
            exec('cd ' . escapeshellarg($root) . ' && ' . $cmd . ' 2>&1', $out, $code);
            $log[] = "$ {$cmd}\n" . implode("\n", $out) . "\n[exit {$code}]";
            return $code;
        };

        // 1) Traer el último código
        $run('git fetch origin main');
        $run('git reset --hard origin/main');

        // 2) Dependencias PHP (probamos varias formas de invocar composer)
        $composerOk = false;
        foreach (['composer', '/usr/local/bin/composer', 'php composer.phar', 'php ~/composer.phar'] as $c) {
            if ($run($c . ' install --no-dev --optimize-autoloader --no-interaction') === 0) {
                $composerOk = true;
                break;
            }
        }
        if (!$composerOk) {
            $log[] = '⚠ composer install no se pudo ejecutar (revisar PATH de composer en el hosting).';
        }

        // 3) Migraciones + storage link (en proceso, no necesitan exec)
        try { Artisan::call('migrate', ['--force' => true]); $log[] = "migrate:\n" . Artisan::output(); }
        catch (\Throwable $e) { $log[] = 'migrate ERROR: ' . $e->getMessage(); }
        try { Artisan::call('storage:link'); $log[] = "storage:link:\n" . Artisan::output(); }
        catch (\Throwable $e) { $log[] = 'storage:link: ' . $e->getMessage(); }

        // 4) Copiar public/ al docroot
        $run('cp -rf ' . escapeshellarg($root . '/public') . '/. ' . escapeshellarg($publicDest) . '/');

        // 5) Limpiar cachés
        try { Artisan::call('optimize:clear'); $log[] = "optimize:clear:\n" . Artisan::output(); }
        catch (\Throwable $e) { $log[] = 'optimize:clear ERROR: ' . $e->getMessage(); }

        return response("DEPLOY HOOK OK\n\n" . implode("\n\n", $log) . "\n", 200)
            ->header('Content-Type', 'text/plain');
    }
}
