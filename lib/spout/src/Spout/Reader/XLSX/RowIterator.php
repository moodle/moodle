<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\XMLProcessingException;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\Wrapper\XMLReader;
use Box\Spout\Reader\XLSX\Helper\CellHelper;
use Box\Spout\Reader\XLSX\Helper\CellValueFormatter;
use Box\Spout\Reader\XLSX\Helper\StyleHelper;
use Box\Spout\Reader\Common\XMLProcessor;

/**
 * Class RowIterator
 *
 * @package Box\Spout\Reader\XLSX
 */
class RowIterator implements IteratorInterface
{
    /** Definition of XML nodes names used to parse data */
    const XML_NODE_DIMENSION = 'dimension';
    const XML_NODE_WORKSHEET = 'worksheet';
    const XML_NODE_ROW = 'row';
    const XML_NODE_CELL = 'c';

    /** Definition of XML attributes used to parse data */
    const XML_ATTRIBUTE_REF = 'ref';
    const XML_ATTRIBUTE_SPANS = 'spans';
    const XML_ATTRIBUTE_ROW_INDEX = 'r';
    const XML_ATTRIBUTE_CELL_INDEX = 'r';

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml */
    protected $sheetDataXMLFilePath;

    /** @var \Box\Spout\Reader\Wrapper\XMLReader The XMLReader object that will help read sheet's XML data */
    protected $xmlReader;

    /** @var \Box\Spout\Reader\Common\XMLProcessor Helper Object to process XML nodes */
    protected $xmlProcessor;

    /** @var Helper\CellValueFormatter Helper to format cell values */
    protected $cellValueFormatter;

    /** @var Helper\StyleHelper $styleHelper Helper to work with styles */
    protected $styleHelper;

    /**
     * TODO: This variable can be deleted when row indices get preserved
     * @var int Number of read rows
     */
    protected $numReadRows = 0;

    /** @var array Contains the data for the currently processed row (key = cell index, value = cell value) */
    protected $currentlyProcessedRowData = [];

    /** @var array|null Buffer used to store the row data, while checking if there are more rows to read */
    protected $rowDataBuffer = null;

    /** @var bool Indicates whether all rows have been read */
    protected $hasReachedEndOfFile = false;

    /** @var int The number of columns the sheet has (0 meaning undefined) */
    protected $numColumns = 0;

    /** @var bool Whether empty rows should be returned or skipped */
    protected $shouldPreserveEmptyRows;

    /** @var int Last row index processed (one-based) */
    protected $lastRowIndexProcessed = 0;

    /** @var int Row index to be processed next (one-based) */
    protected $nextRowIndexToBeProcessed = 0;

    /** @var int Last column index processed (zero-based) */
    protected $lastColumnIndexProcessed = -1;

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param \Box\Spout\Reader\XLSX\ReaderOptions $options Reader's current options
     * @param Helper\SharedStringsHelper $sharedStringsHelper Helper to work with shared strings
     */
    public function __construct($filePath, $sheetDataXMLFilePath, $options, $sharedStringsHelper)
    {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);

        $this->xmlReader = new XMLReader();

        $this->styleHelper = new StyleHelper($filePath);
        $this->cellValueFormatter = new CellValueFormatter($sharedStringsHelper, $this->styleHelper, $options->shouldFormatDates());

        $this->shouldPreserveEmptyRows = $options->shouldPreserveEmptyRows();

