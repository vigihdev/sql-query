<?php

declare(strict_types=1);

namespace SqlQuery;

use SqlQuery\Contracts\QueryContract;
use SqlQuery\Processor\{JoinProcessor, WhereProcessor};

/**
 * Query
 *
 * Main query builder class for constructing MySQL SELECT queries
 *
 */
class Query extends AbstractQuery implements QueryContract
{


    /**
     * Add SELECT columns to the query
     *
     * @param string|string[] ...$columns Column names to select
     * @return self
     */
    public function select(...$columns): static
    {

        $select = $columns;
        $this->select = array_merge($this->select, ...$columns);
        return $this;
    }


    /**
     * Add columns to existing SELECT clause
     *
     * @param mixed $columns Columns to add
     * @return self
     */
    private function addSelect($columns): static
    {
        return $this;
    }

    /**
     * Set DISTINCT flag for the query
     *
     * @param bool $value Whether to use DISTINCT
     * @return self
     */
    public function distinct($value = true): static
    {
        $this->distinct = $value;
        return $this;
    }

    /**
     *
     * @param string $table
     * @return self
     */
    public function from(string $table): static
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Add WHERE condition to the query
     *
     * @param mixed $condition WHERE condition
     * @param array $params Query parameters for binding
     * @return self
     */
    public function where($condition, array $params = []): static
    {

        $where = new WhereProcessor($condition, $params);
        if (is_array($this->where) && !empty($this->where)) {
            $this->where = array_merge($this->where, ['AND ' . $where->build()]);
            return $this;
        }
        $this->where[] = $where->build();
        return $this;
    }

    /**
     * Add AND WHERE condition to existing WHERE clause
     *
     * @param mixed $condition WHERE condition
     * @param array $params Query parameters for binding
     * @return self
     */
    public function andWhere($condition, $params = []): static
    {

        // Handle string condition with parameters
        if (is_string($condition) && !empty($params)) {
            return $this;
        }

        // Handle array condition
        if (is_array($condition)) {
            $where = new WhereProcessor($condition, $params);
            $this->where = array_merge($this->where, ['AND ' . $where->build()]);
        }

        return $this;
    }


    /**
     * Add OR WHERE condition to existing WHERE clause
     *
     * @param mixed $condition WHERE condition
     * @param array $params Query parameters for binding
     * @return self
     */
    public function orWhere($condition, $params = []): static
    {

        // Handle string condition with parameters
        if (is_string($condition) && !empty($params)) {
            return $this;
        }

        // Handle array condition
        if (is_array($condition)) {
            $where = new WhereProcessor($condition, $params);
            $this->where = array_merge($this->where, ['OR ' . $where->build()]);
        }

        return $this;
    }

    /**
     * Add filtered comparison condition with operator detection
     *
     * @param string $name Column name
     * @param mixed $value Value to compare (can include operator prefix)
     * @param string $defaultOperator Default comparison operator
     * @return self
     */
    public function andFilterCompare($name, $value, $defaultOperator = '='): static
    {
        if (preg_match('/^(<>|>=|>|<=|<|=)/', (string)$value, $matches)) {
            $operator = $matches[1];
            $value = substr($value, strlen($operator));
        } else {
            $operator = $defaultOperator;
        }

        return $this->andFilterWhere([$operator, $name, $value]);
    }

    /**
     * Add AND WHERE condition with filtering (ignores empty values)
     *
     * @param array $condition Hash array of conditions
     * @return self
     */
    public function andFilterWhere(array $condition): static
    {

        $condition = $this->filterCondition($condition);
        if (is_array($condition)) {
            $this->andWhere($condition);
        }

        return $this;
    }


    /**
     * Add OR WHERE condition with filtering (ignores empty values)
     *
     * @param array $condition Hash array of conditions
     * @return self
     */
    public function orFilterWhere(array $condition): static
    {

        $condition = $this->filterCondition($condition);
        if (is_array($condition)) {
            $this->orWhere($condition);
        }
        return $this;
    }

