<?php

namespace WeatherApp\framework;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Relay\Relay;

class HttpKernel extends Kernel
{
    public function boot(): void
    {
        $container = $this->getContainer();

        $routes = simpleDispatcher(static function (RouteCollector $r): void {
            $r->get('/', 'WeatherApp\modules\store\frontend\StoreList\StoreListShowController');
            $r->get('/store/{id:\d+}[/{edit:\d}]', 'WeatherApp\modules\store\frontend\Store\StoreItemShowController');
            $r->post('/store/{id:\d+}[/{edit:\d}]', 'WeatherApp\modules\store\frontend\Store\StoreItemEditController');
            $r->get('/json/{id:\d+}', 'WeatherApp\modules\store\frontend\Store\StoreItemJsonController');
        });

        $middlewareQueue[] = new FastRoute($routes);
        $middlewareQueue[] = new RequestHandler($container);

        $requestHandler = new Relay($middlewareQueue);
        $response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

        (new SapiEmitter())->emit($response);
    }
}
