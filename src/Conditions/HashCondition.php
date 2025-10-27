<?php


declare(strict_types=1);


namespace SqlQuery\Conditions;

use SqlQuery\Contracts\ConditionInterface;


class HashCondition implements ConditionInterface
{

    /**
     * @var array|null the condition specification.
     */
    private $hash;

    /**
     * @var mixed $value
     */
    private $value;

    /**
     * @var string $column
     */
    private $column;

    private $operator;


    /**
     * HashCondition constructor.
     *
     * @param array|null $hash
     */
    public function __construct(string $operator, $hash)
    {
        $this->operator = $operator;
        $this->hash = $hash;

        if (is_array($hash) && count($hash) === 1) {
            $this->column = key($hash);
            $this->value = current($hash);
        }
    }

    /**
     * @return array|null
     */
    public function getHash()
    {
        return $this->hash;
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return self
     */
    public static function fromArrayDefinition($operator, $operands)
    {
        return new static($operator, $operands);
    }
}
