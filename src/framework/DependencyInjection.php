<?php

namespace WeatherApp\framework;

use Httpful\Client;
use Httpful\Factory;
use Laminas\Diactoros\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WeatherApp\framework\storage\Database;
use WeatherApp\modules\store\frontend\Store\StoreItemEditController;
use WeatherApp\modules\store\frontend\Store\StoreItemJsonController;
use WeatherApp\modules\store\frontend\Store\StoreItemShowController;
use WeatherApp\modules\store\frontend\StoreList\StoreListShowController;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreRepositoryPdo;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryPdo;
use WeatherApp\modules\store\services\GeolocationApiOpenStreetMapService;
use WeatherApp\modules\store\services\GeolocationApiServiceInterface;
use WeatherApp\modules\weather_importer\services\WeatherSaveService;
use function DI\create;
use function DI\get;

final class DependencyInjection
{
    public static function definitions(): array
    {
        return [
            //
            // controller
            //
            StoreListShowController::class                   => create(StoreListShowController::class)->constructor(
                get(Environment::class),
                get(ResponseInterface::class),
                get(StoreRepositoryInterface::class)
            ),
            StoreItemShowController::class                   => create(StoreItemShowController::class)->constructor(
                get(Environment::class),
                get(ResponseInterface::class),
                get(StoreRepositoryInterface::class),
                get(StoreWeatherRepositoryInterface::class)
            ),
            StoreItemEditController::class                   => create(StoreItemEditController::class)->constructor(
                get(ResponseInterface::class),
                get(StoreRepositoryInterface::class),
                get(StoreWeatherRepositoryInterface::class),
                get(GeolocationApiServiceInterface::class),
                get(WeatherSaveService::class)
            ),
            StoreItemJsonController::class                   => create(StoreItemJsonController::class)->constructor(
                get(ResponseInterface::class),
                get(StoreRepositoryInterface::class),
                get(StoreWeatherRepositoryInterface::class)
            ),

            //
            // repositories
            //
            StoreRepositoryInterface::class                  => create(StoreRepositoryPdo::class)->constructor(
                get(Database::class)
            ),
            StoreWeatherRepositoryInterface::class           => create(StoreWeatherRepositoryPdo::class)->constructor(
                get(Database::class)
            ),

            //
            // services
            //
            WeatherSaveService::class                        => create(WeatherSaveService::class)->constructor(
                get(ClientInterface::class),
                get(RequestFactoryInterface::class),
                get(StoreRepositoryInterface::class),
                get(StoreWeatherRepositoryInterface::class)
            ),
            GeolocationApiServiceInterface::class            => static function () {
                //return new GeolocationApiGoogleService($_ENV['google_maps_api_key']);
                return new GeolocationApiOpenStreetMapService();
            },

            //
            // framework
            //
            ResponseInterface::class       => static function () {
                return new Response();
            },
            Environment::class => static function () {
                $loader = new FilesystemLoader(__DIR__ . '/../modules/store/frontend/templates/');

                return new Environment($loader);
            },
            Database::class                                  => static function () {
                return new Database();
            },
            ClientInterface::class          => static function () {
                return new Client();
            },
            RequestFactoryInterface::class => static function () {
                return new Factory();
            },
        ];
    }
}
