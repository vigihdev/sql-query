<?php

declare(strict_types=1);

namespace SqlQuery\Contracts;

use InvalidArgumentException;
use SqlQuery\Contracts\ExpressionInterface;

/**
 * ConditionFactoryContract
 *
 * Defines the contract for any class responsible for creating 
 * ExpressionInterface objects (conditions) from an operator and operands.
 */
interface ConditionFactoryContract
{
    /**
     * Membuat objek ExpressionInterface/ConditionInterface yang sesuai 
     * berdasarkan operator dan operan yang diberikan.
     *
     * @param string $operator Operator SQL (misalnya 'AND', '=', 'IN', 'IS NOT NULL')
     * @param array $operands Argumen atau nilai untuk kondisi tersebut
     * @return ExpressionInterface Objek kondisi yang sudah dibuat
     * @throws InvalidArgumentException Jika operator tidak didukung atau argumen tidak valid
     */
    public function create(string $operator, array $operands): ExpressionInterface;
}
