<?php

namespace WeatherApp\framework;

use DI\Container;
use DI\ContainerBuilder;

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
