<?php

namespace Box\Spout\Reader\ODS;

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Manager\OptionsManagerInterface;
use Box\Spout\Reader\Common\Entity\Options;
use Box\Spout\Reader\Common\Manager\RowManager;
use Box\Spout\Reader\Common\XMLProcessor;
use Box\Spout\Reader\Exception\InvalidValueException;
use Box\Spout\Reader\Exception\IteratorNotRewindableException;
use Box\Spout\Reader\Exception\XMLProcessingException;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\ODS\Creator\InternalEntityFactory;
use Box\Spout\Reader\ODS\Helper\CellValueFormatter;
use Box\Spout\Reader\Wrapper\XMLReader;

/**
 * Class RowIterator
 */
class RowIterator implements IteratorInterface
{
    /** Definition of XML nodes names used to parse data */
    const XML_NODE_TABLE = 'table:table';
    const XML_NODE_ROW = 'table:table-row';
    const XML_NODE_CELL = 'table:table-cell';
    const MAX_COLUMNS_EXCEL = 16384;

    /** Definition of XML attribute used to parse data */
    const XML_ATTRIBUTE_NUM_ROWS_REPEATED = 'table:number-rows-repeated';
    const XML_ATTRIBUTE_NUM_COLUMNS_REPEATED = 'table:number-columns-repeated';

    /** @var \Box\Spout\Reader\Wrapper\XMLReader The XMLReader object that will help read sheet's XML data */
    protected $xmlReader;

    /** @var \Box\Spout\Reader\Common\XMLProcessor Helper Object to process XML nodes */
    protected $xmlProcessor;

    /** @var bool Whether empty rows should be returned or skipped */
    protected $shouldPreserveEmptyRows;

    /** @var Helper\CellValueFormatter Helper to format cell values */
    protected $cellValueFormatter;

    /** @var RowManager Manages rows */
    protected $rowManager;

    /** @var InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var bool Whether the iterator has already been rewound once */
    protected $hasAlreadyBeenRewound = false;

    /** @var Row The currently processed row */
    protected $currentlyProcessedRow;

    /** @var Row Buffer used to store the current row, while checking if there are more rows to read */
    protected $rowBuffer;

    /** @var bool Indicates whether all rows have been read */
    protected $hasReachedEndOfFile = false;

    /** @var int Last row index processed (one-based) */
    protected $lastRowIndexProcessed = 0;

    /** @var int Row index to be processed next (one-based) */
    protected $nextRowIndexToBeProcessed = 1;

    /** @var Cell Last processed cell (because when reading cell at column N+1, cell N is processed) */
    protected $lastProcessedCell;

    /** @var int Number of times the last processed row should be repeated */
    protected $numRowsRepeated = 1;

    /** @var int Number of times the last cell value should be copied to the cells on its right */
    protected $numColumnsRepeated = 1;

    /** @var bool Whether at least one cell has been read for the row currently being processed */
    protected $hasAlreadyReadOneCellInCurrentRow = false;

