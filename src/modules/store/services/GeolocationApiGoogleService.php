<?php

namespace WeatherApp\modules\store\services;

use Httpful\Client;

final class GeolocationApiGoogleService implements GeolocationApiServiceInterface
{
    public const API_URL = 'maps.googleapis.com/maps/api/geocode/json';

    private ?string $apiKey = null;

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

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

        if ($this->apiKey) {
            $url .= '&key=' . $this->apiKey;
        }

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

        if (!isset($data->results)) {
            throw new \Exception("no results found:" . print_r($response, true));
        }

        return $data->results;
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
        $variables = [$street, $houseNum, $city, $zip, $country];
        foreach ($variables as $variable) {
            if (empty($variable)) {
                continue;
            }

            $items[] = $variable;
        }

        $results = $this->doCall([
            'address' => implode(' ', $items),
            'sensor'  => 'false',
        ]);

        if (!\array_key_exists(0, $results)) {
            throw new \Exception('no coordinates found for address' . print_r($variables, true));
        }

        return [
            'latitude'  => $results[0]->geometry->location->lat,
            'longitude' => $results[0]->geometry->location->lng,
        ];
    }
}
