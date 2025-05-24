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
    private $query;

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

    /**
     * @param DBAttribute[] $attributes
     */
    public function createTable(string $tableName, array $attributes):bool
    {
        try {
            if ($this->databaseType === "MySQL") {
                $attributeQuery = "";
                foreach ($attributes as $attribute) {
                    $attributeQuery .= ("{$attribute->getName()} {$attribute->getType()}({$attribute->getLength()})");

                    if ($attribute->isPrimaryKey() && $attribute->isNullable()) {
                        throw new \Exception("Primary keys cannot be nullable");
                    }

                    if ($attribute->isPrimaryKey()) {
                        $attributeQuery .= " PRIMARY KEY AUTO_INCREMENT";
                    } elseif ($attribute->isNullable()) {
                        $attributeQuery .= " NULL";
                    } else {
                        $attributeQuery .= " NOT NULL";
                    }

                    $attributeQuery .= ",\n";
                }
                $attributeQuery = substr($attributeQuery, 0, -2);
                $query = "CREATE TABLE IF NOT EXISTS {$tableName} ({$attributeQuery});";

                return $this->connection->exec($query) !== false;
            }
            return false;
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function query($query)
    {
        $this->query = $query;
        return $this;
    }

    public function queryWithQB($qb) {}

    public function exec()
    {
        try {
            if ($this->databaseType === "MySQL") {
                $stmt = $this->connection->query($this->query);
                $result = $stmt->fetchAll();

                return $result;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
