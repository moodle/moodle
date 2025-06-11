<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Reader\Exception\NoSheetsFoundException;
use OpenSpout\Reader\SheetIteratorInterface;
use OpenSpout\Reader\XLSX\Manager\SheetManager;

/**
 * @implements SheetIteratorInterface<Sheet>
 */
final class SheetIterator implements SheetIteratorInterface
{
    /** @var Sheet[] The list of sheet present in the file */
    private array $sheets;

    /** @var int The index of the sheet being read (zero-based) */
    private int $currentSheetIndex = 0;

    /**
     * @param SheetManager $sheetManager Manages sheets
     *
     * @throws NoSheetsFoundException If there are no sheets in the file
     */
    public function __construct(SheetManager $sheetManager)
    {
        // Fetch all available sheets
        $this->sheets = $sheetManager->getSheets();

        if (0 === \count($this->sheets)) {
            throw new NoSheetsFoundException('The file must contain at least one sheet.');
        }
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->currentSheetIndex = 0;
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return $this->currentSheetIndex < \count($this->sheets);
    }

    /**
     * Move forward to next element.
     *
     * @see http://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        ++$this->currentSheetIndex;
    }

    /**
     * Return the current element.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    public function current(): Sheet
    {
        return $this->sheets[$this->currentSheetIndex];
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        return $this->currentSheetIndex + 1;
    }
}
