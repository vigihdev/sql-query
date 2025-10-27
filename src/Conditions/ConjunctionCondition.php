<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\ConditionInterface;

/**
 * ConjunctionCondition
 *
 * Abstract base class for logical conjunction operators (AND, OR)
 *
 */
abstract class ConjunctionCondition implements ConditionInterface
{
    /**
     * @var mixed[] Array of expressions to be combined
     */
    protected $expressions;


    /**
     * Constructor for conjunction condition
     *
     * @param mixed $expressions Array of expressions to combine
     */
    public function __construct($expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * Get all expressions in this conjunction
     *
     * @return mixed[] Array of expressions
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * Returns the operator that is represented by this condition class, e.g. `AND`, `OR`.
     *
     * @return string The logical operator
     */
    abstract public function getOperator();

    /**
     * Create conjunction condition from array definition
     *
     * @param string $operator The operator name
     * @param array $operands Array of operands
     * @return self
     */
    public static function fromArrayDefinition($operator, $operands)
    {
        return new static($operands);
    }
}
