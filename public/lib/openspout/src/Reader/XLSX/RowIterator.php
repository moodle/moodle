<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use DOMElement;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Common\Manager\RowManager;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Exception\SharedStringNotFoundException;
use OpenSpout\Reader\RowIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;
use OpenSpout\Reader\XLSX\Helper\CellHelper;
use OpenSpout\Reader\XLSX\Helper\CellValueFormatter;

final class RowIterator implements RowIteratorInterface
{
    /**
     * Definition of XML nodes names used to parse data.
     */
    public const XML_NODE_DIMENSION = 'dimension';
    public const XML_NODE_WORKSHEET = 'worksheet';
    public const XML_NODE_ROW = 'row';
    public const XML_NODE_CELL = 'c';

    /**
     * Definition of XML attributes used to parse data.
     */
    public const XML_ATTRIBUTE_REF = 'ref';
    public const XML_ATTRIBUTE_SPANS = 'spans';
    public const XML_ATTRIBUTE_ROW_INDEX = 'r';
    public const XML_ATTRIBUTE_CELL_INDEX = 'r';

    /** @var string Path of the XLSX file being read */
    private readonly string $filePath;

    /** @var string Path of the sheet data XML file as in [Content_Types].xml */
    private readonly string $sheetDataXMLFilePath;

    /** @var XMLReader The XMLReader object that will help read sheet's XML data */
    private readonly XMLReader $xmlReader;

    /** @var XMLProcessor Helper Object to process XML nodes */
    private readonly XMLProcessor $xmlProcessor;

    /** @var CellValueFormatter Helper to format cell values */
    private readonly CellValueFormatter $cellValueFormatter;

    /** @var RowManager Manages rows */
    private readonly RowManager $rowManager;

    /**
     * TODO: This variable can be deleted when row indices get preserved.
     *
     * @var int Number of read rows
     */
    private int $numReadRows = 0;

    /** @var Row Contains the row currently processed */
    private Row $currentlyProcessedRow;

    /** @var null|Row Buffer used to store the current row, while checking if there are more rows to read */
    private ?Row $rowBuffer = null;

    /** @var bool Indicates whether all rows have been read */
    private bool $hasReachedEndOfFile = false;

    /** @var int The number of columns the sheet has (0 meaning undefined) */
    private int $numColumns = 0;

    /** @var bool Whether empty rows should be returned or skipped */
    private readonly bool $shouldPreserveEmptyRows;

    /** @var int Last row index processed (one-based) */
    private int $lastRowIndexProcessed = 0;

    /** @var int Row index to be processed next (one-based) */
    private int $nextRowIndexToBeProcessed = 0;

    /** @var int Last column index processed (zero-based) */
    private int $lastColumnIndexProcessed = -1;

    /**
     * @param string             $filePath                Path of the XLSX file being read
     * @param string             $sheetDataXMLFilePath    Path of the sheet data XML file as in [Content_Types].xml
     * @param bool               $shouldPreserveEmptyRows Whether empty rows should be preserved
     * @param XMLReader          $xmlReader               XML Reader
     * @param XMLProcessor       $xmlProcessor            Helper to process XML files
     * @param CellValueFormatter $cellValueFormatter      Helper to format cell values
     * @param RowManager         $rowManager              Manages rows
     */
    public function __construct(
        string $filePath,
        string $sheetDataXMLFilePath,
        bool $shouldPreserveEmptyRows,
        XMLReader $xmlReader,
        XMLProcessor $xmlProcessor,
        CellValueFormatter $cellValueFormatter,
        RowManager $rowManager
    ) {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);
        $this->shouldPreserveEmptyRows = $shouldPreserveEmptyRows;
        $this->xmlReader = $xmlReader;
        $this->cellValueFormatter = $cellValueFormatter;
        $this->rowManager = $rowManager;

        // Register all callbacks to process different nodes when reading the XML file
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_DIMENSION, XMLProcessor::NODE_TYPE_START, [$this, 'processDimensionStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_WORKSHEET, XMLProcessor::NODE_TYPE_END, [$this, 'processWorksheetEndingNode']);
    }

