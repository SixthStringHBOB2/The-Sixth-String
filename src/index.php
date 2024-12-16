<?php

require_once 'Router.php';
require_once 'services/authService.php';

$auth = new Auth();
$router = new Router('/', $auth);

$router->get('/', function () {
    include 'views/home.php';
});

$router->get('/account', function () use ($auth) {
    $userData = $auth->getLoggedInUserData();
    $userName = $auth->getLoggedInUserName();

    include 'views/account.php';
}, true);

$router->any('/logout', function () use ($auth) {
    $auth->logout();
}, true);

$router->get('/orders', function () {
    include 'views/orders.php';
}, true);

$router->get('/order/{id}', function ($id) {
    $_GET['id'] = $id;
    include 'views/order_details.php';
}, true);


$router->any('/login', function () {
    include 'views/login.php';
});

$router->any('/register', function () {
    include 'views/register.php';
});


$router->get('/post/{id}', function ($id, $queryParams) {
    include 'views/home.php';
});

$router->get('/products', function ()  {
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
