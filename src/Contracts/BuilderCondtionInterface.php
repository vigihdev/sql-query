<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

/**
 * BuilderConditionInterface
 *
 * Defines contract for objects that can build themselves into SQL strings
 *
 */
interface BuilderCondtionInterface
{
    /**
     * Build the object into SQL string representation
     *
     * @return string Generated SQL string
     */
    public function build(): string;
}
