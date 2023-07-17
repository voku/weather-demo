<?php

namespace WeatherApp\modules\weather_importer\services;

use Httpful\Client;
use Httpful\Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use voku\weather\provider\BrightskyHttpProvider;
use voku\weather\WeatherCollection;
use voku\weather\WeatherDto;
use voku\weather\WeatherQueryDto;

class WeatherApiService
{
    private ClientInterface|Client $client;

    private Factory|RequestFactoryInterface $requestFactory;

    public function __construct(
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null
    ) {
        $this->client = $client ?: new Client();
        $this->requestFactory = $requestFactory ?: new Factory();
    }

    public function getWeatherCurrent(float $latitude, float $longitude): WeatherDto
    {
        $weatherQuery = new WeatherQueryDto(
            $latitude,
            $longitude
        );

        return (new BrightskyHttpProvider($this->client, $this->requestFactory))
            ->getWeatherCurrent($weatherQuery);
    }

    public function getWeatherFuture(
        float $latitude,
        float $longitude,
        \DateTimeInterface $dateTime,
        \DateTimeInterface $lastDateTime,
    ): WeatherCollection {
        $weatherQuery = new WeatherQueryDto(
            $latitude,
            $longitude,
            $dateTime,
            $lastDateTime
        );

        return (new BrightskyHttpProvider($this->client, $this->requestFactory))
            ->getWeatherForecastCollection($weatherQuery);
    }

    public function getWeatherHistorical(
        float $latitude,
        float $longitude,
        \DateTimeInterface $dateTime,
        \DateTimeInterface $lastDateTime,
    ): WeatherCollection {
        $weatherQuery = new WeatherQueryDto(
            $latitude,
            $longitude,
            $dateTime,
            $lastDateTime
        );

        return (new BrightskyHttpProvider($this->client, $this->requestFactory))
            ->getWeatherHistoricalCollection($weatherQuery);
    }
}
