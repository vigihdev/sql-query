<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\ConditionInterface;

/**
 * NotCondition
 *
 * Represents NOT logical operator for negating conditions
 *
 */
class NotCondition implements ConditionInterface
{
    /**
     * @var mixed the condition to be negated
     */
    private $condition;


    /**
     * NotCondition constructor.
     *
     * @param mixed $condition the condition to be negated
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get the condition to be negated
     *
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Create NotCondition from array definition
     *
     * @param string $operator The operator name
     * @param array $operands Array of operands
     * @return self
     * @throws \InvalidArgumentException if wrong number of operands have been given.
     */
    public static function fromArrayDefinition($operator, $operands)
    {
        if (count($operands) !== 1) {
            throw new \InvalidArgumentException("Operator '$operator' requires exactly one operand.");
        }

        return new static(array_shift($operands));
    }
}
