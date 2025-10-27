<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\BuilderCondtionInterface;
use SqlQuery\Contracts\ConditionInterface;

/**
 * SimpleCondition
 *
 * Represents simple comparison conditions (column operator value)
 *
 */
class SimpleCondition implements ConditionInterface, BuilderCondtionInterface
{


    /**
     * @var string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     */
    private $operator;

    /**
     * @var mixed $column the column name to the left of [[operator]]
     */
    private $column;

    /**
     * @var mixed $value the value to the right of the [[operator]]
     */
    private $value;


    /**
     * SimpleCondition constructor
     *
     * @param mixed $column the literal to the left of $operator
     * @param string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     * @param mixed $value the literal to the right of $operator
     */
    public function __construct($column, $operator, $value)
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * Get the comparison operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Get the column name
     *
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Get the comparison value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Build the simple condition into SQL string
     *
     * @return string Generated SQL condition
     */
    public function build(): string
    {
        $value = ParseValueType::parse($this->value);
        return "{$this->column} {$this->operator} {$value}";
    }

    /**
     * Create simple condition from array definition
     *
     * @param string $operator The comparison operator
     * @param array $operands Array containing column and value
     * @return self
     * @throws \InvalidArgumentException if wrong number of operands have been given.
     */
    public static function fromArrayDefinition($operator, $operands)
    {
        if (count($operands) !== 2) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }

        return new static($operands[0], $operator, $operands[1]);
    }
}
