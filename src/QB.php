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
    private array $conditions = [];

    public function add(string $field, $value, string $operator = '=', string $logic = 'AND'): self
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
                    throw new \Exception("Invalid QB usage: Nested QB passed as value.");
                }

                $value = is_numeric($value) ? $value : "'" . addslashes($value) . "'";

                if ($index > 0) {
                    $queryParts[] = "{$condition['logic']} {$field} {$operator} {$value}";
                } else {
                    $queryParts[] = "{$field} {$operator} {$value}";
                }
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
