<?php

require_once 'Router.php';
require_once 'services/authService.php';
require_once 'services/shoppingCartService.php';
require_once 'database/db.php';

$auth = new Auth();
$shoppingCartService = new ShoppingCartService($auth, getDbConnection());
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

$router->get('/order/{id}', function ($id) use ($auth) {
    $_GET['id'] = $id;
    include 'views/order_details.php';
}, true);


$router->any('/login', function () {
    include 'views/login.php';
});

$router->any('/register', function () {
    include 'views/register.php';
});


$router->any('/products', function () use ($auth) {
    include 'views/products.php';
});

$router->get('/search', function ($queryParams) {
    $searchQuery = $queryParams['q'] ?? 'No query';
    echo "Search query: $searchQuery";
});

$router->any('/shoppingcart',  function () use ($auth, $shoppingCartService) {
    include 'views/shoppingcart.php';
});

$router->post('/order-confirmation',  function () use ($auth, $shoppingCartService) {
    include 'views/orderconfirm.php';
});


$router->get('/dashboard', function () {
    include 'views/dashboard.php';
});

$router->get('/purchase', function () {
    include 'views/purchase.php';
});

$router->get('/crudpage', function () {
    include 'views/crudpage.php';
});

$router->post('/crudpage', function () {
    include 'views/crudpage.php';
});

$router->serveStatic($_SERVER['REQUEST_URI'], __DIR__);

$router->handleRequest();
