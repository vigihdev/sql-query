<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

/**
 * AndCondition
 *
 * Represents AND logical operator for combining multiple conditions
 *
 */
class AndCondition extends ConjunctionCondition
{
    /**
     * Returns the operator that is represented by this condition class, e.g. `AND`, `OR`.
     *
     * @return string
     */
    public function getOperator()
    {
        return 'AND';
    }
}
