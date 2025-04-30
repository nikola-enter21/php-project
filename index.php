<?php
// Include core
require 'Router.php';
require 'Request.php';
require 'Response.php';

// Include Middlewares
require 'middlewares/AdminAuthMiddleware.php';

// Include controllers
require 'controllers/AdminController.php';
require 'controllers/UserController.php';

$db = new Database('localhost', 'your_database', 'your_user', 'your_password');
$router = new Router(new Request($db), new Response());

// Global middlewares
//$router->use([AdminAuthMiddleware::class, 'handle']);

// Admin routes
$router->get(
    '/admin',
    [AdminController::class, 'dashboard'],
    [AdminAuthMiddleware::class] // Per-route middlewares
);

// User routes
$router->get('/', [UserController::class, 'index']);
$router->get('/user/:id', [UserController::class, 'getUserById']);
$router->post('/submit', [UserController::class, 'submitForm']);

// Run the router
$router->run();