    /**
     * Add JOIN clause to the query
     *
     * @param string $type JOIN type (INNER, LEFT, RIGHT, etc.)
     * @param string $table Table name to join
     * @param mixed ...$condition JOIN conditions
     * @return self
     */
    private function join(string $type, string $table, ...$condition): static
    {

        if (count($condition) === 2) {
            array_unshift($condition, ...[$type, $table]);
            $join = new JoinProcessor($condition);
            $this->join = array_merge($this->join, [$join->build()]);
            return $this;
        }

        return $this;
    }

    /**
     * Add FULL OUTER JOIN to the query
     *
     * @param string $table Table name to join
     * @param mixed ...$condition JOIN conditions
     * @return self
     */
    public function fullOuterJoin(string $table, ...$condition): static
    {
        return $this->join('FULL OUTER JOIN', $table, ...$condition);
    }


    /**
     * Add INNER JOIN to the query
     *
     * @param string $table Table name to join
     * @param mixed ...$condition JOIN conditions
     * @return self
     */
    public function innerJoin(string $table, ...$condition): static
    {
        return $this->join('INNER JOIN', $table, ...$condition);
    }

    /**
     * Add LEFT JOIN to the query
     *
     * @param string $table Table name to join
     * @param mixed ...$condition JOIN conditions
     * @return self
     */
    public function leftJoin(string $table, ...$condition): static
    {
        return $this->join('LEFT JOIN', $table, ...$condition);
    }

    /**
     * Add RIGHT JOIN to the query
     *
     * @param string $table Table name to join
     * @param mixed ...$condition JOIN conditions
     * @return self
     */
    public function rightJoin(string $table, ...$condition): static
    {
        return $this->join('RIGHT JOIN', $table, ...$condition);
    }


    /**
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }


    /**
     * Add GROUP BY clause to the query
     *
     * @param mixed $columns Columns to group by
     * @return self
     */
    public function groupBy($columns): static
    {
        if (!is_array($columns) && !is_null($columns)) {
            $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        }

        if (is_array($columns) && !empty($columns)) {
            $this->groupBy = array_merge($this->groupBy, $columns);
        }
        return $this;
    }

    /**
     * Add ORDER BY clause to the query
     *
     * @param mixed $columns Columns to order by
     * @return self
     */
    public function orderBy($columns): static
    {

        if (is_null($columns) || empty($columns)) {
            return $this;
        }

        if (is_array($columns)) {
            $orderBy = [];

            foreach ($columns as $name => $direction) {
                $direction = is_string($direction) ? strtoupper($direction) : $direction;
                $validAsc = ['ASC', SORT_ASC, 'SORT_ASC'];
                $validDesc = ['DESC', SORT_DESC, 'SORT_DESC'];

                if (in_array($direction, $validAsc)) {
                    $orderBy[$name] = SORT_ASC;
                }

                if (in_array($direction, $validDesc)) {
                    $orderBy[$name] = SORT_DESC;
                }
            }
            $this->orderBy = $orderBy;
            return $this;
        }

        $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $orderBy = [];
        foreach ($columns as $column) {
            if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $orderBy[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            } else {
                $orderBy[$column] = SORT_ASC;
            }
        }

        $this->orderBy = $orderBy;
        return $this;
    }


    /**
     *
     * @return array
     */
    public function buildParts(): array
    {

        $builder = [];
        $select = $this->buildSelect();
        $from = $this->buildFrom();
        $join = $this->buildJoin();
        $where = $this->buildWhere();
        $limit = $this->buildLimit();
        $offset = $this->buildOffset();
        $orderBy = $this->buildOrderBy();

        array_push($builder, $select, $from, ...$join, ...$where, ...[$orderBy, $limit, $offset]);

        $builder = array_filter($builder, function ($value) {
            return is_string($value) && strlen($value) > 0;
        });

        return $builder;
    }

    /**
     *
     * @return string
     */
    public function toSql(): string
    {
        return implode(' ', $this->buildParts());
    }
}
