<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

/**
 * ExpressionBuilderInterface
 *
 * Defines contract for building raw SQL expressions from expression objects
 *
 */
interface ExpressionBuilderInterface
{
    /**
     * Method builds the raw SQL from the $expression that will not be additionally
     * escaped or quoted.
     *
     * @param ExpressionInterface $expression the expression to be built.
     * @param array $params the binding parameters.
     * @return string the raw SQL that will not be additionally escaped or quoted.
     */
    public function build(ExpressionInterface $expression, array &$params = []);
}
