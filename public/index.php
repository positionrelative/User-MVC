<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $class = ltrim($class, '\\');

    $map = [
        'App\\Core\\'        => dirname(__DIR__) . '/core/',
        'App\\Controllers\\' => dirname(__DIR__) . '/app/controllers/',
        'App\\Models\\'      => dirname(__DIR__) . '/app/models/',
        'App\\Resources\\'   => dirname(__DIR__) . '/app/resources/',
        'App\\Services\\'    => dirname(__DIR__) . '/app/services/',
    ];

    foreach ($map as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
        $file = rtrim($baseDir, "/\\") . DIRECTORY_SEPARATOR . $relativePath;

        if (is_file($file)) {
            require_once $file;
        }
        return;
    }
});

use App\Core\Routing\Router;

$router = new Router();
require dirname(__DIR__) . '/router/api.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

