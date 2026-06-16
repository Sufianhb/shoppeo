<?php

declare(strict_types=1);

namespace core;

/**
 * Router simple pero robusto con soporte para:
 *  - Segmentos dinamicos: /producto/{id}
 *  - Métodos GET y POST
 *  - Respuesta 404 automatica
 */
class Router
{
    private array $routes = [];

    // Registro de rutas
    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'pattern' => $this->pathToRegex($path),
        ];
    }

    // Convertir ruta con {param} a regex
    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    // Despachar la peticion
    public function dispatch(string $method, string $uri): void
    {
        // Normalizar URI: quitar query string y trailing slash
        $uri = rtrim(parse_url($uri, PHP_URL_PATH) ?? '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extraer únicamente los parametros nombrados
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                $this->callController($route['controller'], $route['action'], $params);
                return;
            }
        }

        // Ninguna ruta coincidio → 404
        http_response_code(404);
        require APP_PATH . '/views/layouts/404.php';
    }

    // Instanciar controlador y ejecutar accion
    private function callController(string $controllerName, string $action, array $params): void
    {
        $class = 'controllers\\' . $controllerName;
        $file  = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Controlador no encontrado: $controllerName");
        }

        require_once $file;

        if (!class_exists($class)) {
            throw new \RuntimeException("Clase no encontrada: $class");
        }

        $controller = new $class();

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Método no encontrado: $class::$action");
        }

        $controller->$action($params);
    }
}
