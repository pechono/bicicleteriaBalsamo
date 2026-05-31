<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// En producción (public_html) el proyecto está en ../bicicleteriaBalsamo/
// En local el proyecto está en ../
$appPath = file_exists(__DIR__.'/../bicicleteriaBalsamo/vendor/autoload.php')
    ? __DIR__.'/../bicicleteriaBalsamo'
    : __DIR__.'/..';

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

$app = require_once $appPath.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
