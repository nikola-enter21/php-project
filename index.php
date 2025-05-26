<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\QuoteController;
use App\Middlewares\AuthMiddleware;
use App\Controllers\CollectionController;
use App\Models\QuoteModel;
use App\Models\UserModel;
use App\Models\CollectionModel;
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
    QuoteModel::class,
    fn() => new QuoteModel($container->get(Database::class))
);
$container->set(
    HomeController::class,
    fn($c) => new HomeController($c->get(QuoteModel::class))
);
$container->set(
    UserController::class,
    fn($c) => new UserController($c->get(UserModel::class))
);
$container->set(
    QuoteController::class,
    fn($c) => new QuoteController($c->get(QuoteModel::class))
);
$container->set(
    AdminController::class,
    fn($c) => new AdminController($c->get(UserModel::class))
);

// Routes
try {
    $router = new Router(new Request(), new Response());

    // Home Routes
    $router->get('/', [$container->get(HomeController::class), 'index']);

    // Quote Actions
    $router->get('/quotes/create', [$container->get(QuoteController::class), 'createView'], [AuthMiddleware::class]);
    $router->post('/quotes/create', [$container->get(QuoteController::class), 'create'], [AuthMiddleware::class]);
    $router->post('/quotes/:id/save', [$container->get(QuoteController::class), 'saveQuote'], [AuthMiddleware::class]);
    $router->post('/quotes/:id/like', [$container->get(QuoteController::class), 'likeQuote'], [AuthMiddleware::class]);
    $router->post('/quotes/:id/report', [$container->get(QuoteController::class), 'reportQuote'], [AuthMiddleware::class]);
    $router->post('/quotes/add-to-collection', [$container->get(QuoteController::class), 'addToCollection'], [AuthMiddleware::class]);

    //Collection Routes
    $router->post('/collections/store', [CollectionController::class, 'create'], [AuthMiddleware::class]);
    $router->get('/collections', [CollectionController::class, 'getCollections'], [AuthMiddleware::class]);

    // User Routes
    $router->get('/login', [$container->get(UserController::class), 'loginView']);
    $router->get('/register', [$container->get(UserController::class), 'registerView']);
    $router->post('/login', [$container->get(UserController::class), 'login']);
    $router->post('/register', [$container->get(UserController::class), 'register']);
    $router->post('/logout', [$container->get(UserController::class), 'logout']);

    // Admin Routes
    $router->get('/admin/dashboard', [$container->get(AdminController::class), 'dashboard'], [AuthMiddleware::class]);
    $router->post('/admin/roles', [$container->get(AdminController::class), 'manageRoles'], [AuthMiddleware::class]);
    $router->get('/admin/logs', [$container->get(AdminController::class), 'viewLogs'], [AuthMiddleware::class]);

    $router->run();
} catch (Exception $e) {
    error_log($e->getMessage());
    die($e->getMessage());
}