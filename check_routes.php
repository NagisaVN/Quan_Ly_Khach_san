<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$routes = Illuminate\Support\Facades\Route::getRoutes();

$missing = [];
foreach ($routes as $route) {
    $action = $route->getAction();
    if (isset($action['controller'])) {
        list($class, $method) = explode('@', $action['controller']);
        if (!class_exists($class)) {
            $missing[] = "Class missing: $class";
        } elseif (!method_exists($class, $method)) {
            $missing[] = "Method missing: $class@$method";
        }
    }
}

if (empty($missing)) {
    echo "All controller methods exist.\n";
} else {
    echo "Missing components:\n";
    echo implode("\n", $missing) . "\n";
}
