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
     * @return null|list<StoreWeather>
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchMultiWeatherByStoreIdIfExists(int $store_id, int $data_type): ?array;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchByStoreIdAndWeekIfExists(int $store_id, int $data_type, int $week): ?StoreWeather;

    /**
     * @param list<WeatherDto>|WeatherDto       $weatherDtoOrWeatherDtoList
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function replace(int $id, int $store_id, int $data_type, array|WeatherDto $weatherDtoOrWeatherDtoList, ?int $week = null): bool;

    /**
     * @param list<WeatherDto>|WeatherDto       $weatherDtoOrWeatherDtoList
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function insert(int $store_id, int $data_type, array|WeatherDto $weatherDtoOrWeatherDtoList, ?int $week = null): bool;

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function deleteAllByStoreId(int $store_id, int $data_type, ?int $week = null): bool;
}
