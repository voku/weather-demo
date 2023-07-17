<?php

namespace WeatherApp\modules\store\services;

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
        $curl = curl_init();

        curl_setopt($curl, \CURLOPT_URL, $this->createUrl($parameters));
        curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, \CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, \CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($curl);

        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        if ($errorNumber) {
            throw new \Exception($errorMessage);
        }

        if (is_bool($response)) {
            throw new \Exception('Response should not be boolean.');
        }

        $response = json_decode($response);

        // API returns with an error
        if (isset($response->error_message)) {
            throw new \Exception($response->error_message);
        }

        return $response->results;
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
