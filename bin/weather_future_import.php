<?php

declare(strict_types=1);

use WeatherApp\framework\kernel\TerminalKernel;
use WeatherApp\modules\weather_importer\commands\WeatherFutureImportCommand;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(static function () {
    error_reporting(E_ALL);
    ini_set('display_errors', 'stderr');

    $kernel = new TerminalKernel();
    $kernel->boot();

    (new WeatherFutureImportCommand( $kernel))->execute();
})();