        // Register all callbacks to process different nodes when reading the XML file
        $this->xmlProcessor = new XMLProcessor($this->xmlReader);
        $this->xmlProcessor->registerCallback(self::XML_NODE_DIMENSION, XMLProcessor::NODE_TYPE_START, [$this, 'processDimensionStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_WORKSHEET, XMLProcessor::NODE_TYPE_END, [$this, 'processWorksheetEndingNode']);
    }

    /**
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @return string Path of the XML file containing the sheet data,
     *                without the leading slash.
     */
    protected function normalizeSheetDataXMLFilePath($sheetDataXMLFilePath)
    {
        return ltrim($sheetDataXMLFilePath, '/');
    }

    /**
     * Rewind the Iterator to the first element.
     * Initializes the XMLReader object that reads the associated sheet data.
     * The XMLReader is configured to be safe from billion laughs attack.
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data XML cannot be read
     */
    public function rewind()
    {
        $this->xmlReader->close();

        if ($this->xmlReader->openFileInZip($this->filePath, $this->sheetDataXMLFilePath) === false) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        $this->numReadRows = 0;
        $this->lastRowIndexProcessed = 0;
        $this->nextRowIndexToBeProcessed = 0;
        $this->rowDataBuffer = null;
        $this->hasReachedEndOfFile = false;
        $this->numColumns = 0;

        $this->next();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    public function valid()
    {
        return (!$this->hasReachedEndOfFile);
    }

    /**
     * Move forward to next element. Reads data describing the next unprocessed row.
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \Box\Spout\Common\Exception\IOException If unable to read the sheet data XML
     */
    public function next()
    {
        $this->nextRowIndexToBeProcessed++;

        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }
    }

    /**
     * Returns whether we need data for the next row to be processed.
     * We don't need to read data if:
     *   we have already read at least one row
     *     AND
     *   we need to preserve empty rows
     *     AND
     *   the last row that was read is not the row that need to be processed
     *   (i.e. if we need to return empty rows)
     *
     * @return bool Whether we need data for the next row to be processed.
     */
    protected function doesNeedDataForNextRowToBeProcessed()
    {
        $hasReadAtLeastOneRow = ($this->lastRowIndexProcessed !== 0);

        return (
            !$hasReadAtLeastOneRow ||
            !$this->shouldPreserveEmptyRows ||
            $this->lastRowIndexProcessed < $this->nextRowIndexToBeProcessed
        );
    }

    /**
     * @return void
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \Box\Spout\Common\Exception\IOException If unable to read the sheet data XML
     */
    protected function readDataForNextRow()
    {
        $this->currentlyProcessedRowData = [];

        try {
            $this->xmlProcessor->readUntilStopped();
        } catch (XMLProcessingException $exception) {
            throw new IOException("The {$this->sheetDataXMLFilePath} file cannot be read. [{$exception->getMessage()}]");
        }

        $this->rowDataBuffer = $this->currentlyProcessedRowData;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<dimension>" starting node
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processDimensionStartingNode($xmlReader)
    {
        // Read dimensions of the sheet
        $dimensionRef = $xmlReader->getAttribute(self::XML_ATTRIBUTE_REF); // returns 'A1:M13' for instance (or 'A1' for empty sheet)
        if (preg_match('/[A-Z]+\d+:([A-Z]+\d+)/', $dimensionRef, $matches)) {
            $this->numColumns = CellHelper::getColumnIndexFromCellIndex($matches[1]) + 1;
        }

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<row>" starting node
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processRowStartingNode($xmlReader)
    {
        // Reset index of the last processed column
        $this->lastColumnIndexProcessed = -1;

        // Mark the last processed row as the one currently being read
        $this->lastRowIndexProcessed = $this->getRowIndex($xmlReader);

        // Read spans info if present
        $numberOfColumnsForRow = $this->numColumns;
        $spans = $xmlReader->getAttribute(self::XML_ATTRIBUTE_SPANS); // returns '1:5' for instance
        if ($spans) {
            list(, $numberOfColumnsForRow) = explode(':', $spans);
            $numberOfColumnsForRow = intval($numberOfColumnsForRow);
        }

        $this->currentlyProcessedRowData = ($numberOfColumnsForRow !== 0) ? array_fill(0, $numberOfColumnsForRow, '') : [];

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<cell>" starting node
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processCellStartingNode($xmlReader)
    {
        $currentColumnIndex = $this->getColumnIndex($xmlReader);

        // NOTE: expand() will automatically decode all XML entities of the child nodes
        $node = $xmlReader->expand();
        $this->currentlyProcessedRowData[$currentColumnIndex] = $this->getCellValue($node);
        $this->lastColumnIndexProcessed = $currentColumnIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processRowEndingNode()
    {
        // if the fetched row is empty and we don't want to preserve it..,
        if (!$this->shouldPreserveEmptyRows && $this->isEmptyRow($this->currentlyProcessedRowData)) {
            // ... skip it
            return XMLProcessor::PROCESSING_CONTINUE;
        }

        $this->numReadRows++;

        // If needed, we fill the empty cells
        if ($this->numColumns === 0) {
            $this->currentlyProcessedRowData = CellHelper::fillMissingArrayIndexes($this->currentlyProcessedRowData);
        }

        // at this point, we have all the data we need for the row
        // so that we can populate the buffer
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processWorksheetEndingNode()
    {
        // The closing "</worksheet>" marks the end of the file
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<row>" node
     * @return int Row index
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException When the given cell index is invalid
     */
    protected function getRowIndex($xmlReader)
    {
        // Get "r" attribute if present (from something like <row r="3"...>
        $currentRowIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ROW_INDEX);

        return ($currentRowIndex !== null) ?
                intval($currentRowIndex) :
                $this->lastRowIndexProcessed + 1;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<c>" node
     * @return int Column index
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException When the given cell index is invalid
     */
    protected function getColumnIndex($xmlReader)
    {
        // Get "r" attribute if present (from something like <c r="A1"...>
        $currentCellIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_CELL_INDEX);

        return ($currentCellIndex !== null) ?
                CellHelper::getColumnIndexFromCellIndex($currentCellIndex) :
                $this->lastColumnIndexProcessed + 1;
    }

    /**
     * Returns the (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @param \DOMNode $node
     * @return string|int|float|bool|\DateTime|null The value associated with the cell (null when the cell has an error)
     */
    protected function getCellValue($node)
    {
        return $this->cellValueFormatter->extractAndFormatNodeValue($node);
    }

    /**
     * @param array $rowData
     * @return bool Whether the given row is empty
     */
    protected function isEmptyRow($rowData)
    {
        return (count($rowData) === 1 && key($rowData) === '');
    }

    /**
     * Return the current element, either an empty row or from the buffer.
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return array|null
     */
    public function current()
    {
        $rowDataForRowToBeProcessed = $this->rowDataBuffer;

        if ($this->shouldPreserveEmptyRows) {
            // when we need to preserve empty rows, we will either return
            // an empty row or the last row read. This depends whether the
            // index of last row that was read matches the index of the last
            // row whose value should be returned.
            if ($this->lastRowIndexProcessed !== $this->nextRowIndexToBeProcessed) {
                // return empty row if mismatch between last processed row
                // and the row that needs to be returned
                $rowDataForRowToBeProcessed = [''];
            }
        }

        return $rowDataForRowToBeProcessed;
    }

    /**
     * Return the key of the current element. Here, the row index.
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        // TODO: This should return $this->nextRowIndexToBeProcessed
        //       but to avoid a breaking change, the return value for
        //       this function has been kept as the number of rows read.
        return $this->shouldPreserveEmptyRows ?
                $this->nextRowIndexToBeProcessed :
                $this->numReadRows;
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
