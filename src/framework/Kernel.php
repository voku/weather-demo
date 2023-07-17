<?php

namespace WeatherApp\framework;

use DI\Container;
use DI\ContainerBuilder;
use function DI\create;
use function DI\get;
use Httpful\Client;
use Httpful\Factory;
use Laminas\Diactoros\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WeatherApp\modules\store\services\GeolocationApiFakeService;
use WeatherApp\modules\store\services\GeolocationApiGoogleService;

abstract class Kernel
{
    abstract public function boot(): void;

    public function getContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(false);
        $containerBuilder->addDefinitions([
            //
            // controller
            //
            'WeatherApp\modules\store\frontend\StoreList\StoreListShowController' => create('WeatherApp\modules\store\frontend\StoreList\StoreListShowController')->constructor(
                get('Twig\Environment'),
                get('Psr\Http\Message\ResponseInterface'),
                get('WeatherApp\modules\store\repositories\StoreRepositoryInterface')
            ),
            'WeatherApp\modules\store\frontend\Store\StoreItemShowController' => create('WeatherApp\modules\store\frontend\Store\StoreItemShowController')->constructor(
                get('Twig\Environment'),
                get('Psr\Http\Message\ResponseInterface'),
                get('WeatherApp\modules\store\repositories\StoreRepositoryInterface'),
                get('WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface')
            ),
            'WeatherApp\modules\store\frontend\Store\StoreItemEditController' => create('WeatherApp\modules\store\frontend\Store\StoreItemEditController')->constructor(
                get('Psr\Http\Message\ResponseInterface'),
                get('WeatherApp\modules\store\repositories\StoreRepositoryInterface'),
                get('WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface'),
                get('WeatherApp\modules\store\services\GeolocationApiServiceInterface'),
                get('WeatherApp\modules\weather_importer\services\WeatherSaveService')
            ),
            'WeatherApp\modules\store\frontend\Store\StoreItemJsonController' => create('WeatherApp\modules\store\frontend\Store\StoreItemJsonController')->constructor(
                get('Psr\Http\Message\ResponseInterface'),
                get('WeatherApp\modules\store\repositories\StoreRepositoryInterface'),
                get('WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface')
            ),

            //
            // repositories
            //
            'WeatherApp\modules\store\repositories\StoreRepositoryInterface' => create('WeatherApp\modules\store\repositories\StoreRepositoryPdo')->constructor(
                get('WeatherApp\framework\Database')
            ),
            'WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface' => create('WeatherApp\modules\store\repositories\StoreWeatherRepositoryPdo')->constructor(
                get('WeatherApp\framework\Database')
            ),

            //
            // services
            //
            'WeatherApp\modules\weather_importer\services\WeatherSaveService' => create('WeatherApp\modules\weather_importer\services\WeatherSaveService')->constructor(
                get('Psr\Http\Client\ClientInterface'),
                get('Psr\Http\Message\RequestFactoryInterface'),
                get('WeatherApp\modules\store\repositories\StoreRepositoryInterface'),
                get('WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface')
            ),
            'WeatherApp\modules\store\services\GeolocationApiServiceInterface' => static function () {
                //return new GeolocationApiGoogleService($_ENV['google_maps_api_key']);
                return new GeolocationApiFakeService();
            },

            //
            // framework
            //
            'Psr\Http\Message\ResponseInterface' => static function () {
                return new Response();
            },
            'Twig\Environment' => static function () {
                $loader = new FilesystemLoader(__DIR__ . '/../modules/store/frontend/templates/');

                return new Environment($loader);
            },
            'WeatherApp\framework\Database' => static function () {
                return new Database();
            },
            'Psr\Http\Client\ClientInterface' => static function () {
                return new Client();
            },
            'Psr\Http\Message\RequestFactoryInterface' => static function () {
                return new Factory();
            },
        ]);

        return $containerBuilder->build();
    }
}
