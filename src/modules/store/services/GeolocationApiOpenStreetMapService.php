<?php

namespace WeatherApp\modules\store\services;

use Httpful\Client;

final class GeolocationApiOpenStreetMapService implements GeolocationApiServiceInterface
{
    public const API_URL = 'nominatim.openstreetmap.org/';

    /**
     * @param array<string,mixed> $parameters
     */
    private function createUrl(array $parameters): string
    {
        $url = 'https://' . self::API_URL . '?';

        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }
        $url = rtrim($url, '&');

        // DEBUG
        var_dump($url);

        return $url;
    }

    /**
     * @param array<string,mixed> $parameters
     *
     * @throws \Exception
     */
    private function doCall(array $parameters = []): mixed
    {
        $response = Client::get_request($this->createUrl($parameters))
                                   ->withTimeout(10)
                                   ->followRedirects()
                                   ->send();

        $data = json_decode($response->getRawBody(), false, 512, JSON_THROW_ON_ERROR);

        if (isset($data->error_message)) {
            throw new \Exception('Error: ' . $data->error_message);
        }

        if (!isset($data[0]->lat)) {
            throw new \Exception("no results found:" . print_r($response, true));
        }

        return $data;
    }

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
        $items = [];
        $variables = [$city, $zip, $country];
        foreach ($variables as $variable) {
            if (empty($variable)) {
                continue;
            }

            $items[] = $variable;
        }

        $results = $this->doCall([
            'q' => implode(' ', $items),
            'addressdetails'  => '1',
            'limit' => '1',
            'format' => 'json'
        ]);

        if (!\array_key_exists(0, $results)) {
            throw new \Exception('no coordinates found for address' . print_r($variables, true));
        }

        return [
            'latitude'  => $results[0]->lat,
            'longitude' => $results[0]->lon,
        ];
    }
}
