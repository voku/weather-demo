<?php

declare(strict_types=1);

namespace WeatherApp\modules\weather_importer\commands;

use WeatherApp\framework\kernel\TerminalKernel;
use WeatherApp\modules\weather_importer\services\WeatherSaveService;

final class WeatherCurrentImportCommand
{
    private readonly TerminalKernel $kernel;

    public function __construct(TerminalKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function execute(): int
    {
        $weatherSaveService = $this->kernel->getContainer()->get(WeatherSaveService::class);
        \assert($weatherSaveService instanceof WeatherSaveService);
        $weatherSaveService->saveCurrentWeatherInfoForAllStores();

        echo '-------------------------------';
        echo 'current weather data saved';
        echo '-------------------------------';

        return 0;
    }
}
