<?php
use Psr\Http\Message\ServerRequestInterface;

global $config;
global $router;

$router->routes[] = [
    'testerType' => 'uri',
    'uri' => $router->simplifyUri($config->get()['uri']) . '/',
    'handlerType' => 'custom',
    'handler' => function (ServerRequestInterface $request) use ($router) {
        return $router->statuses[700]($request);
    }
];