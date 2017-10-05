<?php

namespace Box\Spout\Reader\XLSX\Helper;

use Box\Spout\Common\Exception\InvalidArgumentException;

/**
 * Class CellHelper
 * This class provides helper functions when working with cells
 *
 * @package Box\Spout\Reader\XLSX\Helper
 */
class CellHelper
{
    // Using ord() is super slow... Using a pre-computed hash table instead.
    private static $columnLetterToIndexMapping = [
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6,
        'H' => 7, 'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13,
        'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20,
        'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
    ];

    /**
     * Fills the missing indexes of an array with a given value.
     * For instance, $dataArray = []; $a[1] = 1; $a[3] = 3;
     * Calling fillMissingArrayIndexes($dataArray, 'FILL') will return this array: ['FILL', 1, 'FILL', 3]
     *
     * @param array $dataArray The array to fill
     * @param string|void $fillValue optional
     * @return array
     */
    public static function fillMissingArrayIndexes($dataArray, $fillValue = '')
    {
        if (empty($dataArray)) {
            return [];
        }
        $existingIndexes = array_keys($dataArray);

        $newIndexes = array_fill_keys(range(0, max($existingIndexes)), $fillValue);
        $dataArray += $newIndexes;

        ksort($dataArray);

        return $dataArray;
    }

    /**
     * Returns the base 10 column index associated to the cell index (base 26).
     * Excel uses A to Z letters for column indexing, where A is the 1st column,
     * Z is the 26th and AA is the 27th.
     * The mapping is zero based, so that A1 maps to 0, B2 maps to 1, Z13 to 25 and AA4 to 26.
     *
     * @param string $cellIndex The Excel cell index ('A1', 'BC13', ...)
     * @return int
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException When the given cell index is invalid
     */
    public static function getColumnIndexFromCellIndex($cellIndex)
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
                $columnIndex = (self::$columnLetterToIndexMapping[$columnLetters]);
                break;
            case 2:
                $firstLetterIndex = (self::$columnLetterToIndexMapping[$columnLetters[0]] + 1) * 26;
                $secondLetterIndex = self::$columnLetterToIndexMapping[$columnLetters[1]];
                $columnIndex = $firstLetterIndex + $secondLetterIndex;
                break;
            case 3:
                $firstLetterIndex = (self::$columnLetterToIndexMapping[$columnLetters[0]] + 1) * 676;
                $secondLetterIndex = (self::$columnLetterToIndexMapping[$columnLetters[1]] + 1) * 26;
                $thirdLetterIndex = self::$columnLetterToIndexMapping[$columnLetters[2]];
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
     * @return bool
     */
    protected static function isValidCellIndex($cellIndex)
    {
        return (preg_match('/^[A-Z]{1,3}\d+$/', $cellIndex) === 1);
    }
}
