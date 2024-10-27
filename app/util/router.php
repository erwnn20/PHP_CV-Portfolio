<?php
require_once 'errors/errorHandler.php';

class Router
{
    private array $routes = array();
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function addRoute($method, $path, $filePath): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'file' => $filePath
        ];
    }

    public function handleRequest(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg)$/', $requestUri)) {
            return;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                if (file_exists($route['file'])) {
                    require $route['file'];
                } else {
                    throw new Exception("Fichier de route introuvable", 500);
                }
                return;
            }
        }

        ErrorHandler::render404();
    }
}
