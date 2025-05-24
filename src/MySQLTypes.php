<?php

namespace archerbyte;

/**
 * Class MySQLTypes
 * 
 * Provides constants for common MySQL data types to use in query building or schema definitions.
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class MySQLTypes
{
    /**
     * Integer type.
     */
    public const INT = 'INT';

    /**
     * Tiny integer type (1 byte).
     */
    public const TINYINT = 'TINYINT';

    /**
     * Small integer type (2 bytes).
     */
    public const SMALLINT = 'SMALLINT';

    /**
     * Medium integer type (3 bytes).
     */
    public const MEDIUMINT = 'MEDIUMINT';

    /**
     * Big integer type (8 bytes).
     */
    public const BIGINT = 'BIGINT';

    /**
     * Variable-length character string.
     */
    public const VARCHAR = 'VARCHAR';

    /**
     * Fixed-length character string.
     */
    public const CHAR = 'CHAR';

    /**
     * Text type with a maximum length of 65,535 characters.
     */
    public const TEXT = 'TEXT';

    /**
     * Medium text type with a maximum length of 16,777,215 characters.
     */
    public const MEDIUMTEXT = 'MEDIUMTEXT';

    /**
     * Long text type with a maximum length of 4,294,967,295 characters.
     */
    public const LONGTEXT = 'LONGTEXT';

    /**
     * Date type (YYYY-MM-DD).
     */
    public const DATE = 'DATE';

    /**
     * Date and time type (YYYY-MM-DD HH:MM:SS).
     */
    public const DATETIME = 'DATETIME';

    /**
     * Timestamp type.
     */
    public const TIMESTAMP = 'TIMESTAMP';

    /**
     * Time type (HH:MM:SS).
     */
    public const TIME = 'TIME';

    /**
     * Year type (4-digit year).
     */
    public const YEAR = 'YEAR';

    /**
     * Floating-point type.
     */
    public const FLOAT = 'FLOAT';

    /**
     * Double precision floating-point type.
     */
    public const DOUBLE = 'DOUBLE';

    /**
     * Fixed-point decimal type.
     */
    public const DECIMAL = 'DECIMAL';

    /**
     * Binary large object.
     */
    public const BLOB = 'BLOB';

    /**
     * Medium binary large object.
     */
    public const MEDIUMBLOB = 'MEDIUMBLOB';

    /**
     * Long binary large object.
     */
    public const LONGBLOB = 'LONGBLOB';

    /**
     * Enumerated type.
     */
    public const ENUM = 'ENUM';

    /**
     * Set type (a string object that can have zero or more values).
     */
    public const SET = 'SET';
}
