<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\XLSX\Helper\SheetHelper;
use Box\Spout\Reader\Exception\NoSheetsFoundException;

/**
 * Class SheetIterator
 * Iterate over XLSX sheet.
 *
 * @package Box\Spout\Reader\XLSX
 */
class SheetIterator implements IteratorInterface
{
    /** @var \Box\Spout\Reader\XLSX\Sheet[] The list of sheet present in the file */
    protected $sheets;

    /** @var int The index of the sheet being read (zero-based) */
    protected $currentSheetIndex;

    /**
     * @param string $filePath Path of the file to be read
     * @param \Box\Spout\Reader\XLSX\Helper\SharedStringsHelper $sharedStringsHelper
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     * @throws \Box\Spout\Reader\Exception\NoSheetsFoundException If there are no sheets in the file
     */
    public function __construct($filePath, $sharedStringsHelper, $globalFunctionsHelper, $shouldFormatDates)
    {
        // Fetch all available sheets
        $sheetHelper = new SheetHelper($filePath, $sharedStringsHelper, $globalFunctionsHelper, $shouldFormatDates);
        $this->sheets = $sheetHelper->getSheets();

        if (count($this->sheets) === 0) {
            throw new NoSheetsFoundException('The file must contain at least one sheet.');
        }
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    public function rewind()
    {
        $this->currentSheetIndex = 0;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->currentSheetIndex < count($this->sheets));
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    public function next()
    {
        // Using isset here because it is way faster than array_key_exists...
        if (isset($this->sheets[$this->currentSheetIndex])) {
            $currentSheet = $this->sheets[$this->currentSheetIndex];
            $currentSheet->getRowIterator()->end();

            $this->currentSheetIndex++;
        }
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return \Box\Spout\Reader\XLSX\Sheet
     */
    public function current()
    {
        return $this->sheets[$this->currentSheetIndex];
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return $this->currentSheetIndex + 1;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end()
    {
        // make sure we are not leaking memory in case the iteration stopped before the end
        foreach ($this->sheets as $sheet) {
            $sheet->getRowIterator()->end();
        }
    }
}
