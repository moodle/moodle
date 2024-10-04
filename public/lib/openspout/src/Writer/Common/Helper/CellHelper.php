<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;

/**
 * @internal
 */
final class CellHelper
{
    /** @var array<int, string> Cache containing the mapping column index => column letters */
    private static array $columnIndexToColumnLettersCache = [];

    /**
     * Returns the column letters (base 26) associated to the base 10 column index.
     * Excel uses A to Z letters for column indexing, where A is the 1st column,
     * Z is the 26th and AA is the 27th.
     * The mapping is zero based, so that 0 maps to A, B maps to 1, Z to 25 and AA to 26.
     *
     * @param int $columnIndexZeroBased The Excel column index (0, 42, ...)
     *
     * @return string The associated cell index ('A', 'BC', ...)
     */
    public static function getColumnLettersFromColumnIndex(int $columnIndexZeroBased): string
    {
        $originalColumnIndex = $columnIndexZeroBased;

        // Using isset here because it is way faster than array_key_exists...
        if (!isset(self::$columnIndexToColumnLettersCache[$originalColumnIndex])) {
            $columnLetters = '';
            $capitalAAsciiValue = \ord('A');

            do {
                $modulus = $columnIndexZeroBased % 26;
                $columnLetters = \chr($capitalAAsciiValue + $modulus).$columnLetters;

                // substracting 1 because it's zero-based
                $columnIndexZeroBased = (int) ($columnIndexZeroBased / 26) - 1;
            } while ($columnIndexZeroBased >= 0);

            self::$columnIndexToColumnLettersCache[$originalColumnIndex] = $columnLetters;
        }

        return self::$columnIndexToColumnLettersCache[$originalColumnIndex];
    }
}
