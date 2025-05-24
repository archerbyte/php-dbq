<?php

namespace archerbyte;

use Exception;

/**
 * Class Api
 * 
 * This class handles the request for sql database queries
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class DBQ
{
    private $connection;
    private $databaseType;

    public function connectMySQL($host = "localhost", $database, $user = "root", $password, $port = 3306, $charset = "utf8mb4", $options = [])
    {
        $this->databaseType = "MySQL";

        $options = $options ?? [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        try {
            $this->connection = new \PDO($dsn, $user, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function getTables(): array
    {
        try {
            if ($this->databaseType === "MySQL") {
                $stmt = $this->connection->query("SHOW TABLES");

                $tables = $stmt->fetchAll();

                return $tables;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function query(){

    }

    public function queryWithQB($qb){
        
    }

    public function exec(){

    }
}
