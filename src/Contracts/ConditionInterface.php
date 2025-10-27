<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;


interface ConditionInterface extends ExpressionInterface
{
    /**
     * Creates object by array-definition as described in
     * [Query Builder – Operator format](guide:db-query-builder#operator-format) guide article.
     *
     * @param string $operator operator in uppercase.
     * @param array $operands array of corresponding operands
     *
     * @return self
     * @throws \InvalidParamException if input parameters are not suitable for this condition
     */
    public static function fromArrayDefinition($operator, $operands);
}
