<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

/**
 * CompositeInterface
 *
 * Defines contract for composite query conditions with operator and sub-conditions
 *
 */
interface CompositeInterface extends ExpressionInterface
{
    /**
     * Get the logical operator for combining conditions
     *
     * @return string Logical operator (AND, OR, etc.)
     */
    public function getOperator(): string;

    /**
     * Get array of sub-conditions
     *
     * @return array Array of condition objects
     */
    public function getConditions(): array;
}
