<?php

namespace archerbyte;

use Exception;

/**
 * Class QB
 * 
 * A query builder class for constructing SQL WHERE conditions dynamically.
 * Supports logical operators, nested query groups, and common SQL operators.
 * 
 * Example usage:
 * ```php
 * $qb = new QB();
 * $qb->add('age', 30, QB::GT)
 *    ->add('name', 'John', QB::LIKE, QB::AND)
 *    ->addQB(QB::OR, (new QB())->add('status', 'active'));
 * $sqlWhere = $qb->build();
 * ```
 * 
 * @package PHP-DBQ
 * @author archerbyte
 * @link https://archerbyte.dev
 * @license MIT
 */
class QB
{
    public const EQ       = '=';
    public const NEQ      = '!=';
    public const GT       = '>';
    public const GTE      = '>=';
    public const LT       = '<';
    public const LTE      = '<=';
    public const LIKE     = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const IN       = 'IN';
    public const NOT_IN   = 'NOT IN';
    public const IS_NULL  = 'IS NULL';
    public const IS_NOT_NULL = 'IS NOT NULL';

    public const AND = 'AND';
    public const OR  = 'OR';

    /**
     * @var array List of conditions and groups for the query builder
     */
    private array $conditions = [];

    /**
     * Add a condition to the query builder
     *
     * @param string $field The database field/column name
     * @param mixed $value The value to compare against
     * @param string $operator SQL comparison operator (default =)
     * @param string $logic Logical operator to combine with previous conditions (AND/OR)
     * 
     * @return self Returns the QB instance for method chaining
     */
    public function add(string $field, $value, string $operator = self::EQ, string $logic = self::AND): self
    {
        $this->conditions[] = [
            'type'     => 'condition',
            'field'    => $field,
            'value'    => $value,
            'operator' => $operator,
            'logic'    => $logic
        ];
        return $this;
    }

    /**
     * Add a nested query builder group with a logical operator
     *
     * @param string $logic Logical operator to combine this group (AND/OR)
     * @param QB $qb A nested QB instance representing a grouped condition set
     * 
     * @return self Returns the QB instance for method chaining
     */
    public function addQB(string $logic, QB $qb): self
    {
        $this->conditions[] = [
            'type'  => 'group',
            'logic' => $logic,
            'qb'    => $qb
        ];
        return $this;
    }

    /**
     * Build the SQL query string for the current conditions
     *
     * This method generates the SQL WHERE clause parts without the 'WHERE' keyword.
     * It handles proper escaping and formatting of values and supports nested groups.
     * 
     * @throws Exception If a nested QB instance is used as a value, which is invalid
     * 
     * @return string The constructed SQL query condition string
     */
    public function build(): string
    {
        $queryParts = [];

        foreach ($this->conditions as $index => $condition) {
            if ($condition['type'] === 'condition') {
                $field = $condition['field'];
                $operator = $condition['operator'];
                $value = $condition['value'];

                if ($value instanceof QB) {
                    throw new Exception("Invalid QB usage: Nested QB passed as value.");
                }

                if (in_array($operator, [self::IS_NULL, self::IS_NOT_NULL])) {
                    $clause = "{$field} {$operator}";
                } elseif (in_array($operator, [self::IN, self::NOT_IN]) && is_array($value)) {
                    $escaped = array_map(fn($v) => is_numeric($v) ? $v : "'" . addslashes($v) . "'", $value);
                    $clause = "{$field} {$operator} (" . implode(", ", $escaped) . ")";
                } else {
                    $value = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
                    $clause = "{$field} {$operator} {$value}";
                }

                $queryParts[] = $index > 0 ? "{$condition['logic']} {$clause}" : $clause;
            } elseif ($condition['type'] === 'group') {
                $logic = $condition['logic'];
                $subQB = $condition['qb'];
                $groupSql = $subQB->build();
                $queryParts[] = "{$logic} ({$groupSql})";
            }
        }

        return implode(" ", $queryParts);
    }
}
