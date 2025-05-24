<?php

namespace archerbyte;

use Exception;

/**
 * Class QB
 * 
 * This class is a query builder to build SQL queries
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

    private array $conditions = [];

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

    public function addQB(string $logic, QB $qb): self
    {
        $this->conditions[] = [
            'type'  => 'group',
            'logic' => $logic,
            'qb'    => $qb
        ];
        return $this;
    }

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
