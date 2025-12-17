<?php

namespace XMVC;

use XMVC\Service\Config;
use XMVC\Service\Router;
use App\Http\Request;
use App\Http\Response;

class Kernel
{
    protected $container;
    protected $router;
    protected $request;
    protected $config;

    public function __construct(Container $container, Router $router, Request $request, Config $config)
    {
        $this->container = $container;
        $this->router = $router;
        $this->request = $request;
        $this->config = $config;
    }

    public function handle()
    {
        $route = $this->router->match($this->request);

        if (!$route) {
            (new Response("404 Not Found", 404))->send();
            return;
        }

        $middleware = $this->gatherMiddleware($route);

        $response = $this->sendRequestThroughMiddleware($this->request, $middleware, function ($request) use ($route) {
            $actionResponse = $this->router->handleAction($route['action'], $route['params'], $request);
            return $this->prepareResponse($actionResponse);
        });

        $response->send();
    }

    protected function gatherMiddleware($route)
    {
        $globalMiddleware = $this->config->get('middleware.global', []);
        $routeMiddlewareMap = $this->config->get('middleware.route', []);

        $middleware = $globalMiddleware;
        foreach ($route['middleware'] as $key) {
            if (isset($routeMiddlewareMap[$key])) {
                $middleware[] = $routeMiddlewareMap[$key];
            }
        }

        return $middleware;
    }

    protected function sendRequestThroughMiddleware($request, $middleware, $destination)
    {
        $pipeline = array_reduce(
            array_reverse($middleware),
            function ($next, $middlewareClass) {
                return function ($request) use ($next, $middlewareClass) {
                    $middlewareInstance = $this->container->make($middlewareClass);
                    return $middlewareInstance->handle($request, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }

    protected function prepareResponse($response)
    {
        if ($response instanceof Response) {
            return $response;
        }

        if (is_array($response) || is_object($response)) {
            return Response::json($response);
        }

        return new Response((string) $response);
    }
}