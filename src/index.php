<?php

use services\Auth;
use services\ShoppingCartService;

require_once 'Router.php';
require_once 'services/authService.php';
require_once 'services/shoppingCartService.php';
require_once 'database/db.php';


$auth = new Auth();
$shoppingCartService = new ShoppingCartService($auth, getDbConnection());
$router = new Router('/', $auth);

$router->get('/', function () use ($auth, $shoppingCartService) {
    include 'views/home.php';
});

$router->get('/account', function () use ($auth, $shoppingCartService)  {
    $userData = $auth->getLoggedInUserData();
    $userName = $auth->getLoggedInUserName();

    include 'views/account.php';
}, true);

$router->any('/logout', function () use ($auth, $shoppingCartService)  {
    $auth->logout();
}, true);

$router->get('/orders', function () {
    include 'views/orders.php';
}, true);

$router->get('/order/{id}', function ($id) use ($auth, $shoppingCartService)  {
    $_GET['id'] = $id;
    include 'views/order_details.php';
}, true);


$router->any('/login', function ()use ($auth, $shoppingCartService) {
    include 'views/login.php';
});

$router->any('/register', function ()use ($auth, $shoppingCartService) {
    include 'views/register.php';
});


$router->any('/products', function () use ($auth, $shoppingCartService) {
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


$router->get('/dashboard', function () use ($auth, $shoppingCartService)  {
    if (!$auth->isAdminLogin()) {
        header('Location: /');
        exit;
    }
    include 'views/dashboard.php';
}, true);

$router->get('/productdetailpagina', function () use ($auth, $shoppingCartService) {
    include 'views/productdetailpagina.php';
});

$router->get('/crudpage', function () use ($auth, $shoppingCartService)  {
    if (!$auth->isAdminLogin()) {
        header('Location: /');
        exit;
    }
    include 'views/crudpage.php';
}, true);

$router->post('/crudpage', function () use ($auth, $shoppingCartService)  {
    if (!$auth->isAdminLogin()) {
        header('Location: /');
        exit;
    }
    include 'views/crudpage.php';
}, true);

$router->any('/productdetailpagina/{id}', function ($id) use ($auth, $shoppingCartService)  {
    $_GET['id'] = $id;
    include 'views/productdetailpagina.php';
}, true);

$router->any('/submitreview', function () use ($auth, $shoppingCartService) {
    include 'services/submitreview.php';
});

$router->serveStatic($_SERVER['REQUEST_URI'], __DIR__);

$router->handleRequest();
