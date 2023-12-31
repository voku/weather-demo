<?php

declare(strict_types=1);

use WeatherApp\framework\kernel\TerminalKernel;
use WeatherApp\modules\weather_importer\commands\WeatherTodayImportCommand;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(static function () {
    error_reporting(E_ALL);
    ini_set('display_errors', 'stderr');

    $kernel = new TerminalKernel();
    $kernel->boot();

    (new WeatherTodayImportCommand( $kernel))->execute();
})();
