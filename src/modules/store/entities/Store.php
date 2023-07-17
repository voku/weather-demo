<?php declare(strict_types=1);

namespace WeatherApp\modules\store\entities;

class Store
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $street,
        public readonly string $houseNo,
        public readonly string $zip,
        public readonly string $city,
        public readonly float $latitude,
        public readonly float $longitude
    ) {
    }
}
