<?php

namespace archerbyte;

/**
 * Class DBAttribute
 * 
 * Represents a database table attribute (column) with its properties.
 * Used to define the schema when creating tables or manipulating columns.
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class DBAttribute
{
    /**
     * @var string The name of the attribute/column
     */
    public $name;

    /**
     * @var string The data type of the attribute (e.g. VARCHAR, INT)
     */
    public $type;

    /**
     * @var int The length of the attribute (e.g. 255 for VARCHAR(255))
     */
    public $length;

    /**
     * @var bool Whether the attribute can be NULL
     */
    public $nullable;

    /**
     * @var bool Whether the attribute is a primary key
     */
    public $isPrimaryKey;

    /**
     * Constructor to initialize the attribute
     *
     * @param string $name The attribute name
     * @param string $type The attribute data type
     * @param int $length The length of the attribute
     * @param bool $nullable Whether the attribute is nullable (default false)
     * @param bool $isPrimaryKey Whether the attribute is a primary key (default false)
     */
    public function __construct(string $name, string $type, int $length, bool $nullable = false, bool $isPrimaryKey = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
        $this->nullable = $nullable;
        $this->isPrimaryKey = $isPrimaryKey;
    }

    /**
     * Set the attribute name
     *
     * @param string $name
     * @return DBAttribute
     */
    public function setName($name): DBAttribute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the attribute type
     *
     * @param string $type
     * @return DBAttribute
     */
    public function setType($type): DBAttribute
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the length of the attribute
     *
     * @param int $length
     * @return DBAttribute
     */
    public function setLength($length): DBAttribute
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Mark the attribute as nullable
     *
     * @return DBAttribute
     */
    public function setNullable(): DBAttribute
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Mark the attribute as a primary key
     *
     * @return DBAttribute
     */
    public function setPrimaryKey(): DBAttribute
    {
        $this->isPrimaryKey = true;
        return $this;
    }

    /**
     * Get the attribute name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the attribute type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the attribute length
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Check if the attribute is nullable
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Check if the attribute is a primary key
     *
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->isPrimaryKey;
    }
}
