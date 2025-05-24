<?php

namespace archerbyte;

use Exception;

/**
 * Class DBQ
 * 
 * This class handles SQL database queries and abstracts
 * common database operations for MySQL databases.
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class DBQ
{
    /**
     * @var \PDO|null The PDO database connection instance.
     */
    private $connection;

    /**
     * @var string|null The type of the database, e.g., "MySQL".
     */
    private $databaseType;

    /**
     * @var string|null The current SQL query string.
     */
    private $query;

    /**
     * @var string|null The name of the selected database.
     */
    private $databaseName;

    /**
     * Establishes a connection to a MySQL database using PDO.
     *
     * @param string $host The database host. Default "localhost".
     * @param string $database The database name to connect to.
     * @param string $user The database user. Default "root".
     * @param string $password The password for the database user.
     * @param int $port The database port. Default 3306.
     * @param string $charset The charset for the connection. Default "utf8mb4".
     * @param array $options Optional PDO options.
     * 
     * @throws \Exception If connection fails.
     * 
     * @return void
     */
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

    /**
     * Retrieves a list of tables in the currently selected database.
     *
     * @throws \Exception If query execution fails.
     * 
     * @return array An array of table names (each as a numeric array).
     */
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
     * Creates a table with the specified name and attributes.
     *
     * @param string $tableName The name of the table to create.
     * @param DBAttribute[] $attributes Array of DBAttribute objects defining columns.
     * 
     * @throws \Exception If table creation fails or primary keys are nullable.
     * 
     * @return bool True on success, false on failure.
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

    /**
     * Sets the SQL query string to be executed later.
     *
     * @param string $query The SQL query string.
     * 
     * @return $this For method chaining.
     */
    public function query($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Executes the current SQL query and fetches all results.
     *
     * @throws \Exception If execution or fetching fails.
     * 
     * @return array The result set as an associative array.
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

    /**
     * Validates that a table exists in the current database.
     *
     * @param string $table The table name to check.
     * 
     * @throws \Exception If the table does not exist.
     * 
     * @return void
     */
    public function validateTableExistence($table)
    {
        $tables = $this->getTables();

        if (!in_array($table, array_column($tables, 0))) {
            throw new \Exception("This table does not exist in the database");
        }
    }

    /**
     * Inserts a new row into the specified table.
     *
     * @param string $table The table name.
     * @param array $data Associative array of column => value pairs.
     * 
     * @throws \Exception If table doesn't exist or insert fails.
     * 
     * @return void
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

    /**
     * Selects rows from a table with optional columns and pagination.
     *
     * @param string $table The table name.
     * @param string[] $attributes Optional list of columns to retrieve; defaults to all columns.
     * @param int $start The starting offset (default 0).
     * @param int $end The ending offset (default 100).
     * 
     * @throws \Exception If table doesn't exist or arguments are invalid.
     * 
     * @return array Result rows as associative arrays.
     */
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

    /**
     * Selects rows using a Query Builder object to build the WHERE clause.
     *
     * @param string $table The table name.
     * @param QB $qb The query builder instance with conditions.
     * 
     * @return array Result rows as associative arrays.
     */
    public function selectWithQB(string $table, QB $qb)
    {
        $whereClause = $qb->build();
        $query = "SELECT * FROM {$table} WHERE {$whereClause}";
        return $this->query($query)->exec();
    }

    /**
     * Updates rows matching the query builder conditions with the provided data.
     *
     * @param string $table The table name.
     * @param QB $qb The query builder with WHERE clause.
     * @param array $data Associative array of column => value for updating.
     * 
     * @return bool True on success, false otherwise.
     */
    public function updateWithQB(string $table, QB $qb, array $data)
    {
        $setParts = [];
        foreach ($data as $column => $value) {
            $escapedValue = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $setParts[] = "{$column} = {$escapedValue}";
        }
        $setClause = implode(", ", $setParts);

        $whereClause = $qb->build();
        $query = "UPDATE {$table} SET {$setClause} WHERE {$whereClause}";

        return $this->connection->prepare($query)->execute();
    }

    /**
     * Deletes rows from a table matching the query builder conditions.
     *
     * @param string $table The table name.
     * @param QB $qb The query builder with WHERE clause.
     * 
     * @return int|false Number of affected rows, or false on failure.
     */
    public function deleteWithQB(string $table, QB $qb)
    {
        $whereClause = $qb->build();
        $query = "DELETE FROM {$table} WHERE {$whereClause}";
        return $this->query($query)->exec();
    }
}
