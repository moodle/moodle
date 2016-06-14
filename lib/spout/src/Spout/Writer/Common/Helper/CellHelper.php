<?php

namespace Box\Spout\Writer\Common\Helper;

/**
 * Class CellHelper
 * This class provides helper functions when working with cells
 *
 * @package Box\Spout\Writer\Common\Helper
 */
class CellHelper
{
    /** @var array Cache containing the mapping column index => cell index */
    private static $columnIndexToCellIndexCache = [];

    /**
     * Returns the cell index (base 26) associated to the base 10 column index.
     * Excel uses A to Z letters for column indexing, where A is the 1st column,
     * Z is the 26th and AA is the 27th.
     * The mapping is zero based, so that 0 maps to A, B maps to 1, Z to 25 and AA to 26.
     *
     * @param int $columnIndex The Excel column index (0, 42, ...)
     * @return string The associated cell index ('A', 'BC', ...)
     */
    public static function getCellIndexFromColumnIndex($columnIndex)
    {
        $originalColumnIndex = $columnIndex;

        // Using isset here because it is way faster than array_key_exists...
        if (!isset(self::$columnIndexToCellIndexCache[$originalColumnIndex])) {
            $cellIndex = '';
            $capitalAAsciiValue = ord('A');

            do {
                $modulus = $columnIndex % 26;
                $cellIndex = chr($capitalAAsciiValue + $modulus) . $cellIndex;

                // substracting 1 because it's zero-based
                $columnIndex = intval($columnIndex / 26) - 1;

            } while ($columnIndex >= 0);

            self::$columnIndexToCellIndexCache[$originalColumnIndex] = $cellIndex;
        }

        return self::$columnIndexToCellIndexCache[$originalColumnIndex];
    }

    /**
     * @param $value
     * @return bool Whether the given value is a non empty string
     */
    public static function isNonEmptyString($value)
    {
        return (gettype($value) === 'string' && $value !== '');
    }

    /**
     * Returns whether the given value is numeric.
     * A numeric value is from type "integer" or "double" ("float" is not returned by gettype).
     *
     * @param $value
     * @return bool Whether the given value is numeric
     */
    public static function isNumeric($value)
    {
        $valueType = gettype($value);
        return ($valueType === 'integer' || $valueType === 'double');
    }

    /**
     * Returns whether the given value is boolean.
     * "true"/"false" and 0/1 are not booleans.
     *
     * @param $value
     * @return bool Whether the given value is boolean
     */
    public static function isBoolean($value)
    {
        return gettype($value) === 'boolean';
    }
}
