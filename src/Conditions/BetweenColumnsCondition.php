<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\{ConditionInterface, BuilderCondtionInterface};

/**
 * BetweenColumnsCondition
 *
 * Represents BETWEEN condition for comparing values between two columns
 *
 */
class BetweenColumnsCondition implements ConditionInterface, BuilderCondtionInterface
{
    /**
     * @var string $operator the operator to use (e.g. `BETWEEN` or `NOT BETWEEN`)
     */
    private $operator;

    /**
     * @var mixed the value to compare against
     */
    private $value;

    /**
     * @var string|ExpressionInterface|Query the column name or expression that is a beginning of the interval
     */
    private $intervalStartColumn;

    /**
     * @var string|ExpressionInterface|Query the column name or expression that is an end of the interval
     */
    private $intervalEndColumn;


    /**
     * Creates a condition with the `BETWEEN` operator.
     *
     * @param mixed the value to compare against
     * @param string $operator the operator to use (e.g. `BETWEEN` or `NOT BETWEEN`)
     * @param string|ExpressionInterface $intervalStartColumn the column name or expression that is a beginning of the interval
     * @param string|ExpressionInterface $intervalEndColumn the column name or expression that is an end of the interval
     */
    public function __construct($value, $operator, $intervalStartColumn, $intervalEndColumn)
    {
        $this->value = $value;
        $this->operator = $operator;
        $this->intervalStartColumn = $intervalStartColumn;
        $this->intervalEndColumn = $intervalEndColumn;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string|ExpressionInterface|Query
     */
    public function getIntervalStartColumn()
    {
        return $this->intervalStartColumn;
    }

    /**
     * @return string|ExpressionInterface|Query
     */
    public function getIntervalEndColumn()
    {
        return $this->intervalEndColumn;
    }

    /**
     * @return self
     * @throws \InvalidArgumentException if wrong number of operands have been given.
     */
    public static function fromArrayDefinition($operator, $operands)
    {
        if (!isset($operands[0], $operands[1], $operands[2])) {
            throw new \InvalidArgumentException("Operator '$operator' requires three operands.");
        }

        return new static($operands[0], $operator, $operands[1], $operands[2]);
    }

    /**
     * Build the simple condition into SQL string
     *
     * @return string Generated SQL condition
     */
    public function build(): string
    {
        return "{$this->value} {$this->operator} {$this->intervalStartColumn} AND {$this->intervalEndColumn}";
    }
}
