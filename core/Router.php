<?php

declare(strict_types=1);

final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:string}> */
    private array $routes = [];

    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        if ($basePath !== null) {
            $this->basePath = '/' . trim($basePath, '/');
            if ($this->basePath === '/') {
                $this->basePath = '';
            }
        } else {
            // Try config first, fallback to /pageon
            $cfg = config('app.base_path', '/pageon');
            $this->basePath = '/' . trim((string) $cfg, '/');
            if ($this->basePath === '/') {
                $this->basePath = '';
            }
        }
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        // Split path into segments, escape static parts, keep placeholders.
        $segments = explode('/', trim($path, '/'));
        $regexParts = [];

        foreach ($segments as $seg) {
            if ($seg === '') {
                continue;
            }
            if (preg_match('/^\{(\w+)\}$/', $seg, $m)) {
                $regexParts[] = '(?P<' . $m[1] . '>[^/]+)';
            } else {
                $regexParts[] = preg_quote($seg, '#');
            }
        }

        $pattern = '#^/' . implode('/', $regexParts) . '$#';
        // Root path special case
        if ($path === '/') {
            $pattern = '#^/$#';
        }

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // Strip base path
        if ($this->basePath !== '' && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
            if ($uri === '' || $uri === false) {
                $uri = '/';
            }
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        // Support method spoofing via _method (POST only)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper(trim((string) $_POST['_method']));
            if (in_array($spoofed, ['PUT', 'DELETE', 'PATCH'], true)) {
                $method = $spoofed;
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // Validate that id params are numeric when named "id"
                if (isset($params['id']) && !ctype_digit((string) $params['id'])) {
                    abort(404);
                }

                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        abort(404);
    }

    /**
     * @param array<string,string> $params
     */
    private function callHandler(string $handler, array $params): void
    {
        if (!str_contains($handler, '@')) {
            error_log("[Router] Invalid handler: {$handler}");
            abort(500, 'Route handler tidak valid.');
        }

        [$controllerName, $methodName] = explode('@', $handler, 2);

        // Validate names to prevent directory traversal
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $controllerName) || !preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $methodName)) {
            error_log("[Router] Invalid controller/method: {$handler}");
            abort(500);
        }

        $controllerFile = BASE_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            error_log("[Router] Controller file not found: {$controllerFile}");
            abort(500);
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            error_log("[Router] Controller class not found: {$controllerName}");
            abort(500);
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            error_log("[Router] Method not found: {$controllerName}@{$methodName}");
            abort(500);
        }

        try {
            call_user_func_array([$controller, $methodName], array_values($params));
        } catch (Throwable $e) {
            error_log("[Router] Handler exception {$handler}: " . $e->getMessage());

            if (config('app.debug', false) === true) {
                throw $e;
            }

            abort(500);
        }
    }
}
