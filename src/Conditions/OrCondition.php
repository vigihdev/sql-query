<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

/**
 * OrCondition
 *
 * Represents OR logical operator for combining multiple conditions
 *
 */
class OrCondition extends ConjunctionCondition
{
    /**
     * Returns the operator that is represented by this condition class, e.g. `AND`, `OR`.
     *
     * @return string
     */
    public function getOperator()
    {
        return 'OR';
    }
}
