<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use DOMElement;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Exception\InvalidValueException;
use OpenSpout\Reader\Exception\IteratorNotRewindableException;
use OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use OpenSpout\Reader\RowIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;

final class RowIterator implements RowIteratorInterface
{
    /**
     * Definition of XML nodes names used to parse data.
     */
    public const XML_NODE_TABLE = 'table:table';
    public const XML_NODE_ROW = 'table:table-row';
    public const XML_NODE_CELL = 'table:table-cell';
    public const MAX_COLUMNS_EXCEL = 16384;

    /**
     * Definition of XML attribute used to parse data.
     */
    public const XML_ATTRIBUTE_NUM_ROWS_REPEATED = 'table:number-rows-repeated';
    public const XML_ATTRIBUTE_NUM_COLUMNS_REPEATED = 'table:number-columns-repeated';

    private Options $options;

    /** @var XMLProcessor Helper Object to process XML nodes */
    private XMLProcessor $xmlProcessor;

    /** @var Helper\CellValueFormatter Helper to format cell values */
    private Helper\CellValueFormatter $cellValueFormatter;

    /** @var bool Whether the iterator has already been rewound once */
    private bool $hasAlreadyBeenRewound = false;

    /** @var Row The currently processed row */
    private Row $currentlyProcessedRow;

    /** @var null|Row Buffer used to store the current row, while checking if there are more rows to read */
    private ?Row $rowBuffer;

    /** @var bool Indicates whether all rows have been read */
    private bool $hasReachedEndOfFile = false;

    /** @var int Last row index processed (one-based) */
    private int $lastRowIndexProcessed = 0;

    /** @var int Row index to be processed next (one-based) */
    private int $nextRowIndexToBeProcessed = 1;

    /** @var null|Cell Last processed cell (because when reading cell at column N+1, cell N is processed) */
    private ?Cell $lastProcessedCell;

    /** @var int Number of times the last processed row should be repeated */
    private int $numRowsRepeated = 1;

    /** @var int Number of times the last cell value should be copied to the cells on its right */
    private int $numColumnsRepeated = 1;

    /** @var bool Whether at least one cell has been read for the row currently being processed */
    private bool $hasAlreadyReadOneCellInCurrentRow = false;

