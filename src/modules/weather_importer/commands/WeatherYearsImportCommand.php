<?php

declare(strict_types=1);

namespace WeatherApp\modules\weather_importer\commands;

use WeatherApp\framework\kernel\TerminalKernel;
use WeatherApp\modules\weather_importer\services\WeatherSaveService;

final class WeatherYearsImportCommand
{
    private readonly TerminalKernel $kernel;

    private int $yearsToImport;

    public const DEFAULT_YEARS = 5;

    public function __construct(TerminalKernel $kernel, int $yearsToImport)
    {
        $this->kernel = $kernel;
        $this->yearsToImport = $yearsToImport;
    }

    public function execute(): int
    {
        $weatherSaveService = $this->kernel->getContainer()->get(WeatherSaveService::class);
        \assert($weatherSaveService instanceof WeatherSaveService);
        $weatherSaveService->saveYearsWeatherInfoForAllStores($this->yearsToImport);

        echo '-------------------------------';
        echo 'avg weather data saved';
        echo '-------------------------------';

        return 0;
    }
}
