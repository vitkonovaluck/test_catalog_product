<?php

namespace Core;

/**
 * Простий маршрутизатор
 */
class Router
{
    private $routes = [];

    /**
     * Додати GET маршрут
     */
    public function get($pattern, $callback)
    {
        $this->routes['GET'][$pattern] = $callback;
    }

    /**
     * Додати POST маршрут
     */
    public function post($pattern, $callback)
    {
        $this->routes['POST'][$pattern] = $callback;
    }

    /**
     * Обробити запит
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Видалити базовий шлях якщо є
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = $uri ?: '/';

        // Шукати точний збіг
        if (isset($this->routes[$method][$uri])) {
            return $this->executeCallback($this->routes[$method][$uri]);
        }

        // Шукати паттерни з параметрами
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $callback) {
                $params = $this->matchRoute($pattern, $uri);
                if ($params !== false) {
                    return $this->executeCallback($callback, $params);
                }
            }
        }

        // 404 - маршрут не знайдено
        http_response_code(404);
        echo "404 - Page not found";
    }

    /**
     * Перевірити чи збігається маршрут з паттерном
     */
    private function matchRoute($pattern, $uri)
    {
        // Перетворити паттерн в регулярний вираз
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Видалити повний збіг
            return $matches;
        }

        return false;
    }

    /**
     * Виконати callback
     */
    private function executeCallback($callback, $params = [])
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);

            $controllerClass = "App\\Controllers\\{$controller}";

            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();

                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                } else {
                    throw new \Exception("Method {$method} not found in {$controllerClass}");
                }
            } else {
                throw new \Exception("Controller {$controllerClass} not found");
            }
        }

        throw new \Exception("Invalid callback");
    }
}
?>