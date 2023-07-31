<?php

namespace WeatherApp\modules\store\entities;

use voku\weather\WeatherDto;

class StoreWeather
{
    public const DATA_TYPE_CURRENT = 10;

    public const DATA_TYPE_HISTORICAL = 20;

    public const DATA_TYPE_FUTURE = 30;

    public const DATA_TYPE_TODAY = 40;

    public function __construct(
        public readonly int $id,
        public readonly int $store_id,
        /**
         * @var StoreWeather::DATA_TYPE_*
         */
        public readonly int $data_type,
        public readonly WeatherDto $weatherDto
    ) {
    }
}
