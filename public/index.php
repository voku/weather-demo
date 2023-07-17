<?php

declare(strict_types=1);

use WeatherApp\framework\HttpKernel;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(new HttpKernel())->boot();
