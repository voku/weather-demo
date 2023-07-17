<?php

namespace WeatherApp\framework;

class Database
{
    public readonly \PDO $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('sqlite:../database/weather.db');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
