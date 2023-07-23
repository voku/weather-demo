<?php

namespace WeatherApp\framework\storage;

class Database
{
    public readonly \PDO $pdo;

    public function __construct()
    {
        $databaseFile = __DIR__ . '/../../../database/weather.db';
        $this->pdo = new \PDO('sqlite:' .  $databaseFile);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
