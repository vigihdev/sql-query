<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\ExpressionInterface;

class IsCondition implements ExpressionInterface
{

    /**
     * @var string $operator the operator to use (e.g. `IS` or `IS NOT`)
     */
    private $operator;

    /**
     * @var string the column name. If it is an array, a composite `IS` condition
     * will be generated.
     */
    private $column;

    /**
     * @var null|string 
     */
    private $value;

    /**
     *
     * @param string the column name
     * @param string $operator the operator to use (e.g. `IS` or `IS NOT`)
     * @param null an array of values that [[column]] value should be among. If it is an empty array the generated
     */
    public function __construct($column, $operator, $value = null)
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function getValue()
    {
        return $this->value;
    }
}
