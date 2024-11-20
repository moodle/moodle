<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Helper;

use OpenSpout\Common\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class CellHelper
{
    // Using ord() is super slow... Using a pre-computed hash table instead.
    private const columnLetterToIndexMapping = [
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6,
        'H' => 7, 'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13,
        'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20,
        'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
    ];

    /**
     * Returns the base 10 column index associated to the cell index (base 26).
     * Excel uses A to Z letters for column indexing, where A is the 1st column,
     * Z is the 26th and AA is the 27th.
     * The mapping is zero based, so that A1 maps to 0, B2 maps to 1, Z13 to 25 and AA4 to 26.
     *
     * @param string $cellIndex The Excel cell index ('A1', 'BC13', ...)
     *
     * @throws InvalidArgumentException When the given cell index is invalid
     */
    public static function getColumnIndexFromCellIndex(string $cellIndex): int
    {
        if (!self::isValidCellIndex($cellIndex)) {
            throw new InvalidArgumentException('Cannot get column index from an invalid cell index.');
        }

        $columnIndex = 0;

        // Remove row information
        $columnLetters = preg_replace('/\d/', '', $cellIndex);

        // strlen() is super slow too... Using isset() is way faster and not too unreadable,
        // since we checked before that there are between 1 and 3 letters.
        $columnLength = isset($columnLetters[1]) ? (isset($columnLetters[2]) ? 3 : 2) : 1;

        // Looping over the different letters of the column is slower than this method.
        // Also, not using the pow() function because it's slooooow...
        switch ($columnLength) {
            case 1:
                $columnIndex = self::columnLetterToIndexMapping[$columnLetters];

                break;

            case 2:
                $firstLetterIndex = (self::columnLetterToIndexMapping[$columnLetters[0]] + 1) * 26;
                $secondLetterIndex = self::columnLetterToIndexMapping[$columnLetters[1]];
                $columnIndex = $firstLetterIndex + $secondLetterIndex;

                break;

            case 3:
                $firstLetterIndex = (self::columnLetterToIndexMapping[$columnLetters[0]] + 1) * 676;
                $secondLetterIndex = (self::columnLetterToIndexMapping[$columnLetters[1]] + 1) * 26;
                $thirdLetterIndex = self::columnLetterToIndexMapping[$columnLetters[2]];
                $columnIndex = $firstLetterIndex + $secondLetterIndex + $thirdLetterIndex;

                break;
        }

        return $columnIndex;
    }

    /**
     * Returns whether a cell index is valid, in an Excel world.
     * To be valid, the cell index should start with capital letters and be followed by numbers.
     * There can only be 3 letters, as there can only be 16,384 rows, which is equivalent to 'XFE'.
     *
     * @param string $cellIndex The Excel cell index ('A1', 'BC13', ...)
     */
    private static function isValidCellIndex(string $cellIndex): bool
    {
        return 1 === preg_match('/^[A-Z]{1,3}\d+$/', $cellIndex);
    }
}
