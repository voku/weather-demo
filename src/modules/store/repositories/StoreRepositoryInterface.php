<?php

namespace WeatherApp\modules\store\repositories;

use WeatherApp\framework\RepositoryDataNotFoundException;
use WeatherApp\modules\store\entities\Store;

interface StoreRepositoryInterface
{
    /**
     * @throws RepositoryDataNotFoundException
     */
    public function fetchById(int $id): Store;

    public function fetchByIdIfExists(int $id): ?Store;

    /**
     * @return list<Store>
     */
    public function all(): array;

    public function update(
        int $id,
        string $name,
        string $street,
        string $houseNo,
        string $zip,
        string $city,
        float $latitude,
        float $longitude
    ): bool;
}
