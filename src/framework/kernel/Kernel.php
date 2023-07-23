<?php

namespace WeatherApp\framework\kernel;

use DI\Container;
use DI\ContainerBuilder;
use WeatherApp\framework\DependencyInjection;

abstract class Kernel
{
    abstract public function boot(): void;

    public function getContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(false);
        $containerBuilder->addDefinitions(DependencyInjection::definitions());

        return $containerBuilder->build();
    }
}
