<?php

class Router
{
    private $routes = [];
    private $basePath;
    private $auth;

    public function __construct($basePath = '/', $auth = null)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->auth = $auth;
    }

    public function get($path, $callback, $authRequired = false)
    {
        $this->addRoute('GET', $path, $callback, $authRequired);
    }

    public function post($path, $callback, $authRequired = false)
    {
        $this->addRoute('POST', $path, $callback, $authRequired);
    }

    public function any($path, $callback, $authRequired = false)
    {
        $this->addRoute('ANY', $path, $callback, $authRequired);
    }

    private function addRoute($method, $path, $callback, $authRequired)
    {
        $pattern = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'path' => $path,
            'authRequired' => $authRequired, // Flag to indicate if authentication is required
        ];
    }

    public function serveStatic($uri, $filePath)
    {
        if (preg_match('/^\/assets\//', $uri)) {
            $assetPath = $filePath . str_replace('/assets', '/public', $uri);

            if (file_exists($assetPath)) {
                $extension = pathinfo($assetPath, PATHINFO_EXTENSION);

                $mimeTypes = [
                    'css'  => 'text/css',
                    'js'   => 'application/javascript',
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',
                    'gif'  => 'image/gif',
                    'svg'  => 'image/svg+xml',
                    'ico'  => 'image/x-icon',
                    'woff' => 'font/woff',
                    'woff2'=> 'font/woff2',
                    'ttf'  => 'font/ttf',
                    'eot'  => 'application/vnd.ms-fontobject',
                    'html' => 'text/html',
                    'json' => 'application/json',
                ];

                $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
                header("Content-Type: $mimeType");

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

        if (strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }

        $this->serveStatic($requestUri, __DIR__);

        parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $queryParams);

        foreach ($this->routes as $route) {
            if (
                ($route['method'] === 'ANY' || $route['method'] === $method) &&
                preg_match($route['pattern'], $requestUri, $matches)
            ) {
                if ($route['authRequired'] && !$this->auth->isLoggedIn()) {
                    $this->handleAuthRedirect();
                    return;
                }

                $arguments = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                $arguments[] = $queryParams;

                call_user_func_array($route['callback'], $arguments);
                return;
            }
        }

        http_response_code(404);
        echo "Page not found!";
        error_log("Debug: No matching route for URI: $requestUri");
    }

    private function handleAuthRedirect()
    {
        $redirectPath = urlencode($_SERVER['REQUEST_URI']);
        header("Location: /login?redirect_path=$redirectPath");
        exit;
    }
}
