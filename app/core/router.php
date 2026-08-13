<?php
declare(strict_types=1);

namespace app\core;

class router
{
    private array $routes = [];

    /**
     * Mendaftarkan rute baru
     */
    public function add(string $method, string $path, array|callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler
        ];
    }

    /**
     * Resolusi dan mengeksekusi rute
     */
    public function resolve(request $request): void
    {
        $currentMethod = strtoupper($request->getmethod());
        $currentPath   = $request->getpath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $currentMethod && $route['path'] === $currentPath) {
                $handler = $route['handler'];

                // Jika handler adalah Closure / Callback
                if (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }

                // Jika handler adalah [ControllerClass, 'method']
                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $method] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $method)) {
                            $controller->$method();
                            return;
                        }
                    }
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 Not Found";
    }
}