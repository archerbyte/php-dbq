<?php

namespace archerbyte;

use Exception;

/**
 * Class DBQ
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
    private $databaseName;

    public function connectMySQL($host = "localhost", $database, $user = "root", $password, $port = 3306, $charset = "utf8mb4", $options = [])
    {
        $this->databaseType = "MySQL";
        $this->databaseName = $database;

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

                $tables = $stmt->fetchAll(\PDO::FETCH_NUM);

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
    public function createTable(string $tableName, array $attributes): bool
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

    /**
     * 
     */
    public function exec()
    {
        try {
            if ($this->databaseType === "MySQL") {
                $stmt = $this->connection->query($this->query);
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                return $result;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function validateTableExistence($table)
    {
        $tables = $this->getTables();

        if (!in_array($table, array_column($tables, 0))) {
            throw new \Exception("This table does not exist in the database");
        }
    }

    /**
     * @param $data Array<object>|obect
     */
    public function insert(string $table, array $data)
    {
        $this->validateTableExistence($table);

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $attributes = implode(', ', $columns);
        $values = implode(', ', $placeholders);

        $query = "INSERT INTO {$table} ({$attributes}) VALUES ({$values})";

        $stmt = $this->connection->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
    }


    public function select(string $table, array $attributes = [], int $start = 0, int $end = 100)
    {
        $this->validateTableExistence($table);

        $limit = $end - $start;

        if ($limit <= 0) {
            throw new \InvalidArgumentException("End must be greater than start.");
        }

        $columns = '*';
        if (!empty($attributes)) {
            foreach ($attributes as $attr) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $attr)) {
                    throw new \InvalidArgumentException("Invalid column name: {$attr}");
                }
            }
            $columns = implode(', ', $attributes);
        }

        $query = "SELECT {$columns} FROM {$table} LIMIT :limit OFFSET :offset";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $start, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function selectWithQB(string $table, QB $qb)
    {
        $whereClause = $qb->build();
        $query = "SELECT * FROM {$table} WHERE {$whereClause}";
        return $this->query($query)->exec();
    }
}
