<?php

namespace WeatherApp\modules\store\repositories;

use WeatherApp\framework\exceptions\RepositoryDataNotFoundException;
use WeatherApp\framework\storage\Database;
use WeatherApp\modules\store\entities\Store;

class StoreRepositoryPdo implements StoreRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->pdo;
    }

    /**
     * @throws RepositoryDataNotFoundException
     */
    public function fetchById(int $id): Store
    {
        $return = $this->fetchByIdIfExists($id);
        if (!$return) {
            throw new RepositoryDataNotFoundException();
        }

        return $return;
    }

    public function fetchByIdIfExists(int $id): ?Store
    {
        $sql = sprintf('SELECT * FROM stores WHERE id = %d', (int) $id);

        $sth = $this->pdo->query($sql);
        $row = $sth->fetch();
        if ($row) {
            return new Store(
                $row['id'],
                $row['name'],
                $row['street'],
                $row['houseNo'],
                $row['zip'],
                $row['city'],
                $row['latitude'],
                $row['longitude'],
            );
        }

        return null;
    }

    /**
     * @return list<Store>
     */
    public function all(): array
    {
        $sql = 'SELECT * FROM stores';

        $stores = [];
        foreach ($this->pdo->query($sql) as $row) {
            $stores[] = new Store(
                $row['id'],
                $row['name'],
                $row['street'],
                $row['houseNo'],
                $row['zip'],
                $row['city'],
                $row['latitude'],
                $row['longitude'],
            );
        }

        return $stores;
    }

    public function update(
        int $id,
        string $name,
        string $street,
        string $houseNo,
        string $zip,
        string $city,
        float $latitude,
        float $longitude
    ): bool {
        $smt = $this->pdo->prepare('
            UPDATE stores
            SET name=:name,street=:street,houseNo=:houseNo,zip=:zip,city=:city,latitude=:latitude,longitude=:longitude
            WHERE id=:id
        ');
        $smt->bindParam(':id', $id);
        $smt->bindParam(':name', $name);
        $smt->bindParam(':street', $street);
        $smt->bindParam(':houseNo', $houseNo);
        $smt->bindParam(':zip', $zip);
        $smt->bindParam(':city', $city);
        $smt->bindParam(':latitude', $latitude);
        $smt->bindParam(':longitude', $longitude);

        return $smt->execute();
    }
}
