<?php

namespace WeatherApp\modules\weather_importer\services;

use Httpful\Client;
use Httpful\Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use voku\weather\WeatherDto;
use WeatherApp\modules\store\entities\Store;
use WeatherApp\modules\store\entities\StoreWeather;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface;

class WeatherSaveService
{
    private ClientInterface|Client $client;

    private Factory|RequestFactoryInterface $requestFactory;

    private StoreRepositoryInterface $storeRepository;

    private StoreWeatherRepositoryInterface $storeWeatherRepository;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StoreRepositoryInterface $storeRepository,
        StoreWeatherRepositoryInterface $storeWeatherRepository
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->storeRepository = $storeRepository;
        $this->storeWeatherRepository = $storeWeatherRepository;
    }

    public function saveYearsWeatherInfoForAllStores(int $yearsToImport): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        foreach ($this->storeRepository->all() as $store) {
            $this->insertOrReplaceHistoricalWeatherData($weatherApi, $store, $yearsToImport);
        }
    }

    public function saveYearsWeatherInfoForStore(int $store_id, int $yearsToImport): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        $store = $this->storeRepository->fetchById($store_id);

        $this->insertOrReplaceHistoricalWeatherData($weatherApi, $store, $yearsToImport);
    }

    private function insertOrReplaceHistoricalWeatherData(WeatherApiService $weatherApi, Store $store, int $yearsToImport): void
    {
        // init
        $weatherPerWeek = [];

        // INFO: we can't fetch so many data at once, so let's do this year-by-year
        $currentYear = date('Y');
        $year = $currentYear - $yearsToImport;
        while ($year < $currentYear) {
            $weatherCollection = $weatherApi->getWeatherHistorical(
                $store->latitude,
                $store->longitude,
                new \DateTimeImmutable($year . '-01-01 00:00:00'),
                new \DateTimeImmutable($year . '-12-31 23:59:59')
            );

            foreach ($weatherCollection->getAll() as $weatherDto) {
                \assert($weatherDto instanceof WeatherDto);
                if (!$weatherDto->utcDateTime) {
                    continue;
                }
                $week = (int) $weatherDto->utcDateTime->format('W');

                if (!isset($weatherPerWeek[$week])) {
                    $weatherPerWeek[$week] = [];
                }
                $weatherPerWeek[$week][] = $weatherDto;
            }

            $year++;
        }

        $counter = [];
        $weekAvgData = [];
        foreach ($weatherPerWeek as $week => $weatherDtoList) {
            foreach ($weatherDtoList as $weatherDto) {
                \assert($weatherDto instanceof WeatherDto);

                foreach (get_object_vars($weatherDto) as $key => $value) {
                    if (!is_numeric($value)) {
                        continue;
                    }

                    if (!isset($counter[$week][$key])) {
                        $counter[$week][$key] = 0;
                    }
                    $counter[$week][$key]++;

                    if (!isset($weekAvgData[$week][$key])) {
                        $weekAvgData[$week][$key] = 0;
                    }
                    $weekAvgData[$week][$key] += $value;
                }
            }
        }

        foreach ($weekAvgData as $week => $data) {
            foreach ($data as $key => $value) {
                $weekAvgData[$week][$key] = $value / $counter[$week][$key];
            }
        }

        foreach ($weekAvgData as $week => $avgData) {
            $weeklyAvgWeather = new WeatherDto(
                $weatherPerWeek[$week][0]->unit,
                $weatherPerWeek[$week][0]->sources,
                $weatherPerWeek[$week][0]->latitude,
                $weatherPerWeek[$week][0]->longitude,
                round($avgData['temperature'], 1),
                $weatherPerWeek[$week][0]->temperatureUnit,
                round($avgData['dewPoint'], 1),
                round($avgData['humidity'], 1),
                round($avgData['pressure'], 1),
                round($avgData['windSpeed'], 1),
                $weatherPerWeek[$week][0]->windSpeedUnit,
                null,
                round($avgData['precipitation'], 1),
                $weatherPerWeek[$week][0]->precipitationUnit,
                round($avgData['cloudCover'], 1),
                null,
                null,
                null,
                null,
                (int) $avgData['sunshine'],
                $weatherPerWeek[$week][0]->sunshineUnit,
            );
            $dbWeather = $this->storeWeatherRepository->fetchByStoreIdAndWeekIfExists(
                $store->id,
                StoreWeather::DATA_TYPE_HISTORICAL,
                $week
            );
            if ($dbWeather) {
                $this->storeWeatherRepository->replace(
                    $dbWeather->id,
                    $store->id,
                    StoreWeather::DATA_TYPE_HISTORICAL,
                    $weeklyAvgWeather,
                    $week
                );
            } else {
                $this->storeWeatherRepository->insert(
                    $store->id,
                    StoreWeather::DATA_TYPE_HISTORICAL,
                    $weeklyAvgWeather,
                    $week
                );
            }
        }
    }

    public function saveCurrentWeatherInfoForAllStores(): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        foreach ($this->storeRepository->all() as $store) {
            $this->insertOrReplaceCurrentWeatherData($weatherApi, $store);
        }
    }

    private function insertOrReplaceCurrentWeatherData(WeatherApiService $weatherApi, Store $store): void
    {
        $currentWeather = $weatherApi->getWeatherCurrent($store->latitude, $store->longitude);

        $dbWeather = $this->storeWeatherRepository->fetchByStoreIdIfExists($store->id, StoreWeather::DATA_TYPE_CURRENT);
        if ($dbWeather) {
            $this->storeWeatherRepository->replace($dbWeather->id, $store->id, StoreWeather::DATA_TYPE_CURRENT, $currentWeather);
        } else {
            $this->storeWeatherRepository->insert($store->id, StoreWeather::DATA_TYPE_CURRENT, $currentWeather);
        }
    }

    public function saveCurrentWeatherInfoForStore(int $store_id): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        $store = $this->storeRepository->fetchById($store_id);

        $this->insertOrReplaceCurrentWeatherData($weatherApi, $store);
    }

    public function saveFutureWeatherInfoForAllStores(): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        foreach ($this->storeRepository->all() as $store) {
            $this->insertOrReplaceFutureWeatherData($weatherApi, $store);
        }
    }

    private function insertOrReplaceFutureWeatherData(WeatherApiService $weatherApi, Store $store): void
    {
        $weatherCollection = $weatherApi->getWeatherFuture(
            $store->latitude,
            $store->longitude,
            new \DateTimeImmutable(),
            (new \DateTimeImmutable())->modify('+7 days')
        );

        $counter = [];
        $avgData = [];
        $allWeatherData = $weatherCollection->getAll();
        foreach ($allWeatherData as $weatherDto) {
            \assert($weatherDto instanceof WeatherDto);

            foreach (get_object_vars($weatherDto) as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }

                if (!isset($counter[$key])) {
                    $counter[$key] = 0;
                }
                $counter[$key]++;

                if (!isset($avgData[$key])) {
                    $avgData[$key] = 0;
                }
                $avgData[$key] += $value;
            }
        }

        foreach ($avgData as $key => $value) {
            $avgData[$key] = $value / $counter[$key];
        }

        $avgWeather = new WeatherDto(
            $allWeatherData[0]->unit,
            $allWeatherData[0]->sources,
            $allWeatherData[0]->latitude,
            $allWeatherData[0]->longitude,
            round($avgData['temperature'], 1),
            $allWeatherData[0]->temperatureUnit,
            round($avgData['dewPoint'], 1),
            null,
            round($avgData['pressure'], 1),
            round($avgData['windSpeed'], 1),
            $allWeatherData[0]->windSpeedUnit,
            null,
            round($avgData['precipitation'], 1),
            $allWeatherData[0]->precipitationUnit,
            round($avgData['cloudCover'], 1),
            null,
            null,
            null,
            null,
            (int) $avgData['sunshine'],
            $allWeatherData[0]->sunshineUnit,
        );
        $dbWeather = $this->storeWeatherRepository->fetchByStoreIdIfExists($store->id, StoreWeather::DATA_TYPE_FUTURE);
        if ($dbWeather) {
            $this->storeWeatherRepository->replace(
                $dbWeather->id,
                $store->id,
                StoreWeather::DATA_TYPE_FUTURE,
                $avgWeather
            );
        } else {
            $this->storeWeatherRepository->insert(
                $store->id,
                StoreWeather::DATA_TYPE_FUTURE,
                $avgWeather
            );
        }
    }

    public function saveFutureWeatherInfoForStore(int $store_id): void
    {
        $weatherApi = new WeatherApiService($this->client, $this->requestFactory);

        $store = $this->storeRepository->fetchById($store_id);

        $this->insertOrReplaceFutureWeatherData($weatherApi, $store);
    }
}
