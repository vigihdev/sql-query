<?php


declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\ConditionInterface;

class NullCondition implements ConditionInterface
{

    /**
     * @var string $operator the operator to use. Anything could be used e.g. `IS NULL`, `IS NOT NULL`, etc.
     */
    private $operator;

    /**
     * @var string the column name to the left of [[operator]]
     */
    private $column;

    /**
     * @var string the value to the right of the [[operator]]
     */
    private $value;


    /**
     * NullCondition constructor
     *
     * @param string $column the literal to the left of $operator
     * @param string $operator the operator to use. Anything could be used e.g. `IS NULL`, `IS NOT NULL`, etc.
     */
    public function __construct($operator, $column)
    {

        $this->column = $column;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     *
     * @param string $operator
     * @param string $column
     * @return NullCondition
     */
    public static function fromArrayDefinition($operator, $column): self
    {
        return new static($operator, $column);
    }
}
