<?php

namespace Box\Spout\Writer\Common\Helper;

/**
 * Class CellHelper
 * This class provides helper functions when working with cells
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
                $columnIndex = (int) ($columnIndex / 26) - 1;
            } while ($columnIndex >= 0);

            self::$columnIndexToCellIndexCache[$originalColumnIndex] = $cellIndex;
        }

        return self::$columnIndexToCellIndexCache[$originalColumnIndex];
    }
}
