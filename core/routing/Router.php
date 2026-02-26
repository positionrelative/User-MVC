<?php

namespace App\Core\Routing;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $path, callable|array $handler): self
    {
        $method = strtoupper($method);
        $this->routes[$method][] = [
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];

        return $this;
    }

    public function get(string $path, callable|array $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): self
    {
        return $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): self
    {
        return $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): self
    {
        return $this->add('DELETE', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?? '/');
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $params = $this->matchRoute($route['path'], $path);
            if ($params === null) {
                continue;
            }

            $this->invokeHandler($route['handler'], $params);
            return;
        }

        $this->jsonError('Route not found', 404);
    }

    protected function invokeHandler(callable|array $handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler(...$params);
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->jsonError('Controller action not found', 500);
        }

        $controller->$action(...$params);
    }

    protected function matchRoute(string $routePath, string $requestPath): ?array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if ($routePath === '/' && $requestPath === '/') {
            return [];
        }

        if (count($routeParts) !== count($requestParts)) {
            return null;
        }

        $params = [];

        foreach ($routeParts as $index => $routePart) {
            $requestPart = $requestParts[$index] ?? '';

            if (preg_match('/^\{[a-zA-Z_][a-zA-Z0-9_]*\}$/', $routePart)) {
                $params[] = ctype_digit($requestPart) ? (int) $requestPart : $requestPart;
                continue;
            }

            if ($routePart !== $requestPart) {
                return null;
            }
        }

        return $params;
    }

    protected function normalizePath(string $path): string
    {
        $normalized = '/' . trim($path, '/');
        return $normalized === '//' ? '/' : $normalized;
    }

    protected function jsonError(string $message, int $status): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}