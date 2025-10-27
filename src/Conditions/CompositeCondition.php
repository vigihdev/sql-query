<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\BuilderCondtionInterface;
use SqlQuery\Contracts\CompositeInterface;

/**
 * CompositeCondition
 *
 * Handles complex composite conditions with nested operators and sub-conditions
 *
 */
class CompositeCondition implements CompositeInterface, BuilderCondtionInterface
{

    /**
     * @var string $operator Logical operator (AND, OR, etc.)
     */
    private $operator;

    /**
     * @var array $conditions Array of sub-conditions
     */
    private $conditions = [];

    /**
     * Constructor for composite condition
     *
     * @param string $operator Logical operator (AND, OR, etc.)
     * @param array $conditions Array of sub-conditions
     */
    public function __construct(string $operator, array $conditions)
    {
        $this->operator = strtoupper($operator);
        $this->conditions = $conditions;
    }

    /**
     * Get the logical operator
     *
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Get array of sub-conditions
     *
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Build the composite condition into SQL string
     *
     * @return string Generated SQL string
     */
    public function build(): string
    {
        return $this->buildCondition();
    }


    /**
     * Count number of conditions
     *
     * @return int
     */
    private function count(): int
    {
        return count($this->getConditions());
    }

    /**
     * Shift conditions (placeholder method)
     */
    private function shiffConditions() {}

    /**
     * Build the condition into SQL string with proper parentheses
     *
     * @return string Generated SQL condition
     */
    private function buildCondition(): string
    {

        $conditions = $this->getConditions();
        $operator = $this->getOperator();

        $first = array_shift($conditions);
        $firstBuild = '';
        if ($first instanceof BuilderCondtionInterface) {
            $firstBuild = $first->build();
        }

        $results = [];
        foreach ($conditions as $i => $condition) {
            if ($condition instanceof self) {
                $subConditions = array_map(fn($c) => $c->build(), $condition->getConditions());
                $results[] = implode(" {$condition->getOperator()} ", $subConditions);
                continue;
            }

            if ($condition instanceof BuilderCondtionInterface) {
                $results[] = $condition->build();
            }
        }

        $count = count($conditions);
        $sql = implode(" {$operator} ", $results);
        if ($this->count() === 1) {
            return "{$firstBuild}";
        }

        if ($count === 1 && !$conditions[0] instanceof self) {
            return "{$firstBuild} {$operator} {$sql}";
        }

        return "{$firstBuild} {$operator} ({$sql})";
    }
}
