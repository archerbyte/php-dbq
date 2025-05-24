<?php

namespace archerbyte;


/**
 * Class DBAttribute
 * 
 * This class handles the request for sql database queries
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class DBAttribute
{
    public $name;
    public $type;
    public $length;
    public $nullable;
    public $isPrimaryKey;

    public function __construct(string $name, string $type, int $length, bool $nullable = false, bool $isPrimaryKey = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
        $this->nullable = $nullable;
        $this->isPrimaryKey = $isPrimaryKey;
    }

    public function setName($name): DBAttribute
    {
        $this->name = $name;
        return $this;
    }

    public function setType($type): DBAttribute
    {
        $this->type = $type;
        return $this;
    }

    public function setLength($length): DBAttribute
    {
        $this->length = $length;
        return $this;
    }

    public function setNullable(): DBAttribute
    {
        $this->nullable = true;
        return $this;
    }

    public function setPrimaryKey(): DBAttribute
    {
        $this->isPrimaryKey = true;
        return $this;
    }



    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isPrimaryKey(): bool
    {
        return $this->isPrimaryKey;
    }
}
