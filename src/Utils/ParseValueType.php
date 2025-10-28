<?php

declare(strict_types=1);

namespace SqlQuery\Utils;

final class ParseValueType
{

    /**
     * Menambahkan nilai ke array parameter (by reference) dan mengembalikan placeholder SQL.
     * * @param mixed $value Nilai yang akan diikat.
     * @param array $params Array parameter yang dikumpulkan (harus dilewatkan by reference).
     * @return string Placeholder SQL (umumnya '?').
     */
    public static function addParameter(mixed $value, array &$params): string
    {
        $params[] = $value;
        return '?';
    }

    /**
     * Fungsi opsional untuk menangani nilai literal SQL (misalnya, fungsi NOW()).
     * Jika nilai adalah string, tetapi bukan fungsi SQL, ia tetap akan diikat.
     */
    public static function isSqlLiteral(mixed $value): bool
    {
        return is_string($value) && (strpos(strtoupper($value), 'NOW(') === 0 || strpos(strtoupper($value), 'COUNT(') === 0);
    }
}
