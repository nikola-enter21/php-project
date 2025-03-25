<?php
require 'Router.php';
require 'Request.php';
require 'Response.php';

$db = new Database('localhost', 'your_database', 'your_user', 'your_password');
$router = new Router(new Request($db), new Response());

$router->use(function (Request $req, Response $res, callable $next): void {
    if (str_starts_with($req->path(), '/admin') && !$req->session()->has('user')) {
        $res->status(401)->json(['error' => 'Unauthorized']);
        return;
    }
    $next();
});

$router->get('/', function (Request $req, Response $res): void {
    $req->session()->set('user', ['id' => 1, 'name' => 'John']);
    $res->view('home', ['name' => 'John']);
});

$router->get('/admin', function (Request $req, Response $res): void {
    $res->send('Hello, Admin!');
});

$router->get('/json', function (Request $req, Response $res): void {
    $res->json($req->session()->get('user'));
});

$router->get('/user/:id', function (Request $req, Response $res): void {
    $id = $req->param('id');
    $res->json(['user_id' => $id]);
});

$router->post('/submit', function (Request $req, Response $res): void {
    $data = $req->body();
    $res->json(['received' => $data]);
});

$router->run();
