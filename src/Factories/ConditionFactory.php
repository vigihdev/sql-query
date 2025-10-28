<?php

declare(strict_types=1);

namespace SqlQuery\Factories;

use InvalidArgumentException;
use SqlQuery\Contracts\ExpressionInterface;
use SqlQuery\Conditions\{BetweenColumnsCondition, CompositeCondition, InCondition, IsCondition, LikeCondition, NotCondition, SimpleCondition};
use SqlQuery\Contracts\ConditionFactoryContract;

/**
 * ConditionFactory
 *
 * Factory class responsible for creating (instantiating) the correct 
 * Condition object based on the given operator and operands.
 */
final class ConditionFactory implements ConditionFactoryContract
{
    /**
     * Operator-operator yang didukung oleh factory ini.
     * Ini bisa dipindahkan ke kontrak jika diperlukan.
     */
    public const SUPPORTED_OPERATORS = [
        'AND',
        'OR',
        'NOT',
        'BETWEEN',
        'NOT BETWEEN',
        'IN',
        'NOT IN',
        'LIKE',
        'NOT LIKE',
        'OR LIKE',
        'OR NOT LIKE',
        'IS',
        'IS NOT',
        'IS NULL',
        'IS NOT NULL', // IS Condition
        '=',
        '==',
        '===',
        '!=',
        '<>',
        '!==',
        '>',
        '<',
        '>=',
        '<=', // Simple Condition
    ];

    /**
     * Membuat objek ExpressionInterface/ConditionInterface yang sesuai.
     * @param string $operator Operator SQL (misalnya 'AND', '=', 'IN')
     * @param array $operands Argumen atau nilai untuk kondisi tersebut
     * @return ExpressionInterface Objek kondisi yang sudah dibuat
     * @throws InvalidArgumentException Jika operator tidak didukung atau argumen tidak valid
     */
    public function create(string $operator, array $operands): ExpressionInterface
    {
        $operator = strtoupper($operator);

        if (!in_array($operator, self::SUPPORTED_OPERATORS)) {
            throw new InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return match ($operator) {
            'AND', 'OR' => $this->createComposite($operator, $operands),
            'NOT' => $this->createNot($operands),
            'BETWEEN', 'NOT BETWEEN' => $this->createBetween($operator, $operands),
            'IN', 'NOT IN' => $this->createIn($operator, $operands),
            'LIKE', 'NOT LIKE', 'OR LIKE', 'OR NOT LIKE' => $this->createLike($operator, $operands),

            // IS dan Simple Condition
            'IS', 'IS NOT', 'IS NULL', 'IS NOT NULL' => $this->createIs($operator, $operands),
            default => $this->createSimple($operator, $operands),
        };
    }

    /**
     * Membuat objek SimpleCondition (misalnya `kolom = nilai`).
     */
    protected function createSimple(string $operator, array $operands): SimpleCondition|IsCondition
    {
        if (count($operands) !== 2) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan 2 argumen: [kolom, nilai]");
        }

        $column = $operands[0];
        $value = $operands[1];

        if (is_null($value)) {
            // Jika nilai adalah NULL, kita harus mengkonversi ke IS NULL atau IS NOT NULL.
            $sqlOperator = match ($operator) {
                '=', '==', '===' => 'IS NULL',
                '!=', '<>', '!==' => 'IS NOT NULL',
                default => throw new InvalidArgumentException("Operator '{$operator}' tidak dapat digunakan dengan NULL.")
            };
            // Mengarahkan ke pembuatan IsCondition
            return $this->createIs($sqlOperator, [$column]);
        }

        // Jika nilai BUKAN NULL, lanjutkan dengan SimpleCondition normal
        $sqlOperator = match ($operator) {
            '==', '===' => '=',
            '!=', '!==' => '<>',
            default => $operator,
        };

        return new SimpleCondition($column, $sqlOperator, $value);
    }


    /**
     * Membuat objek CompositeCondition (AND/OR).
     * Catatan: Untuk AND/OR, operan adalah *sub-conditions* yang sudah diproses oleh parser.
     */
    protected function createComposite(string $operator, array $operands): CompositeCondition
    {
        if (empty($operands)) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan setidaknya satu sub-kondisi.");
        }
        // Asumsi: Operands di sini sudah berupa array of ExpressionInterface
        return new CompositeCondition($operator, $operands);
    }

    /**
     * Membuat objek NotCondition.
     */
    protected function createNot(array $operands): NotCondition
    {
        // Asumsi: Operands[0] adalah objek ExpressionInterface yang sudah diproses.
        if (count($operands) !== 1 || !$operands[0] instanceof ExpressionInterface) {
            throw new InvalidArgumentException("Operator 'NOT' membutuhkan satu objek kondisi sebagai argumen.");
        }
        return new NotCondition($operands[0]);
    }

    /**
     * Membuat objek BetweenColumnsCondition.
     */
    protected function createBetween(string $operator, array $operands): BetweenColumnsCondition
    {
        if (count($operands) !== 3) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan 3 argumen: [kolom, nilai1, nilai2]");
        }
        return new BetweenColumnsCondition($operands[0], $operator, $operands[1], $operands[2]);
    }

    /**
     * Membuat objek InCondition.
     */
    protected function createIn(string $operator, array $operands): InCondition
    {
        if (count($operands) !== 2) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan 2 argumen: [kolom, array_nilai]");
        }
        return new InCondition($operands[0], $operator, $operands[1]);
    }

    /**
     * Membuat objek LikeCondition.
     */
    protected function createLike(string $operator, array $operands): LikeCondition
    {
        if (count($operands) !== 2) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan 2 argumen: [kolom, pola_string]");
        }
        return new LikeCondition($operands[0], $operator, $operands[1]);
    }

    /**
     * Membuat objek IsCondition (IS NULL, IS NOT NULL).
     */
    protected function createIs(string $operator, array $operands): IsCondition
    {
        if (count($operands) < 1) {
            throw new InvalidArgumentException("Operator '{$operator}' membutuhkan setidaknya 1 argumen: [kolom].");
        }

        $column = $operands[0];

        // Handle format IS NOT / IS NULL jika operatornya hanya 'IS' atau 'IS NOT'
        if ($operator === 'IS' || $operator === 'IS NOT') {
            // Asumsi: nilai kedua harus NULL atau literal lain, di sini kita sederhanakan untuk NULL
            if (isset($operands[1]) && is_null($operands[1])) {
                $operator .= ' NULL';
            } else {
                throw new InvalidArgumentException("Operator '{$operator}' untuk saat ini hanya mendukung nilai NULL di argumen kedua.");
            }
        }

        return new IsCondition($column, $operator);
    }
}
