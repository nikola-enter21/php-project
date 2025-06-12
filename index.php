<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\QuoteController;
use App\Middlewares\AuthMiddleware;
use App\Controllers\CollectionController;
use App\Middlewares\AdminMiddleware;
use App\Models\QuoteModel;
use App\Models\UserModel;
use App\Models\LogModel;
use App\Models\CollectionModel;
use Core\Container;
use Core\Router;
use Core\Request;
use Core\Response;
use App\Controllers\UserController;
use Core\Database;

require './autoload.php';

// Dependency Injection Container
$container = new Container();

$container->set(Database::class, function () {
    $config = require 'config/database.php';
    return new Database(
        $config['host'],
        $config['database'],
        $config['username'],
        $config['password'],
        $config['port']
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
    LogModel::class,
    fn() => new LogModel($container->get(Database::class))
);
$container->set(
    CollectionModel::class,
    fn() => new CollectionModel($container->get(Database::class))
);
$container->set(
    HomeController::class,
    fn($c) => new HomeController($c->get(QuoteModel::class))
);
$container->set(
    UserController::class,
    fn($c) => new UserController($c->get(UserModel::class), $c->get(LogModel::class))
);
$container->set(
    QuoteController::class,
    fn($c) => new QuoteController(
        $c->get(QuoteModel::class),      
        $c->get(CollectionModel::class),
        $c->get(LogModel::class)
    )
);
$container->set(
    AdminController::class,
    fn($c) => new AdminController(
        $c->get(UserModel::class),
        $c->get(QuoteModel::class),
        $c->get(LogModel::class)
    )
);
$container->set(
    CollectionController::class,
    fn($c) => new CollectionController($c->get(CollectionModel::class), $c->get(LogModel::class))
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
    $router->post('/quotes/:id/add-to-collection', [$container->get(QuoteController::class), 'addToCollection'], [AuthMiddleware::class]);
    $router->post('/quotes/import-csv', [$container->get(QuoteController::class), 'importCsv'], [AuthMiddleware::class]);
    $router->get('/quotes/import-csv', [$container->get(QuoteController::class), 'importCsvView'], [AuthMiddleware::class]);
    $router->get('/quotes/:id', [$container->get(QuoteController::class), 'getQuoteDetails'], [AuthMiddleware::class]);
    $router->delete('/quotes/:id', [$container->get(QuoteController::class), 'deleteQuote'], [AuthMiddleware::class]);
    $router->post('/quotes/:id/annotations/create', [$container->get(QuoteController::class), 'addAnnotation'], [AuthMiddleware::class]);
    $router->get('/quotes/:id/annotations/create', [$container->get(QuoteController::class), 'addAnnotationView'], [AuthMiddleware::class]);
    $router->get('/quotes/:id/annotations', [$container->get(QuoteController::class), 'viewAnnotations'], [AuthMiddleware::class]);
    
    //Collection Routes
    $router->get('/collections/create', [$container->get(CollectionController::class), 'createView'], [AuthMiddleware::class]);
    $router->post('/collections/create', [$container->get(CollectionController::class), 'create'], [AuthMiddleware::class]);
    $router->get('/collections', [$container->get(CollectionController::class), 'getCollections'], [AuthMiddleware::class]);
    $router->get('/collections/json', [$container->get(CollectionController::class), 'getCollectionsJson'], [AuthMiddleware::class]);
    $router->get('/collections/:id/export-pdf', [$container->get(CollectionController::class), 'exportAsPdf'], [AuthMiddleware::class]);
    $router->get('/collections/:id/export-csv', [$container->get(CollectionController::class), 'exportAsCsv'], [AuthMiddleware::class]);
    $router->get('/collections/:id/export-html', [$container->get(CollectionController::class), 'exportAsHtml'], [AuthMiddleware::class]);    $router->get('/collections/:id/export-bibtex', [$container->get(CollectionController::class), 'exportAsBibtex'], [AuthMiddleware::class]);
    $router->delete('/collections/:collectionId/quotes/:quoteId/delete', [$container->get(CollectionController::class), 'deleteQuoteFromCollection'], [AuthMiddleware::class]);
    
    // User Routes
    $router->get('/login', [$container->get(UserController::class), 'loginView']);
    $router->get('/register', [$container->get(UserController::class), 'registerView']);
    $router->post('/login', [$container->get(UserController::class), 'login']);
    $router->post('/register', [$container->get(UserController::class), 'register']);
    $router->post('/logout', [$container->get(UserController::class), 'logout']);

    // Admin Routes
    $router->delete('/users/:id', [$container->get(UserController::class), 'deleteUser'], [AdminMiddleware::class]);
    $router->get('/admin/dashboard', [$container->get(AdminController::class), 'dashboard'], [AdminMiddleware::class]);
    $router->post('/admin/roles', [$container->get(AdminController::class), 'manageRoles'], [AdminMiddleware::class]);
    $router->get('/admin/users', [$container->get(AdminController::class), 'manageUsers'], [AdminMiddleware::class]);
    $router->patch('/admin/users/:id/role', [$container->get(AdminController::class), 'updateUserRole'], [AdminMiddleware::class]);
    $router->get('/admin/logs', [$container->get(AdminController::class), 'viewLogs'], [AdminMiddleware::class]);
    $router->delete('/admin/logs/:id', [$container->get(AdminController::class), 'deleteLogById'], [AdminMiddleware::class]);
    $router->delete('/admin/logs', [$container->get(AdminController::class), 'deleteLogs'], [AdminMiddleware::class]);
    $router->get('/admin/quotes/most-liked', [$container->get(AdminController::class), 'mostLikedQuotes'], [AdminMiddleware::class]);
    $router->get('/admin/quotes/reported', [$container->get(AdminController::class), 'reportedQuotes'], [AdminMiddleware::class]);

    $router->run();
} catch (Exception $e) {
    error_log($e->getMessage());
    die($e->getMessage());
}
