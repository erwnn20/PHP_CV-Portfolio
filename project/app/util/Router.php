<?php

require_once 'app/controllers/HomeController.php';
require_once 'app/controllers/ProfileController.php';
require_once 'app/controllers/CvController.php';
require_once 'app/controllers/ProjectController.php';
require_once 'app/controllers/AdminController.php';
require_once 'app/controllers/ErrorController.php';

class Router
{
    private array $routes = array();
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addRoute($method, $path, $controller, $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function handleRequest($method, $path)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                return $controller->{$route['action']}();
            }
        }
        http_response_code(404);
        return (new ErrorController)->render404();
    }
}