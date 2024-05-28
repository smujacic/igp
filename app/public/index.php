<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); 
$dotenv->load();

use App\Core\Router;
use App\Core\Request;
use App\Controllers\UserController;
use App\Controllers\PaymentController;
use App\Middleware\AuthMiddleware;

$router = new Router();

AuthMiddleware::handle();

// Users endpoints
$router->add('GET', 'public/users', [new UserController(), 'getUsers']);
$router->add('GET', 'public/users/(\d+)', [new UserController(), 'getUser']);
$router->add('POST', 'public/users', [new UserController(), 'createUser']);

// Payments endpoints
$router->add('POST', 'public/payment', [new PaymentController(), 'createPayment']);
$router->add('POST', 'public/hookurl', [new PaymentController(), 'webhookUpdate']);

$router->dispatch(Request::method(), Request::uri());