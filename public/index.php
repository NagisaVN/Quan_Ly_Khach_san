<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

// Fix routing when app runs in a subdirectory (localhost/.../public)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptName !== '' && $scriptName !== '/') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    if (str_starts_with($uri, $scriptName)) {
        $path = substr($uri, strlen($scriptName)) ?: '/';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $_SERVER['REQUEST_URI'] = $query ? $path.'?'.$query : $path;
    }
}

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());