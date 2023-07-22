<?php

namespace WeatherApp\framework;

use FastRoute\RouteCollector;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Relay\Relay;
use function FastRoute\simpleDispatcher;

class HttpKernel extends Kernel
{
    public function boot(): void
    {
        $container = $this->getContainer();

        $routes = simpleDispatcher(static function (RouteCollector $r): void {
            HttpRouting::routes($r);
        });

        $middlewareQueue[] = new FastRoute($routes);
        $middlewareQueue[] = new RequestHandler($container);

        $requestHandler = new Relay($middlewareQueue);
        $response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

        (new SapiEmitter())->emit($response);
    }
}
