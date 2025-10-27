<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

/**
 * ProcessorInterface
 *
 * Defines contract for query processors that build SQL strings
 *
 */
interface ProcessorInterface
{
    /**
     * Process and build SQL string
     *
     * @return string Generated SQL string
     */
    public function build(): string;
}
