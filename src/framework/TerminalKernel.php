<?php

namespace WeatherApp\framework;

use DI\Container;

class TerminalKernel extends Kernel
{
    public readonly Container $container;

    public function boot(): void
    {
        $this->container = $this->getContainer();
    }
}
