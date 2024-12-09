<?php

require_once 'Router.php';

$router = new Router();

$router->get('/', function () {
    include 'views/home.php';
});

$router->get('/post/{id}', function ($id, $queryParams) {
    include 'views/home.php';
});

$router->get('/products', function () {
    include 'views/products.php';
});

$router->get('/search', function ($queryParams) {
    $searchQuery = isset($queryParams['q']) ? $queryParams['q'] : 'No query';
    echo "Search query: $searchQuery";
});

$router->get('/shoppingcart', function () {
    include 'views/shoppingcart.php';
});

$router->post('/shoppingcart', function () {
    include 'views/shoppingcart.php';
});


$router->get('/purchase', function () {
    include 'views/purchase.php';
});

$router->serveStatic($_SERVER['REQUEST_URI'], __DIR__);

$router->handleRequest();
