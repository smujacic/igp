<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch($method, $path) {
        foreach ($this->routes as $route) {
            $pattern = '/^' . str_replace('/', '\/', $route['path']) . '$/';
            if ($method == $route['method'] && preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match from the matches array
                return call_user_func_array($route['handler'], $matches);
            }
        }
        http_response_code(404);
        echo "Not Found";
    }
    
}