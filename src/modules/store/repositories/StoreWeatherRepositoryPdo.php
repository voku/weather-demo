<?php

namespace WeatherApp\modules\store\repositories;

use voku\weather\WeatherDto;
use WeatherApp\framework\storage\Database;
use WeatherApp\modules\store\entities\StoreWeather;

class StoreWeatherRepositoryPdo implements StoreWeatherRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->pdo;
    }

    /**
     * @return list<StoreWeather>
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchAllByStoreId(int $store_id, int $data_type, ?int $week = null): array
    {
        $sql = sprintf('
            SELECT 
                * 
            FROM 
                stores_weather 
            WHERE 
                store_id = %d 
            AND 
                data_type = %d',
            $store_id, $data_type
        );

        if ($week) {
            $sql .= sprintf(' AND week = %d', $week);
        }

        $weatherList = [];
        $result = $this->pdo->query($sql);
        if ($result === false) {
            throw new \Exception('query error: ' . print_r($this->pdo->errorInfo(), true));
        }
        foreach ($result as $row) {
            $weatherList[] = new StoreWeather(
                $row['id'],
                $row['store_id'],
                $row['data_type'],
                WeatherDto::createFromJson($row['json_data']),
            );
        }

        return $weatherList;
    }

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchByStoreIdAndWeekIfExists(int $store_id, int $data_type, int $week): ?StoreWeather
    {
        $weatherList = $this->fetchAllByStoreId($store_id, $data_type, $week);

        if (\count($weatherList) === 0) {
            return null;
        }

        if (\count($weatherList) > 1) {
            throw new \Exception('We do not want to find multiple entries here, given: ' . print_r($weatherList, true));
        }

        return $weatherList[0];
    }

    /**
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchByStoreIdIfExists(int $store_id, int $data_type): ?StoreWeather
    {
        $weatherList = $this->fetchAllByStoreId($store_id, $data_type);

        if (\count($weatherList) === 0) {
            return null;
        }

        if (\count($weatherList) > 1) {
            throw new \Exception('We do not want to find multiple entries here, given: ' . print_r($weatherList, true));
        }

        return $weatherList[0];
    }

    /**
     * @return null|list<StoreWeather>
     *
     * @phpstan-param StoreWeather::DATA_TYPE_* $data_type
     */
    public function fetchMultiWeatherByStoreIdIfExists(int $store_id, int $data_type): ?array
    {
        $sql = sprintf('
            SELECT 
                * 
            FROM 
                stores_weather 
            WHERE 
                store_id = %d 
            AND 
                data_type = %d',
                       $store_id, $data_type
        );

        $weatherList = [];
        $result = $this->pdo->query($sql);
        if ($result === false) {
            throw new \Exception('query error: ' . print_r($this->pdo->errorInfo(), true));
        }

        foreach ($result as $row) {
            $data = json_decode($row['json_data'], true, 512, \JSON_THROW_ON_ERROR);

            foreach ($data as $item) {
                $weatherList[] = new StoreWeather(
                    $row['id'],
                    $row['store_id'],
                    $row['data_type'],
                    WeatherDto::createFromJson(json_encode($item)),
                );
            }
        }

        if (\count($weatherList) === 0) {
            return null;
        }

        return $weatherList;
    }

    /**
     * @param list<WeatherDto>|WeatherDto $weatherDtoOrWeatherDtoList
     */
    public function replace(
        int        $id,
        int        $store_id,
        int        $data_type,
        array|WeatherDto $weatherDtoOrWeatherDtoList,
        ?int       $week = null,
    ): bool {
        $smt = $this->pdo->prepare('
            REPLACE INTO 
                stores_weather (id,store_id,data_type,json_data,week) 
                VALUES (:id,:store_id,:data_type,:json_data,:week)
        ');
        $smt->bindParam(':id', $id);
        $smt->bindParam(':store_id', $store_id);
        $smt->bindParam(':data_type', $data_type);
        $json_data_weather = json_encode($weatherDtoOrWeatherDtoList, \JSON_THROW_ON_ERROR);
        $smt->bindParam(':json_data', $json_data_weather);
        $smt->bindParam(':week', $week);

        return $smt->execute();
    }

    /**
     * @param list<WeatherDto>|WeatherDto $weatherDtoOrWeatherDtoList
     */
    public function insert(
        int        $store_id,
        int        $data_type,
        array|WeatherDto $weatherDtoOrWeatherDtoList,
        ?int       $week = null,
    ): bool {
        $smt = $this->pdo->prepare('
            INSERT INTO 
                stores_weather (store_id,data_type,json_data,week) 
                VALUES (:store_id,:data_type,:json_data,:week)
        ');
        $smt->bindParam(':store_id', $store_id);
        $smt->bindParam(':data_type', $data_type);
        $json_data_weather = json_encode($weatherDtoOrWeatherDtoList, \JSON_THROW_ON_ERROR);
        $smt->bindParam(':json_data', $json_data_weather);
        $smt->bindParam(':week', $week);

        return $smt->execute();
    }

    public function deleteAllByStoreId(int $store_id, int $data_type, ?int $week = null): bool
    {
        $sql = sprintf('
            DELETE FROM 
                stores_weather 
            WHERE 
                store_id = %d
            AND 
                data_type = %d',
           $store_id, $data_type
        );

        if ($week) {
            $sql .= sprintf(' AND week = %d', $week);
        }

        return (bool) $this->pdo->query($sql);
    }
}
