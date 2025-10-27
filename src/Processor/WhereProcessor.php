<?php

declare(strict_types=1);

namespace SqlQuery\Processor;

use SqlQuery\Contracts\{BuilderCondtionInterface, CompositeInterface, ConditionInterface, ExpressionInterface, ProcessorInterface};
use SqlQuery\Conditions\{BetweenColumnsCondition, CompositeCondition, InCondition, IsCondition, LikeCondition, NotCondition, SimpleCondition};

/**
 * WhereProcessor
 *
 * Processes WHERE clause conditions and builds SQL WHERE statements
 *
 */
class WhereProcessor implements ProcessorInterface
{

    /**
     * @var array $OPERATOR Supported SQL operators for WHERE conditions
     */
    private static $OPERATOR = ['IS', 'IS NOT', 'NOT', 'AND', 'OR', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'OR LIKE', 'OR NOT LIKE', '=', '==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=',];

    /**
     * @var ExpressionInterface|CompositeInterface $conditions WHERE conditions to process
     */
    private $conditions;

    /**
     * @var array $params Query parameters for binding
     */

    private array $params = [];

    /**
     * Constructor for WHERE processor
     *
     * @param mixed $condition WHERE condition (array, string, or ExpressionInterface)
     * @param array $params Query parameters for binding
     */
    public function __construct($condition, array $params = [])
    {

        $this->params = $params;

        if ($condition instanceof ExpressionInterface) {
            $this->conditions = $condition;
        }

        if (is_array($condition)) {
            $this->conditions = $this->buildCondition($condition);
        }

        if (is_string($condition)) {
            return;
        }
    }


    /**
     * Build SQL WHERE clause from processed conditions
     *
     * @return string Generated WHERE clause SQL
     */
    public function build(): string
    {

        $querys = [];
        $condition = $this->conditions;
        if ($condition instanceof BuilderCondtionInterface) {
            $querys[] = $condition->build();
        }

        if (count($querys) === 1) {
            return current($querys);
        }

        return "";
    }

    /**
     * Build condition from various input formats
     *
     * @param mixed $condition Input condition (array, string, or ExpressionInterface)
     * @return ExpressionInterface Processed condition object
     * @throws \InvalidArgumentException When condition format is invalid
     */
    protected function buildCondition($condition)
    {
        $condition = $this->filterCondition($condition);

        if ($condition instanceof ExpressionInterface) {
            return $condition;
        }

        if (!is_array($condition)) {
            return new SimpleCondition((string)$condition, '=', '');
        }

        // Handle hash format
        if (!isset($condition[0])) {
            return $this->processHashCondition($condition);
        }

        // Format Operator: ['AND', ['name', '=', 'John']] atau ['name', '=', 'John']
        $operator = strtoupper($condition[0]);

        // Cek apakah elemen pertama adalah operator yang dikenal
        if (in_array($operator, self::$OPERATOR)) {
            return $this->processOperatorCondition($condition);
        }

        // Jika elemen pertama bukan operator, asumsikan ini adalah kondisi sederhana
        // dengan format ['kolom', 'operator', 'nilai']
        if (count($condition) >= 2) {
            $column = $condition[0];
            $operator = $condition[1];
            $value = $condition[2] ?? null;

            return $this->processOperatorCondition([$operator, $column, $value]);
        }

        throw new \InvalidArgumentException("Format kondisi tidak valid.");
    }

    /**
     * Process hash-format conditions (key-value pairs)
     *
     * @param array $condition Hash array of conditions
     * @return CompositeCondition Combined conditions with AND operator
     */
    protected function processHashCondition(array $condition)
    {

        $simpleConditions = [];
        $compositeConditions = [];

        foreach ($condition as $name => $value) {
            if (is_array($value)) {
                $compositeConditions[] = new InCondition($name, 'IN', $value);
            } elseif ($value === null) {
                $compositeConditions[] = new IsCondition($name, 'IS NULL');
            } else {
                $simpleConditions[] = new SimpleCondition($name, '=', $value);
            }
        }

        $allConditions = array_merge($simpleConditions, $compositeConditions);

        // Gabungkan semua kondisi dengan AND
        return new CompositeCondition('AND', $allConditions);
    }

    /**
     * Process hash-format conditions (alternative implementation)
     *
     * @param array $condition Hash array of conditions
     * @return ExpressionInterface|array Processed condition
     */
    protected function processHashCondition_(array $condition)
    {
        $conditions = [];

        var_dump($condition);
        foreach ($condition as $name => $value) {
            if (is_array($value)) {
                // Handle IN condition
                $conditions = new InCondition($name, 'IN', $value);
                unset($condition[$name]);
            } elseif ($value === null) {
                // Handle IS NULL condition
                $conditions = new IsCondition($name, 'IS NULL');
                unset($condition[$name]);
            } else {
                // Handle simple equality
                $conditions = new SimpleCondition($name, '=', $value);
                unset($condition[$name]);
            }
        }
        return $conditions;
    }

