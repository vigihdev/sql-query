<?php

declare(strict_types=1);

namespace SqlQuery;

/**
 * AbstractQuery
 *
 * Abstract base class for SQL query builders with common properties and methods
 *
 */
abstract class AbstractQuery
{

    /**
     * @var string[] $select SELECT columns for the query
     */
    protected $select = [];

    /**
     * @var string $from FROM table name
     */
    protected ?string $from = null;

    /**
     * @var array $where WHERE conditions array
     */
    protected array $where = [];

    /**
     * @var array $join JOIN conditions array
     */
    protected array $join = [];

    /**
     * @var int|null $limit LIMIT value for the query
     */
    protected ?int $limit = null;

    /**
     * @var int|null $offset OFFSET value for the query
     */
    protected ?int $offset = null;

    /**
     * @var string[] $groupBy GROUP BY columns
     */
    protected array $groupBy = [];

    /**
     * @var array $orderBy ORDER BY columns and directions
     */
    protected array $orderBy = [];

    /**
     * @var bool|string|null $distinct DISTINCT flag or expression
     */
    protected ?bool $distinct = null;


    /**
     * Build FROM clause for the query
     *
     * @return string FROM clause SQL
     */
    protected function buildFrom(): string
    {

        $from = $this->from;
        if (!$from) {
            return '';
        }

        return "FROM {$from}";
    }


    /**
     * Build SELECT clause for the query
     *
     * @return string SELECT clause SQL
     */
    protected function buildSelect(): string
    {

        $select = $this->select;
        if (is_array($select)) {
            if (!empty($select)) {
                $result = implode(', ', $select);
                if (is_bool($this->distinct) && $this->distinct) {
                    return "SELECT DISTINCT {$result}";
                }
                return "SELECT {$result}";
            }
            return "SELECT *";
        }
        return '';
    }


    /**
     * Build WHERE clause for the query
     *
     * @return array WHERE clause parts
     */
    protected function buildWhere(): array
    {

        if (!is_array($this->where)) {
            return [];
        }

        $wheres = $this->where;

        if (!empty($wheres)) {
            $where = array_shift($wheres);
            array_unshift($wheres, "WHERE {$where}");
        }

        return $wheres;
    }


    /**
     * Build JOIN clauses for the query
     *
     * @return array JOIN clause parts
     */
    protected function buildJoin(): array
    {

        $join = $this->join;
        return $join;
    }


    /**
     * @param array $columns
     * @return string the GROUP BY clause
     */
    protected function buildGroupBy(array $columns): string
    {

        if (empty($columns)) {
            return '';
        }

        return 'GROUP BY ' . implode(', ', $columns);
    }

    /**
     * @return string the ORDER BY clause built from [[Query::$orderBy]].
     */
    protected function buildOrderBy(): string
    {

        $columns = $this->orderBy;
        if (empty($columns)) {
            return '';
        }

        $orders = [];
        foreach ($columns as $name => $direction) {
            $desc = $direction === SORT_DESC ? ' DESC' : '';
            $asc = $direction === SORT_ASC ? ' ASC' : '';
            $orders[] = $name . "{$desc}{$asc}";
        }

        return 'ORDER BY ' . implode(', ', $orders);
    }



    /**
     * @param int $offset
     * @return string the LIMIT clauses
     */
    protected function buildLimit(): string
    {

        $limit = $this->limit;
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
        }

        return $sql;
    }

    /**
     * @param int $offset
     * @return string the OFFSET clauses
     */
    protected function buildOffset(): string
    {

        $offset = $this->offset;
        $sql = '';
        if ($this->hasOffset($offset)) {
            $sql = 'OFFSET ' . $offset;
        }

        return $sql;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @return string the LIMIT and OFFSET clauses
     */
    protected function buildLimitOffset(int $limit, int $offset): string
    {
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
        }
        if ($this->hasOffset($offset)) {
            $sql .= ' OFFSET ' . $offset;
        }

        return ltrim($sql);
    }

    /**
     * Checks to see if the given limit is effective.
     * @param mixed $limit the given limit
     * @return bool whether the limit is effective
     */
    protected function hasLimit($limit): bool
    {
        return ctype_digit((string)$limit);
    }

    /**
     * Checks to see if the given offset is effective.
     * @param mixed $offset the given offset
     * @return bool whether the offset is effective
     */
    protected function hasOffset($offset): bool
    {
        return ctype_digit((string)$offset) && (string)$offset !== '0';
    }


    /**
     * Filter condition by removing empty values
     *
     * @param mixed $condition Condition to filter
     * @return mixed Filtered condition
     */
    protected function filterCondition($condition)
    {
        if (!is_array($condition)) {
            return $condition;
        }

        if (!isset($condition[0])) {
            // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
            foreach ($condition as $name => $value) {
                if ($this->isEmpty($value)) {
                    unset($condition[$name]);
                }
            }

            return $condition;
        }

        // operator format: operator, operand 1, operand 2, ...

        $operator = array_shift($condition);

        switch (strtoupper($operator)) {
            case 'NOT':
            case 'AND':
            case 'OR':
                foreach ($condition as $i => $operand) {
                    $subCondition = $this->filterCondition($operand);
                    if ($this->isEmpty($subCondition)) {
                        unset($condition[$i]);
                    } else {
                        $condition[$i] = $subCondition;
                    }
                }

                if (empty($condition)) {
                    return [];
                }
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (array_key_exists(1, $condition) && array_key_exists(2, $condition)) {
                    if ($this->isEmpty($condition[1]) || $this->isEmpty($condition[2])) {
                        return [];
                    }
                }
                break;
            default:
                if (array_key_exists(1, $condition) && $this->isEmpty($condition[1])) {
                    return [];
                }
        }

        array_unshift($condition, $operator);

        return $condition;
    }


    /**
     * Check if value is considered empty
     *
     * @param mixed $value Value to check
     * @return bool Whether value is empty
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }
}
