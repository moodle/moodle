<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Reader\Exception\NoSheetsFoundException;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\XLSX\Manager\SheetManager;

/**
 * Class SheetIterator
 * Iterate over XLSX sheet.
 */
class SheetIterator implements IteratorInterface
{
    /** @var \Box\Spout\Reader\XLSX\Sheet[] The list of sheet present in the file */
    protected $sheets;

    /** @var int The index of the sheet being read (zero-based) */
    protected $currentSheetIndex;

    /**
     * @param SheetManager $sheetManager Manages sheets
     * @throws \Box\Spout\Reader\Exception\NoSheetsFoundException If there are no sheets in the file
     */
    public function __construct($sheetManager)
    {
        // Fetch all available sheets
        $this->sheets = $sheetManager->getSheets();

        if (\count($this->sheets) === 0) {
            throw new NoSheetsFoundException('The file must contain at least one sheet.');
        }
    }

    /**
     * Rewind the Iterator to the first element
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->currentSheetIndex = 0;
    }

    /**
     * Checks if current position is valid
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return ($this->currentSheetIndex < \count($this->sheets));
    }

    /**
     * Move forward to next element
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
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
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @return \Box\Spout\Reader\XLSX\Sheet
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->sheets[$this->currentSheetIndex];
    }

    /**
     * Return the key of the current element
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->currentSheetIndex + 1;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function end()
    {
        // make sure we are not leaking memory in case the iteration stopped before the end
        foreach ($this->sheets as $sheet) {
            $sheet->getRowIterator()->end();
        }
    }
}
