<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Auto-run migrations once in background if database is configured
if (!file_exists($migratedLock = __DIR__.'/../storage/framework/.migrated')) {
    if (getenv('DATABASE_URL') || getenv('DB_DATABASE') || getenv('DB_HOST')) {
        @touch($migratedLock);
        @exec('php '.__DIR__.'/../artisan migrate --force > /dev/null 2>&1 &');
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
