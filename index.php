<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Middlewares\AuthMiddleware;
use App\Models\UserModel;
use Core\Container;
use Core\Router;
use Core\Request;
use Core\Response;
use App\Controllers\UserController;
use Core\Database;

require './vendor/autoload.php';

// Dependency Injection Container
$container = new Container();
$container->set(Database::class, function () {
    $config = require 'config/database.php';
    return new Database(
        $config['host'],
        $config['database'],
        $config['username'],
        $config['password']
    );
});
$container->set(
    UserModel::class,
    fn() => new UserModel($container->get(Database::class))
);
$container->set(
    HomeController::class,
    fn($c) => new HomeController()
);
$container->set(
    UserController::class,
    fn($c) => new UserController($c->get(UserModel::class))
);
$container->set(
    AdminController::class,
    fn($c) => new AdminController($c->get(UserModel::class))
);

// Routes
try {
    $router = new Router(new Request(), new Response());

    $router->get('/', [$container->get(HomeController::class), 'index']);

    $router->get('/login', [$container->get(UserController::class), 'loginView']);
    $router->get('/register', [$container->get(UserController::class), 'registerView']);

    $router->get('/admin/dashboard', [$container->get(AdminController::class), 'dashboard'], [AuthMiddleware::class]);
    $router->post('/admin/roles', [$container->get(AdminController::class), 'manageRoles'], [AuthMiddleware::class]);
    $router->get('/admin/logs', [$container->get(AdminController::class), 'viewLogs'], [AuthMiddleware::class]);

    $router->run();
} catch (Exception $e) {
    error_log($e->getMessage());
    die($e->getMessage());
}

