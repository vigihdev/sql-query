<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

use InvalidArgumentException;
use SqlQuery\Contracts\ExpressionInterface;

interface ConditionParserContract
{
    /**
     * Metode utama untuk parsing berbagai format kondisi.
     *
     * @param mixed $condition Input kondisi (string, array hash, atau array operator)
     * @return ExpressionInterface Objek kondisi yang sudah diproses
     * @throws InvalidArgumentException
     */
    public function parse(mixed $condition): ExpressionInterface;
}
