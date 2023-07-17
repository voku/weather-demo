<?php

declare(strict_types=1);

namespace WeatherApp\modules\weather_importer\commands;

use WeatherApp\framework\TerminalKernel;
use WeatherApp\modules\weather_importer\services\WeatherSaveService;

final class WeatherFutureImportCommand
{
    private readonly TerminalKernel $kernel;

    public function __construct(TerminalKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function execute(): int
    {
        $weatherSaveService = $this->kernel->getContainer()->get('WeatherApp\modules\weather_importer\services\WeatherSaveService');
        \assert($weatherSaveService instanceof WeatherSaveService);
        $weatherSaveService->saveFutureWeatherInfoForAllStores();

        echo '-------------------------------';
        echo 'future weather data saved';
        echo '-------------------------------';

        return 0;
    }
}
