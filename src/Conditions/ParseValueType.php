<?php

declare(strict_types=1);

namespace SqlQuery\Conditions;

/**
 * ParseValueType
 *
 * Utility class for parsing and formatting values for SQL queries
 *
 */
final class ParseValueType
{
    /**
     * Parse mixed value into SQL-safe format
     *
     * @param mixed $value Value to be parsed
     * @return mixed Formatted value for SQL query
     */
    public static function parse(mixed $value)
    {
        if (is_null($value)) {
            return NULL;
        }

        if (is_string($value)) {
            return "'{$value}'";
        }

        if (is_float($value)) {
            return "{$value}";
        }

        if (is_int($value) || is_numeric($value)) {
            return (int)$value;
        }

        return $value;
    }
}
