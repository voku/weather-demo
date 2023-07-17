<?php

namespace WeatherApp\modules\store\services;

interface GeolocationApiServiceInterface
{
    /**
     * Get coordinates latitude/longitude.
     *
     * @return array{
     *     latitude: float,
     *     longitude: float
     * }
     */
    public function getCoordinates(
        ?string $street = null,
        ?string $houseNum = null,
        ?string $city = null,
        ?string $zip = null,
        ?string $country = null
    ): array;
}
