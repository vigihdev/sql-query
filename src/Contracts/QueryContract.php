<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

interface QueryContract
{
    /**
     * Add SELECT columns to the query
     *
     * @param string|string[] ...$columns
     * @return static
     */
    public function select(...$columns): static;

    /**
     * Set DISTINCT flag
     *
     * @param bool $value
     * @return static
     */
    public function distinct(bool $value = true): static;

    /**
     * Set the FROM clause
     *
     * @param string $table
     * @return static
     */
    public function from(string $table): static;

    /**
     * Add WHERE condition
     *
     * @param mixed $condition
     * @param array $params
     * @return static
     */
    public function where(mixed $condition, array $params = []): static;

    /**
     * Add AND WHERE condition
     *
     * @param mixed $condition
     * @param array $params
     * @return static
     */
    public function andWhere(mixed $condition, array $params = []): static;

    /**
     * Add OR WHERE condition
     *
     * @param mixed $condition
     * @param array $params
     * @return static
     */
    public function orWhere(mixed $condition, array $params = []): static;

    /**
     * Add filtered comparison condition with operator detection
     *
     * @param string $name
     * @param mixed $value
     * @param string $defaultOperator
     * @return static
     */
    public function andFilterCompare(string $name, mixed $value, string $defaultOperator = '='): static;

    /**
     * Add AND WHERE condition with filtering (ignores empty values)
     *
     * @param array $condition
     * @return static
     */
    public function andFilterWhere(array $condition): static;

    /**
     * Add OR WHERE condition with filtering (ignores empty values)
     *
     * @param array $condition
     * @return static
     */
    public function orFilterWhere(array $condition): static;

    /**
     * Add JOIN clause
     *
     * @param string $type
     * @param string $table
     * @param mixed ...$condition
     * @return static
     */
    public function innerJoin(string $table, ...$condition): static;
    public function leftJoin(string $table, ...$condition): static;
    public function rightJoin(string $table, ...$condition): static;
    public function fullOuterJoin(string $table, ...$condition): static;

    /**
     * Add GROUP BY clause
     *
     * @param mixed $columns
     * @return static
     */
    public function groupBy(mixed $columns): static;

    /**
     * Add ORDER BY clause
     *
     * @param mixed $columns
     * @return static
     */
    public function orderBy(mixed $columns): static;

    /**
     * Set LIMIT
     *
     * @param int $limit
     * @return static
     */
    public function limit(int $limit): static;

    /**
     * Set OFFSET
     *
     * @param int $offset
     * @return static
     */
    public function offset(int $offset): static;

    /**
     * Build query parts
     *
     * @return array
     */
    public function buildParts(): array;

    /**
     * Convert query to SQL string
     *
     * @return string
     */
    public function toSql(): string;
}