    public function __construct(
        Options $options,
        CellValueFormatter $cellValueFormatter,
        XMLProcessor $xmlProcessor
    ) {
        $this->cellValueFormatter = $cellValueFormatter;

        // Register all callbacks to process different nodes when reading the XML file
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_TABLE, XMLProcessor::NODE_TYPE_END, [$this, 'processTableEndingNode']);
        $this->options = $options;
    }

    /**
     * Rewind the Iterator to the first element.
     * NOTE: It can only be done once, as it is not possible to read an XML file backwards.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @throws \OpenSpout\Reader\Exception\IteratorNotRewindableException If the iterator is rewound more than once
     */
    public function rewind(): void
    {
        // Because sheet and row data is located in the file, we can't rewind both the
        // sheet iterator and the row iterator, as XML file cannot be read backwards.
        // Therefore, rewinding the row iterator has been disabled.
        if ($this->hasAlreadyBeenRewound) {
            throw new IteratorNotRewindableException();
        }

        $this->hasAlreadyBeenRewound = true;
        $this->lastRowIndexProcessed = 0;
        $this->nextRowIndexToBeProcessed = 1;
        $this->rowBuffer = null;
        $this->hasReachedEndOfFile = false;

        $this->next();
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return !$this->hasReachedEndOfFile;
    }

    /**
     * Move forward to next element. Empty rows will be skipped.
     *
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @throws \OpenSpout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \OpenSpout\Common\Exception\IOException                   If unable to read the sheet data XML
     */
    public function next(): void
    {
        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }

        ++$this->lastRowIndexProcessed;
    }

    /**
     * Return the current element, from the buffer.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    public function current(): Row
    {
        return $this->rowBuffer;
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        return $this->lastRowIndexProcessed;
    }

    /**
     * Returns whether we need data for the next row to be processed.
     * We DO need to read data if:
     *   - we have not read any rows yet
     *      OR
     *   - the next row to be processed immediately follows the last read row.
     *
     * @return bool whether we need data for the next row to be processed
     */
    private function doesNeedDataForNextRowToBeProcessed(): bool
    {
        $hasReadAtLeastOneRow = (0 !== $this->lastRowIndexProcessed);

        return
            !$hasReadAtLeastOneRow
            || $this->lastRowIndexProcessed === $this->nextRowIndexToBeProcessed - 1;
    }

    /**
     * @throws \OpenSpout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \OpenSpout\Common\Exception\IOException                   If unable to read the sheet data XML
     */
    private function readDataForNextRow(): void
    {
        $this->currentlyProcessedRow = new Row([], null);

        $this->xmlProcessor->readUntilStopped();

        $this->rowBuffer = $this->currentlyProcessedRow;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<table:table-row>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processRowStartingNode(XMLReader $xmlReader): int
    {
        // Reset data from current row
        $this->hasAlreadyReadOneCellInCurrentRow = false;
        $this->lastProcessedCell = null;
        $this->numColumnsRepeated = 1;
        $this->numRowsRepeated = $this->getNumRowsRepeatedForCurrentNode($xmlReader);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<table:table-cell>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processCellStartingNode(XMLReader $xmlReader): int
    {
        $currentNumColumnsRepeated = $this->getNumColumnsRepeatedForCurrentNode($xmlReader);

        // NOTE: expand() will automatically decode all XML entities of the child nodes
        /** @var DOMElement $node */
        $node = $xmlReader->expand();
        $currentCell = $this->getCell($node);

        // process cell N only after having read cell N+1 (see below why)
        if ($this->hasAlreadyReadOneCellInCurrentRow) {
            for ($i = 0; $i < $this->numColumnsRepeated; ++$i) {
                $this->currentlyProcessedRow->addCell($this->lastProcessedCell);
            }
        }

        $this->hasAlreadyReadOneCellInCurrentRow = true;
        $this->lastProcessedCell = $currentCell;
        $this->numColumnsRepeated = $currentNumColumnsRepeated;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processRowEndingNode(): int
    {
        $isEmptyRow = $this->isEmptyRow($this->currentlyProcessedRow, $this->lastProcessedCell);

        // if the fetched row is empty and we don't want to preserve it...
        if (!$this->options->SHOULD_PRESERVE_EMPTY_ROWS && $isEmptyRow) {
            // ... skip it
            return XMLProcessor::PROCESSING_CONTINUE;
        }

        // if the row is empty, we don't want to return more than one cell
        $actualNumColumnsRepeated = (!$isEmptyRow) ? $this->numColumnsRepeated : 1;
        $numCellsInCurrentlyProcessedRow = $this->currentlyProcessedRow->getNumCells();

        // Only add the value if the last read cell is not a trailing empty cell repeater in Excel.
        // The current count of read columns is determined by counting the values in "$this->currentlyProcessedRowData".
        // This is to avoid creating a lot of empty cells, as Excel adds a last empty "<table:table-cell>"
        // with a number-columns-repeated value equals to the number of (supported columns - used columns).
        // In Excel, the number of supported columns is 16384, but we don't want to returns rows with
        // always 16384 cells.
        if (($numCellsInCurrentlyProcessedRow + $actualNumColumnsRepeated) !== self::MAX_COLUMNS_EXCEL) {
            for ($i = 0; $i < $actualNumColumnsRepeated; ++$i) {
                $this->currentlyProcessedRow->addCell($this->lastProcessedCell);
            }
        }

        // If we are processing row N and the row is repeated M times,
        // then the next row to be processed will be row (N+M).
        $this->nextRowIndexToBeProcessed += $this->numRowsRepeated;

        // at this point, we have all the data we need for the row
        // so that we can populate the buffer
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processTableEndingNode(): int
    {
        // The closing "</table:table>" marks the end of the file
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<table:table-row>" starting node
     *
     * @return int The value of "table:number-rows-repeated" attribute of the current node, or 1 if attribute missing
     */
    private function getNumRowsRepeatedForCurrentNode(XMLReader $xmlReader): int
    {
        $numRowsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_ROWS_REPEATED);

        return (null !== $numRowsRepeated) ? (int) $numRowsRepeated : 1;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<table:table-cell>" starting node
     *
     * @return int The value of "table:number-columns-repeated" attribute of the current node, or 1 if attribute missing
     */
    private function getNumColumnsRepeatedForCurrentNode(XMLReader $xmlReader): int
    {
        $numColumnsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_COLUMNS_REPEATED);

        return (null !== $numColumnsRepeated) ? (int) $numColumnsRepeated : 1;
    }

    /**
     * Returns the cell with (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @return Cell The cell set with the associated with the cell
     */
    private function getCell(DOMElement $node): Cell
    {
        try {
            $cellValue = $this->cellValueFormatter->extractAndFormatNodeValue($node);
            $cell = Cell::fromValue($cellValue);
        } catch (InvalidValueException $exception) {
            $cell = new Cell\ErrorCell($exception->getInvalidValue(), null);
        }

        return $cell;
    }

    /**
     * After finishing processing each cell, a row is considered empty if it contains
     * no cells or if the last read cell is empty.
     * After finishing processing each cell, the last read cell is not part of the
     * row data yet (as we still need to apply the "num-columns-repeated" attribute).
     *
     * @param null|Cell $lastReadCell The last read cell
     *
     * @return bool Whether the row is empty
     */
    private function isEmptyRow(Row $currentRow, ?Cell $lastReadCell): bool
    {
        return
            $currentRow->isEmpty()
            && (null === $lastReadCell || $lastReadCell instanceof Cell\EmptyCell);
    }
}
