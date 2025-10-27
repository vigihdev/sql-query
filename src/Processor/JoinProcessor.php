<?php

declare(strict_types=1);

namespace SqlQuery\Processor;

use SqlQuery\Conditions\JoinCondition;
use SqlQuery\Contracts\{BuilderCondtionInterface, CompositeInterface, ConditionInterface, ExpressionInterface, ProcessorInterface};

/**
 * JoinProcessor
 *
 * Processes JOIN conditions and builds SQL JOIN statements
 *
 */
final class JoinProcessor implements ProcessorInterface
{

    /**
     * @var ConditionInterface $conditions JOIN conditions to process
     */
    private ConditionInterface $conditions;

    /**
     * @var array $params Query parameters for binding
     */

    private array $params = [];

    /**
     * Constructor for JOIN processor
     *
     * @param string|array|ExpressionInterface $condition JOIN condition
     * @param array $params Query parameters for binding
     */
    public function __construct($condition, array $params = [])
    {

        if ($condition instanceof ExpressionInterface) {
            $this->conditions = $condition;
        }

        if (is_array($condition)) {
            $operator = array_shift($condition);
            $this->conditions = new JoinCondition($operator, $condition[0], $condition[1], $condition[2]);
        }

        if (is_string($condition)) {
            return;
        }
    }


    /**
     * Build SQL JOIN clause from processed conditions
     *
     * @return string Generated JOIN clause SQL
     */
    public function build(): string
    {

        $condition = $this->conditions;
        if ($condition instanceof BuilderCondtionInterface) {
            return $condition->build();
        }

        return "";
    }

    /**
     * Get processed JOIN conditions
     *
     * @return ExpressionInterface|CompositeInterface|ConditionInterface
     */
    public function getConditions(): ExpressionInterface|CompositeInterface|ConditionInterface
    {
        return $this->conditions;
    }

    /**
     * Get query parameters for binding
     *
     * @return array Query parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
