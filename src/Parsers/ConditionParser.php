<?php

declare(strict_types=1);

namespace SqlQuery\Parsers;

use InvalidArgumentException;
use SqlQuery\Contracts\{ConditionFactoryContract, ExpressionInterface, ConditionParserContract};
use SqlQuery\Conditions\{CompositeCondition};
use SqlQuery\Factories\ConditionFactory;

/**
 * ConditionParser
 *
 * Bertanggung jawab untuk menganalisis format kondisi array/string dan 
 * mengubahnya menjadi objek ExpressionInterface yang valid menggunakan ConditionFactory.
 */
final class ConditionParser implements ConditionParserContract
{

    /**
     * @var array $COMPOSITE_OPERATORS Operator yang menggabungkan kondisi lain (AND, OR)
     */
    private const COMPOSITE_OPERATORS = ['AND', 'OR'];

    public function __construct(
        private readonly ConditionFactoryContract $factory
    ) {}

    /**
     * Metode utama untuk parsing berbagai format kondisi.
     *
     * @param mixed $condition Input kondisi (string, array hash, atau array operator)
     * @return ExpressionInterface Objek kondisi yang sudah diproses
     * @throws InvalidArgumentException
     */
    public function parse(mixed $condition): ExpressionInterface
    {
        if ($condition instanceof ExpressionInterface) {
            return $condition;
        }

        if (is_string($condition)) {
            return $this->factory->create('=', [(string)$condition, 1]); // Contoh: 'kolom' = 1
        }

        if (!is_array($condition) || empty($condition)) {
            throw new InvalidArgumentException("Kondisi harus berupa string, array, atau ExpressionInterface yang valid.");
        }

        // Cek apakah ini format hash (key-value)
        if (!isset($condition[0])) {
            return $this->parseHashCondition($condition);
        }

        // Jika bukan hash, harus format operator
        $operator = strtoupper($condition[0]);

        if (in_array($operator, self::COMPOSITE_OPERATORS)) {
            return $this->parseCompositeCondition($operator, array_slice($condition, 1));
        }

        // Asumsikan sisa format adalah format operator tunggal: ['kolom', 'operator', 'nilai']
        // Di sini kita mendeteksi format ['kolom', 'nilai'] (default =), atau ['kolom', 'operator', 'nilai']
        return $this->parseSingleOperatorCondition($condition);
    }

    /**
     * Memproses kondisi dalam format Hash (kolom => nilai)
     * Contoh: ['name' => 'John', 'age' => null, 'status' => ['active', 'pending']]
     *
     * @param array $condition Hash array of conditions
     * @return CompositeCondition Kombinasi kondisi dengan operator AND
     */
    protected function parseHashCondition(array $condition): CompositeCondition|ExpressionInterface
    {
        $subConditions = [];
        foreach ($condition as $column => $value) {

            $operator = '=';
            $operands = [$column, $value];

            $subConditions[] = $this->factory->create($operator, $operands);
        }

        // Jika hanya satu kondisi, kembalikan objek tunggal
        if (count($subConditions) === 1) {
            return reset($subConditions);
        }

        // Jika lebih dari satu, gabungkan dengan AND
        return $this->factory->create('AND', $subConditions);
    }

    /**
     * Memproses kondisi dalam format Composite (AND, OR)
     * Contoh: ['AND', ['name', '=', 'John'], ['age', '>', 25]]
     */
    protected function parseCompositeCondition(string $operator, array $conditions): ExpressionInterface
    {
        $subConditions = [];
        foreach ($conditions as $subInput) {
            // REKURSIF: Parse setiap sub-kondisi di dalamnya
            $subConditions[] = $this->parse($subInput);
        }

        // Hapus kondisi yang kosong (misalnya filterCondition Anda yang lama)
        $filtered = array_filter($subConditions, function ($cond) {
            return $cond instanceof ExpressionInterface;
        });

        if (empty($filtered)) {
            // Jika semua kondisi di dalam AND/OR kosong, kembalikan kondisi yang valid (misal: TrueCondition/Raw '1=1')
            throw new InvalidArgumentException("Kondisi kosong di dalam operator '{$operator}'.");
        }

        // Jika hanya ada satu sub-kondisi, kembalikan sub-kondisi itu sendiri
        if (count($filtered) === 1) {
            return reset($filtered);
        }

        // Delegasikan ke factory untuk membuat CompositeCondition
        return $this->factory->create($operator, $filtered);
    }

    /**
     * Memproses format operator tunggal, yang mungkin juga format SimpleCondition
     * Contoh: ['age', '>', 25] atau ['IN', 'category', [1, 2]]
     */
    protected function parseSingleOperatorCondition(array $condition): ExpressionInterface
    {
        $count = count($condition);

        // Format 1: ['kolom', 'nilai'] -> Asumsi operator default '='
        if ($count === 2) {
            [$column, $value] = $condition;
            // Panggil factory dengan operator default =
            return $this->factory->create('=', [$column, $value]);
        }

        // Format 2: ['kolom', 'operator', 'nilai']
        if ($count === 3) {
            // Logika umum untuk 3 elemen: [kolom, operator, nilai]
            $column = $condition[0];
            $operator = $condition[1];
            $value = $condition[2];

            // Cek khusus untuk operator IN/BETWEEN/LIKE yang biasanya ditulis [OPERATOR, kolom, nilai, ...]
            // Misalnya: ['IN', 'kolom', [nilai]]

            $firstElementUpper = strtoupper($condition[0]);
            // Jika elemen pertama adalah operator khusus (IN, BETWEEN, LIKE, IS), 
            // kita asumsikan formatnya ['OPERATOR', kolom, nilai]
            if (in_array($firstElementUpper, ConditionFactory::SUPPORTED_OPERATORS)) {
                // Ini adalah format Operator-Pertama: [OPERATOR, kolom, nilai]
                $operator = $firstElementUpper;
                $operands = array_slice($condition, 1);
                return $this->factory->create($operator, $operands);
            }

            // Jika tidak ada operator khusus di elemen [0], kita asumsikan format [kolom, operator, nilai]
            $operands = [$column, $value];
            return $this->factory->create($operator, $operands);
        }

        // Format 3: Format operator kompleks (mis. BETWEEN, yang memiliki 4 elemen: [BETWEEN, kolom, nilai1, nilai2])
        // Kita asumsikan elemen pertama adalah operator, dan sisanya adalah operan.
        if ($count > 3) {
            $operator = strtoupper($condition[0]);
            $operands = array_slice($condition, 1);
            return $this->factory->create($operator, $operands);
        }

        throw new InvalidArgumentException("Kondisi array tidak didukung atau memiliki jumlah elemen yang salah: " . print_r($condition, true));
    }
}
