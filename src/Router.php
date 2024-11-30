<?php

class Router
{
    private $routes = [];
    private $basePath;

    public function __construct($basePath = '/')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function any($path, $callback)
    {
        $this->addRoute('ANY', $path, $callback);
    }

    private function addRoute($method, $path, $callback)
    {
        // Convert path with parameters into regex pattern
        $pattern = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'path' => $path, // For debugging purposes
        ];
        error_log("Debug: Added route: $method $pattern");
    }

    public function serveStatic($uri, $filePath)
    {
        if (preg_match('/^\/assets\//', $uri)) {
            $assetPath = $filePath . '/public' . str_replace('/assets', '', $uri);

            if (file_exists($assetPath)) {
                header("Content-Type: " . mime_content_type($assetPath));
                readfile($assetPath);
                exit;
            } else {
                error_log("Debug: Asset not found: $uri");
                http_response_code(404);
                echo "Asset not found!";
                exit;
            }
        }
    }


    public function handleRequest()
    {
        $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        error_log("Debug: Received request for URI: $requestUri with method: $method");

        if (strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }

        error_log("Debug: Processed URI after base path adjustment: $requestUri");

        // Check for static assets (CSS, images, etc.)
        $this->serveStatic($requestUri, __DIR__);

        parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $queryParams);

        foreach ($this->routes as $route) {
            error_log("Debug: Checking route pattern: {$route['pattern']} against URI: $requestUri");

            if (
                ($route['method'] === 'ANY' || $route['method'] === $method) &&
                preg_match($route['pattern'], $requestUri, $matches)
            ) {
                error_log("Debug: Matched route: {$route['path']} with pattern {$route['pattern']}");
                $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $arguments = array_merge($matches, [$queryParams]);
                call_user_func_array($route['callback'], $arguments);
                return;
            }
        }

        http_response_code(404);
        echo "Page not found!";
        error_log("Debug: No matching route for URI: $requestUri");
    }
}
