<?php

namespace WeatherApp\modules\store\services;

final class GeolocationApiFakeService implements GeolocationApiServiceInterface
{
    /**
     * Get coordinates latitude/longitude.
     *
     * @throws \Exception
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
    ): array {
        $return = [
            [
                'latitude'  => 48.137154,
                'longitude' => 11.576124,
            ],
            [
                'latitude'  => 52.520008,
                'longitude' => 13.404954,
            ],
            [
                'latitude'  => 53.551086,
                'longitude' => 9.993682,
            ],
        ];

        return $return[random_int(0, \count($return) - 1)];
    }
}
