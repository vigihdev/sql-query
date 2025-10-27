<?php


declare(strict_types=1);

namespace SqlQuery\Conditions;

use SqlQuery\Contracts\BuilderCondtionInterface;
use SqlQuery\Contracts\ConditionInterface;


class JoinCondition implements ConditionInterface, BuilderCondtionInterface
{

    /**
     * @var string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     */
    private $operator;

    /**
     * @var string $table
     */
    private $table;

    /**
     * @var string $columnReferensi
     */
    private $columnReferensi;

    private string $columnTable;


    /**
     * JoinCondition constructor
     *
     * @param string $columnTable the literal to the left of $columnTable
     * @param mixed $column the literal to the left of $operator
     * @param string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     * @param string $columnReferensi the literal to the right of $operator
     */
    public function __construct($operator, $table, $columnTable, $columnReferensi)
    {

        $this->operator = $operator;
        $this->table = $table;
        $this->columnTable = $columnTable;
        $this->columnReferensi = $columnReferensi;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
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
    public function getColumnTable()
    {
        return $this->columnTable;
    }

    /**
     * @return string
     */
    public function getColumnReferensi()
    {
        return $this->columnReferensi;
    }

    public function build(): string
    {
        return "{$this->operator} {$this->table} ON {$this->columnTable} = {$this->columnReferensi}";
    }

    /**
     * @return self
     * @throws \InvalidArgumentException if wrong number of operands have been given.
     */
    public static function fromArrayDefinition($operator, $operands)
    {

        if (count($operands) !== 3) {
            throw new \InvalidArgumentException("Operator '$operator' requires 3 operands.");
        }

        return new static($operator, $operands[0], $operands[1], $operands[2]);
    }
}
