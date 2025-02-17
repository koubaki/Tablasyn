<?php
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;
use Tablasyn\Auto;
use Tablasyn\Config;
use Tablasyn\Router;

global $config;
$config = new Config();
global $router;
$router = new Router();
global $auto;
$auto = new Auto();

$server = new HttpServer(
    function (ServerRequestInterface $request) use ($router) {
        return $router->route($request);
    }
);

$socket = new SocketServer(trim($config->get()['ip']) . ':' . trim($config->get()['port']));

$socket->on(
    'error',
    function (Exception $e) {
        echo $e . "\n";
        error_log($e . "\n", 3, str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/logs.txt'));
    }
);

$server->listen($socket);
