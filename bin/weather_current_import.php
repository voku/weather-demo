<?php

declare(strict_types=1);

use WeatherApp\framework\TerminalKernel;
use WeatherApp\modules\weather_importer\commands\WeatherCurrentImportCommand;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(static function () {
    error_reporting(E_ALL);
    ini_set('display_errors', 'stderr');

    $kernel = new TerminalKernel();
    $kernel->boot();

    (new WeatherCurrentImportCommand( $kernel))->execute();
})();
