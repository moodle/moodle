<?php

namespace Box\Spout\Common\Helper;

/**
 * Class CellTypeHelper
 * This class provides helper functions to determine the type of the cell value
 */
class CellTypeHelper
{
    /**
     * @param $value
     * @return bool Whether the given value is considered "empty"
     */
    public static function isEmpty($value)
    {
        return ($value === null || $value === '');
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

    /**
     * Returns whether the given value is a DateTime or DateInterval object.
     *
     * @param $value
     * @return bool Whether the given value is a DateTime or DateInterval object
     */
    public static function isDateTimeOrDateInterval($value)
    {
        return (
            $value instanceof \DateTime ||
            $value instanceof \DateInterval
        );
    }
}