    /**
     * Rewind the Iterator to the first element.
     * Initializes the XMLReader object that reads the associated sheet data.
     * The XMLReader is configured to be safe from billion laughs attack.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @throws IOException If the sheet data XML cannot be read
     */
    public function rewind(): void
    {
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, $this->sheetDataXMLFilePath)) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        $this->numReadRows = 0;
        $this->lastRowIndexProcessed = 0;
        $this->nextRowIndexToBeProcessed = 0;
        $this->rowBuffer = null;
        $this->hasReachedEndOfFile = false;
        $this->numColumns = 0;

        $this->next();
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        $valid = !$this->hasReachedEndOfFile;
        if (!$valid) {
            $this->xmlReader->close();
        }

        return $valid;
    }

    /**
     * Move forward to next element. Reads data describing the next unprocessed row.
     *
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @throws SharedStringNotFoundException If a shared string was not found
     * @throws IOException                   If unable to read the sheet data XML
     */
    public function next(): void
    {
        ++$this->nextRowIndexToBeProcessed;

        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }
    }

    /**
     * Return the current element, either an empty row or from the buffer.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    public function current(): Row
    {
        $rowToBeProcessed = $this->rowBuffer;

        if ($this->shouldPreserveEmptyRows) {
            // when we need to preserve empty rows, we will either return
            // an empty row or the last row read. This depends whether the
            // index of last row that was read matches the index of the last
            // row whose value should be returned.
            if ($this->lastRowIndexProcessed !== $this->nextRowIndexToBeProcessed) {
                // return empty row if mismatch between last processed row
                // and the row that needs to be returned
                $rowToBeProcessed = new Row([], null);
            }
        }

        \assert(null !== $rowToBeProcessed);

        return $rowToBeProcessed;
    }

    /**
     * Return the key of the current element. Here, the row index.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        // TODO: This should return $this->nextRowIndexToBeProcessed
        //       but to avoid a breaking change, the return value for
        //       this function has been kept as the number of rows read.
        return $this->shouldPreserveEmptyRows ?
                $this->nextRowIndexToBeProcessed :
                $this->numReadRows;
    }

    /**
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     *
     * @return string path of the XML file containing the sheet data,
     *                without the leading slash
     */
    private function normalizeSheetDataXMLFilePath(string $sheetDataXMLFilePath): string
    {
        return ltrim($sheetDataXMLFilePath, '/');
    }

    /**
     * Returns whether we need data for the next row to be processed.
     * We don't need to read data if:
     *   we have already read at least one row
     *     AND
     *   we need to preserve empty rows
     *     AND
     *   the last row that was read is not the row that need to be processed
     *   (i.e. if we need to return empty rows).
     *
     * @return bool whether we need data for the next row to be processed
     */
    private function doesNeedDataForNextRowToBeProcessed(): bool
    {
        $hasReadAtLeastOneRow = (0 !== $this->lastRowIndexProcessed);

        return
            !$hasReadAtLeastOneRow
            || !$this->shouldPreserveEmptyRows
            || $this->lastRowIndexProcessed < $this->nextRowIndexToBeProcessed;
    }

    /**
     * @throws SharedStringNotFoundException If a shared string was not found
     * @throws IOException                   If unable to read the sheet data XML
     */
    private function readDataForNextRow(): void
    {
        $this->currentlyProcessedRow = new Row([], null);

        $this->xmlProcessor->readUntilStopped();

        $this->rowBuffer = $this->currentlyProcessedRow;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<dimension>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processDimensionStartingNode(XMLReader $xmlReader): int
    {
        // Read dimensions of the sheet
        $dimensionRef = $xmlReader->getAttribute(self::XML_ATTRIBUTE_REF); // returns 'A1:M13' for instance (or 'A1' for empty sheet)
        \assert(null !== $dimensionRef);
        if (1 === preg_match('/[A-Z]+\d+:([A-Z]+\d+)/', $dimensionRef, $matches)) {
            $this->numColumns = CellHelper::getColumnIndexFromCellIndex($matches[1]) + 1;
        }

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<row>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processRowStartingNode(XMLReader $xmlReader): int
    {
        // Reset index of the last processed column
        $this->lastColumnIndexProcessed = -1;

        // Mark the last processed row as the one currently being read
        $this->lastRowIndexProcessed = $this->getRowIndex($xmlReader);

        // Read spans info if present
        $numberOfColumnsForRow = $this->numColumns;
        $spans = $xmlReader->getAttribute(self::XML_ATTRIBUTE_SPANS); // returns '1:5' for instance
        if (null !== $spans && '' !== $spans) {
            [, $numberOfColumnsForRow] = explode(':', $spans);
            $numberOfColumnsForRow = (int) $numberOfColumnsForRow;
        }

        $cells = array_fill(0, $numberOfColumnsForRow, Cell::fromValue(''));
        $this->currentlyProcessedRow->setCells($cells);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<cell>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processCellStartingNode(XMLReader $xmlReader): int
    {
        $currentColumnIndex = $this->getColumnIndex($xmlReader);

        // NOTE: expand() will automatically decode all XML entities of the child nodes
        $node = $xmlReader->expand();
        \assert($node instanceof DOMElement);
        $cell = $this->cellValueFormatter->extractAndFormatNodeValue($node);

        $this->currentlyProcessedRow->setCellAtIndex($cell, $currentColumnIndex);
        $this->lastColumnIndexProcessed = $currentColumnIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processRowEndingNode(): int
    {
        // if the fetched row is empty and we don't want to preserve it..,
        if (!$this->shouldPreserveEmptyRows && $this->currentlyProcessedRow->isEmpty()) {
            // ... skip it
            return XMLProcessor::PROCESSING_CONTINUE;
        }

        ++$this->numReadRows;

        // If needed, we fill the empty cells
        if (0 === $this->numColumns) {
            $this->rowManager->fillMissingIndexesWithEmptyCells($this->currentlyProcessedRow);
        }

        // at this point, we have all the data we need for the row
        // so that we can populate the buffer
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processWorksheetEndingNode(): int
    {
        // The closing "</worksheet>" marks the end of the file
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<row>" node
     *
     * @return int Row index
     *
     * @throws InvalidArgumentException When the given cell index is invalid
     */
    private function getRowIndex(XMLReader $xmlReader): int
    {
        // Get "r" attribute if present (from something like <row r="3"...>
        $currentRowIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ROW_INDEX);

        return (null !== $currentRowIndex) ?
                (int) $currentRowIndex :
                $this->lastRowIndexProcessed + 1;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<c>" node
     *
     * @return int Column index
     *
     * @throws InvalidArgumentException When the given cell index is invalid
     */
    private function getColumnIndex(XMLReader $xmlReader): int
    {
        // Get "r" attribute if present (from something like <c r="A1"...>
        $currentCellIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_CELL_INDEX);

        return (null !== $currentCellIndex) ?
                CellHelper::getColumnIndexFromCellIndex($currentCellIndex) :
                $this->lastColumnIndexProcessed + 1;
    }
}
