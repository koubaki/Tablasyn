<?php
namespace Tablasyn;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Router {
    public $routes;
    public $statuses;

    private function handleHandlerType(ServerRequestInterface $request, array $route) {
        switch ($route['handlerType']) {
            case 'redirect':
                switch ($route['redirType']) {
                    case 301:
                        return [true, $this->statuses[301]($request, $route['target'])];

                    case 302:
                        return [true, $this->statuses[302]($request, $route['target'])];

                    case 307:
                        return [true, $this->statuses[307]($request, $route['target'])];

                    case 308:
                        return [true, $this->statuses[308]($request, $route['target'])];

                    default:
                        return [true, $this->statuses[303]($request)];
                }

            case 'custom':
                return [true, $route['handler']($request)];

            case 'group':
                if ($route['groupTester']()) {
                    return [false, $route['group']];
                } else {
                    return [true, $route['handler']($request)];
                }

            default:
                return [true, $this->statuses[701]($request)];
        }
    }

    public function __construct() {
        $this->routes = [];
        $this->statuses = [
            301 => function (ServerRequestInterface $request, string $target) {
                return new Response(301, ['Location' => trim($target), 'Content-Type' => 'text/plain'], 'Tablasyn Message: 301 Moved Permanently');
            },
            302 => function (ServerRequestInterface $request, string $target) {
                return new Response(302, ['Location' => trim($target), 'Content-Type' => 'text/plain'], 'Tablasyn Message: 302 Found');
            },
            307 => function (ServerRequestInterface $request, string $target) {
                return new Response(307, ['Location' => trim($target), 'Content-Type' => 'text/plain'], 'Tablasyn Message: 301 Temporary Redirect');
            },
            308 => function (ServerRequestInterface $request, string $target) {
                return new Response(308, ['Location' => trim($target), 'Content-Type' => 'text/plain'], 'Tablasyn Message: 308 Permanent Redirect');
            },
            404 => function (ServerRequestInterface $request) {
                return new Response(404, ['Content-Type' => 'text/plain'], 'Tablasyn Message: 404 Not Found');
            },
            700 => function (ServerRequestInterface $request) {
                return new Response(700, ['Content-Type' => 'text/plain'], 'Tablasyn Message: 700 Framework OK', 1.1, 'Framework OK');
            },
            701 => function (ServerRequestInterface $request) {
                return new Response(701, ['Content-Type' => 'text/plain'], 'Tablasyn Message: 701 Unknown Router Handler', 1.1, 'Unknown Router Handler');
            },
            702 => function (ServerRequestInterface $request) {
                return new Response(702, ['Content-Type' => 'text/plain'], 'Tablasyn Message: 702 Unknown Router Handler', 1.1, 'Unknown Router Tester');
            },
            703 => function (ServerRequestInterface $request) {
                return new Response(703, ['Content-Type' => 'text/plain'], 'Tablasyn Message: 703 Unknown Router Handler', 1.1, 'Unknown Router Redirect');
            }
        ];
    }

    public function route(ServerRequestInterface $request) {
        $index = 0;
        $routeGroup = $this->routes;

        while ($index < count($routeGroup)) {
            $route = $routeGroup[$index];

            switch ($route['testerType']) {
                case 'uri':
                    if ($this->simplifyUri($route['uri']) === $this->simplifyUri($request->getUri()->getPath())) {
                        if (isset($route['methods'])) {
                            if (in_array($request->getMethod(), $route['methods'], true)) {
                                $result = $this->handleHandlerType($request, $route);
                                if ($result[0]) {
                                    return $result[1];
                                }
                                $routeGroup = $result[1];
                                $index = 0;
                            }
                        } else {
                            $result = $this->handleHandlerType($request, $route);
                            if ($result[0]) {
                                return $result[1];
                            }
                            $routeGroup = $result[1];
                            $index = 0;
                        }
                    }
                break;

                case 'regex':
                    if (preg_match($route['regex'], $this->simplifyUri($request->getUri()->getPath()))) {
                        if (isset($route['methods'])) {
                            if (in_array($request->getMethod(), $route['methods'], true)) {
                                $result = $this->handleHandlerType($request, $route);
                                if ($result[0]) {
                                    return $result[1];
                                }
                                $routeGroup = $result[1];
                                $index = 0;
                            }
                        } else {
                            $result = $this->handleHandlerType($request, $route);
                            if ($result[0]) {
                                return $result[1];
                            }
                            $routeGroup = $result[1];
                            $index = 0;
                        }
                    }
                break;

                case 'broad':
                    if (isset($route['methods'])) {
                        if (in_array($request->getMethod(), $route['methods'], true)) {
                            $result = $this->handleHandlerType($request, $route);
                            if ($result[0]) {
                                return $result[1];
                            }
                            $routeGroup = $result[1];
                            $index = 0;
                        }
                    } else {
                        $result = $this->handleHandlerType($request, $route);
                        if ($result[0]) {
                            return $result[1];
                        }
                        $routeGroup = $result[1];
                        $index = 0;
                    }
                break;

                case 'custom':
                    if ($route['tester']($request)) {
                        if (isset($route['methods'])) {
                            if (in_array($request->getMethod(), $route['methods'], true)) {
                                $result = $this->handleHandlerType($request, $route);
                                if ($result[0]) {
                                    return $result[1];
                                }
                                $routeGroup = $result[1];
                                $index = 0;
                            }
                        } else {
                            $result = $this->handleHandlerType($request, $route);
                            if ($result[0]) {
                                return $result[1];
                            }
                            $routeGroup = $result[1];
                            $index = 0;
                        }
                    }
                break;

                default:
                    return $this->statuses[702]($request);
            }

            $index++;
        }

        return $this->statuses[404]($request);
    }

    public function simplifyUri(string $uri) {
        $result = str_replace('\\', '/', $uri);
        $result = rtrim($result, '/');
        $result = preg_replace('#/+#', '/', $result);

        return $result;
    }
}
