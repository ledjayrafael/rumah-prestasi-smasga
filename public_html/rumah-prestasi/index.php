<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// From public_html/rumah-prestasi → repo root → core-rumah-prestasi
$core = __DIR__.'/../../core-rumah-prestasi';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $core.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $core.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $core.'/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
