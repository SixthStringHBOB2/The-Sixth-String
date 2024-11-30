<?php

require_once 'Router.php';

$router = new Router();

$router->get('/', function () {
    include 'views/home.php';
});

$router->get('/post/{id}', function ($id, $queryParams) {
    echo "Post ID: $id<br>";
    echo "Query Params: " . json_encode($queryParams);
});

$router->get('/search', function ($queryParams) {
    $searchQuery = isset($queryParams['q']) ? $queryParams['q'] : 'No query';
    echo "Search query: $searchQuery";
});

$router->serveStatic($_SERVER['REQUEST_URI'], __DIR__);

$router->handleRequest();