    // Contoh perbaikan untuk processHashCondition
    protected function processHashConditionss(array $condition)
    {
        $simpleConditions = [];
        $compositeConditions = [];

        foreach ($condition as $name => $value) {
            if (is_array($value)) {
                $compositeConditions[] = new InCondition($name, 'IN', $value);
            } elseif ($value === null) {
                $compositeConditions[] = new IsCondition($name, 'IS NULL');
            } else {
                $simpleConditions[] = new SimpleCondition($name, '=', $value);
            }
        }

        $allConditions = array_merge($simpleConditions, $compositeConditions);

        // Gabungkan semua kondisi dengan AND
        return new CompositeCondition('AND', $allConditions);
    }

    /**
     * Memproses kondisi dalam format operator [operator, operand1, operand2, ...]
     * @return CompositeInterface|ExpressionInterface|null
     */
    /**
     * Process operator-based conditions
     *
     * @param array $condition Array with operator and operands
     * @return ExpressionInterface Processed condition object
     * @throws \InvalidArgumentException When operator is unsupported or arguments invalid
     */
    protected function processOperatorCondition(array $condition)
    {
        $operator = strtoupper(array_shift($condition));
        switch ($operator) {
            case 'NOT':
                if (empty($condition)) {
                    throw new \InvalidArgumentException("Operator 'NOT' membutuhkan setidaknya satu argumen.");
                }

                $subCondition = count($condition) === 2 ?
                    $this->processHashCondition([$condition[0] => $condition[1]]) :
                    $this->buildCondition(array_shift($condition));
                return new NotCondition($subCondition);
            case 'AND':
            case 'OR':
                $subConditions = [];
                foreach ($condition as $subCondition) {
                    $subConditions[] = $this->buildCondition($subCondition);
                }
                return $this->combineConditions($operator, $subConditions);
            case 'BETWEEN':
            case 'NOT BETWEEN':
                return new BetweenColumnsCondition($condition[0], $operator, $condition[1], $condition[2]);

            case 'IN':
            case 'NOT IN':
                return new InCondition($condition[0], $operator, $condition[1]);

            case 'LIKE':
            case 'NOT LIKE':
                return new LikeCondition($condition[0], $operator, $condition[1]);
            case '!=':
            case '=':
            case '<>':
            case '>':
            case '>=':
            case '<':
            case '<=':
                return new SimpleCondition($condition[0], $operator, $condition[1]);
            case 'IS NULL':
            case 'IS NOT':
            case 'IS NOT NULL':
                return new IsCondition($condition[0], $operator);
            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }
    }

    /**
     * Combine multiple conditions with logical operator
     *
     * @param string $operator Logical operator (AND, OR)
     * @param array $conditions Array of condition objects
     * @return CompositeInterface|ExpressionInterface Combined condition
     */
    protected function combineConditions(string $operator, array $conditions)
    {
        $filtered = array_filter($conditions, function ($cond) {
            return !$this->isEmpty($cond);
        });

        if (empty($filtered)) {
            throw new \InvalidArgumentException("Empty conditions for {$operator} operator");
        }

        if (count($filtered) === 1) {
            return reset($filtered);
        }

        return new CompositeCondition($operator, $filtered);
    }


    /**
     * Removes [[isEmpty()|empty operands]] from the given query condition.
     *
     * @param array $condition the original condition
     * @return array the condition with [[isEmpty()|empty operands]] removed.
     * @throws NotSupportedException if the condition operator is not supported
     */
    protected function filterCondition($condition)
    {
        if (!is_array($condition)) {
            return $condition;
        }

        if (!isset($condition[0])) {
            // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
            foreach ($condition as $name => $value) {
                if ($this->isEmpty($value)) {
                    unset($condition[$name]);
                }
            }

            return $condition;
        }

        // operator format: operator, operand 1, operand 2, ...

        $operator = array_shift($condition);

        switch (strtoupper($operator)) {
            case 'NOT':
            case 'AND':
            case 'OR':
                foreach ($condition as $i => $operand) {
                    $subCondition = $this->filterCondition($operand);
                    if ($this->isEmpty($subCondition)) {
                        unset($condition[$i]);
                    } else {
                        $condition[$i] = $subCondition;
                    }
                }

                if (empty($condition)) {
                    return [];
                }
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (array_key_exists(1, $condition) && array_key_exists(2, $condition)) {
                    if ($this->isEmpty($condition[1]) || $this->isEmpty($condition[2])) {
                        return [];
                    }
                }
                break;
            case '!=':
            case '=':
            case '==':
                if (is_null($condition[1])) {
                    $op = ['==' => 'IS NULL', '=' => 'IS NULL', '!=' => 'IS NOT NULL'];
                    array_unshift($condition, $op[$operator]);
                    return $condition;
                }
                break;
            default:
                if (array_key_exists(1, $condition) && $this->isEmpty($condition[1])) {
                    return [];
                }
        }

        array_unshift($condition, $operator);

        return $condition;
    }

    /**
     * Returns a value indicating whether the give value is "empty".
     *
     * The value is considered "empty", if one of the following conditions is satisfied:
     *
     * - it is `null`,
     * - an empty string (`''`),
     * - a string containing only whitespace characters,
     * - or an empty array.
     *
     * @param mixed $value
     * @return bool if the value is empty
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }

    /**
     * Get processed WHERE conditions
     *
     * @return ExpressionInterface|CompositeInterface|ConditionInterface
     */
    public function getConditions(): ExpressionInterface|CompositeInterface|ConditionInterface
    {
        return $this->conditions;
    }

    /**
     * Get query parameters for binding
     *
     * @return array Query parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
