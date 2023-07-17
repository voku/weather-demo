<?php

namespace WeatherApp\modules\store\repositories;

use voku\weather\WeatherDto;
use WeatherApp\modules\store\entities\StoreWeather;

interface StoreWeatherRepositoryInterface
{
    /**
     * @return list<StoreWeather>
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchAllByStoreId(int $store_id, int $data_type, ?int $week = null): array;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchByStoreIdIfExists(int $store_id, int $data_type): ?StoreWeather;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchByStoreIdAndWeekIfExists(int $store_id, int $data_type, int $week): ?StoreWeather;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function replace(int $id, int $store_id, int $data_type, WeatherDto $weatherDto, ?int $week = null): bool;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function insert(int $store_id, int $data_type, WeatherDto $weatherDto, ?int $week = null): bool;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function deleteAllByStoreId(int $store_id, int $data_type, ?int $week = null): bool;
}