    /**
     * @param XMLReader $xmlReader XML Reader, positioned on the "<table:table>" element
     * @param OptionsManagerInterface $optionsManager Reader's options manager
     * @param CellValueFormatter $cellValueFormatter Helper to format cell values
     * @param XMLProcessor $xmlProcessor Helper to process XML files
     * @param RowManager $rowManager Manages rows
     * @param InternalEntityFactory $entityFactory Factory to create entities
     */
    public function __construct(
        XMLReader $xmlReader,
        OptionsManagerInterface $optionsManager,
        CellValueFormatter $cellValueFormatter,
        XMLProcessor $xmlProcessor,
        RowManager $rowManager,
        InternalEntityFactory $entityFactory
    ) {
        $this->xmlReader = $xmlReader;
        $this->shouldPreserveEmptyRows = $optionsManager->getOption(Options::SHOULD_PRESERVE_EMPTY_ROWS);
        $this->cellValueFormatter = $cellValueFormatter;
        $this->entityFactory = $entityFactory;
        $this->rowManager = $rowManager;

        // Register all callbacks to process different nodes when reading the XML file
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_TABLE, XMLProcessor::NODE_TYPE_END, [$this, 'processTableEndingNode']);
    }

    /**
     * Rewind the Iterator to the first element.
     * NOTE: It can only be done once, as it is not possible to read an XML file backwards.
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @throws \Box\Spout\Reader\Exception\IteratorNotRewindableException If the iterator is rewound more than once
     * @return void
     */
    public function rewind()
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
     * Checks if current position is valid
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    public function valid()
    {
        return (!$this->hasReachedEndOfFile);
    }

    /**
     * Move forward to next element. Empty rows will be skipped.
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \Box\Spout\Common\Exception\IOException If unable to read the sheet data XML
     * @return void
     */
    public function next()
    {
        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }

        $this->lastRowIndexProcessed++;
    }

    /**
     * Returns whether we need data for the next row to be processed.
     * We DO need to read data if:
     *   - we have not read any rows yet
     *      OR
     *   - the next row to be processed immediately follows the last read row
     *
     * @return bool Whether we need data for the next row to be processed.
     */
    protected function doesNeedDataForNextRowToBeProcessed()
    {
        $hasReadAtLeastOneRow = ($this->lastRowIndexProcessed !== 0);

        return (
            !$hasReadAtLeastOneRow ||
            $this->lastRowIndexProcessed === $this->nextRowIndexToBeProcessed - 1
        );
    }

    /**
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \Box\Spout\Common\Exception\IOException If unable to read the sheet data XML
     * @return void
     */
    protected function readDataForNextRow()
    {
        $this->currentlyProcessedRow = $this->entityFactory->createRow();

        try {
            $this->xmlProcessor->readUntilStopped();
        } catch (XMLProcessingException $exception) {
            throw new IOException("The sheet's data cannot be read. [{$exception->getMessage()}]");
        }

        $this->rowBuffer = $this->currentlyProcessedRow;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<table:table-row>" starting node
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processRowStartingNode($xmlReader)
    {
        // Reset data from current row
        $this->hasAlreadyReadOneCellInCurrentRow = false;
        $this->lastProcessedCell = null;
        $this->numColumnsRepeated = 1;
        $this->numRowsRepeated = $this->getNumRowsRepeatedForCurrentNode($xmlReader);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<table:table-cell>" starting node
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processCellStartingNode($xmlReader)
    {
        $currentNumColumnsRepeated = $this->getNumColumnsRepeatedForCurrentNode($xmlReader);

        // NOTE: expand() will automatically decode all XML entities of the child nodes
        $node = $xmlReader->expand();
        $currentCell = $this->getCell($node);

        // process cell N only after having read cell N+1 (see below why)
        if ($this->hasAlreadyReadOneCellInCurrentRow) {
            for ($i = 0; $i < $this->numColumnsRepeated; $i++) {
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
    protected function processRowEndingNode()
    {
        $isEmptyRow = $this->isEmptyRow($this->currentlyProcessedRow, $this->lastProcessedCell);

        // if the fetched row is empty and we don't want to preserve it...
        if (!$this->shouldPreserveEmptyRows && $isEmptyRow) {
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
            for ($i = 0; $i < $actualNumColumnsRepeated; $i++) {
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
    protected function processTableEndingNode()
    {
        // The closing "</table:table>" marks the end of the file
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<table:table-row>" starting node
     * @return int The value of "table:number-rows-repeated" attribute of the current node, or 1 if attribute missing
     */
    protected function getNumRowsRepeatedForCurrentNode($xmlReader)
    {
        $numRowsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_ROWS_REPEATED);

        return ($numRowsRepeated !== null) ? (int) $numRowsRepeated : 1;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<table:table-cell>" starting node
     * @return int The value of "table:number-columns-repeated" attribute of the current node, or 1 if attribute missing
     */
    protected function getNumColumnsRepeatedForCurrentNode($xmlReader)
    {
        $numColumnsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_COLUMNS_REPEATED);

        return ($numColumnsRepeated !== null) ? (int) $numColumnsRepeated : 1;
    }

    /**
     * Returns the cell with (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @param \DOMNode $node
     * @return Cell The cell set with the associated with the cell
     */
    protected function getCell($node)
    {
        try {
            $cellValue = $this->cellValueFormatter->extractAndFormatNodeValue($node);
            $cell = $this->entityFactory->createCell($cellValue);
        } catch (InvalidValueException $exception) {
            $cell = $this->entityFactory->createCell($exception->getInvalidValue());
            $cell->setType(Cell::TYPE_ERROR);
        }

        return $cell;
    }

    /**
     * After finishing processing each cell, a row is considered empty if it contains
     * no cells or if the last read cell is empty.
     * After finishing processing each cell, the last read cell is not part of the
     * row data yet (as we still need to apply the "num-columns-repeated" attribute).
     *
     * @param Row $currentRow
     * @param Cell $lastReadCell The last read cell
     * @return bool Whether the row is empty
     */
    protected function isEmptyRow($currentRow, $lastReadCell)
    {
        return (
            $this->rowManager->isEmpty($currentRow) &&
            (!isset($lastReadCell) || $lastReadCell->isEmpty())
        );
    }

    /**
     * Return the current element, from the buffer.
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @return Row
     */
    public function current()
    {
        return $this->rowBuffer;
    }

    /**
     * Return the key of the current element
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return $this->lastRowIndexProcessed;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end()
    {
        $this->xmlReader->close();
    }
}
