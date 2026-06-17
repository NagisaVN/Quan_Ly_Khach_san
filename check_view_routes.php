<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$router = app('router');
$routes = $router->getRoutes();

$directory = new RecursiveDirectoryIterator(__DIR__.'/resources/views');
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$missingRoutes = [];

foreach ($regex as $file) {
    $filePath = $file[0];
    $content = file_get_contents($filePath);
    
    if (preg_match_all("/route\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
        foreach ($matches[1] as $routeName) {
            if (!$routes->hasNamedRoute($routeName)) {
                $missingRoutes[] = "$routeName in $filePath";
            }
        }
    }
}

if (empty($missingRoutes)) {
    echo "All view route calls are valid.\n";
} else {
    echo "Missing routes found in views:\n";
    echo implode("\n", array_unique($missingRoutes)) . "\n";
}
