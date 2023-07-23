<?php

namespace WeatherApp\framework\routing;

use FastRoute\RouteCollector;
use WeatherApp\modules\store\frontend\Store\StoreItemEditController;
use WeatherApp\modules\store\frontend\Store\StoreItemJsonController;
use WeatherApp\modules\store\frontend\Store\StoreItemShowController;
use WeatherApp\modules\store\frontend\StoreList\StoreListShowController;

final class HttpRouting
{
    public static function routes(RouteCollector $r): void
    {
        $r->get('/', StoreListShowController::class);
        $r->get('/store/{id:\d+}[/{edit:\d}]', StoreItemShowController::class);
        $r->post('/store/{id:\d+}[/{edit:\d}]', StoreItemEditController::class);
        $r->get('/json/{id:\d+}', StoreItemJsonController::class);
    }
}
