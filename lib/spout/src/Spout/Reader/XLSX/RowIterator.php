<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\XMLProcessingException;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\Wrapper\XMLReader;
use Box\Spout\Reader\XLSX\Helper\CellHelper;
use Box\Spout\Reader\XLSX\Helper\CellValueFormatter;
use Box\Spout\Reader\XLSX\Helper\StyleHelper;

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
    const XML_ATTRIBUTE_CELL_INDEX = 'r';

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml */
    protected $sheetDataXMLFilePath;

    /** @var \Box\Spout\Reader\Wrapper\XMLReader The XMLReader object that will help read sheet's XML data */
    protected $xmlReader;

    /** @var Helper\CellValueFormatter Helper to format cell values */
    protected $cellValueFormatter;

    /** @var Helper\StyleHelper $styleHelper Helper to work with styles */
    protected $styleHelper;

    /** @var int Number of read rows */
    protected $numReadRows = 0;

    /** @var array|null Buffer used to store the row data, while checking if there are more rows to read */
    protected $rowDataBuffer = null;

    /** @var bool Indicates whether all rows have been read */
    protected $hasReachedEndOfFile = false;

    /** @var int The number of columns the sheet has (0 meaning undefined) */
    protected $numColumns = 0;

    /** @var int Last column index processed (zero-based) */
    protected $lastColumnIndexProcessed = -1;

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param Helper\SharedStringsHelper $sharedStringsHelper Helper to work with shared strings
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     */
    public function __construct($filePath, $sheetDataXMLFilePath, $sharedStringsHelper, $shouldFormatDates)
    {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);

        $this->xmlReader = new XMLReader();

        $this->styleHelper = new StyleHelper($filePath);
        $this->cellValueFormatter = new CellValueFormatter($sharedStringsHelper, $this->styleHelper, $shouldFormatDates);
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

        $sheetDataFilePath = 'zip://' . $this->filePath . '#' . $this->sheetDataXMLFilePath;
        if ($this->xmlReader->open($sheetDataFilePath) === false) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        $this->numReadRows = 0;
        $this->rowDataBuffer = null;
        $this->hasReachedEndOfFile = false;
        $this->numColumns = 0;

        $this->next();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean
     */
    public function valid()
    {
        return (!$this->hasReachedEndOfFile);
    }

    /**
     * Move forward to next element. Empty rows will be skipped.
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If a shared string was not found
     * @throws \Box\Spout\Common\Exception\IOException If unable to read the sheet data XML
     */
    public function next()
    {
        $rowData = [];

        try {
            while ($this->xmlReader->read()) {
                if ($this->xmlReader->isPositionedOnStartingNode(self::XML_NODE_DIMENSION)) {
                    // Read dimensions of the sheet
                    $dimensionRef = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_REF); // returns 'A1:M13' for instance (or 'A1' for empty sheet)
                    if (preg_match('/[A-Z\d]+:([A-Z\d]+)/', $dimensionRef, $matches)) {
                        $lastCellIndex = $matches[1];
                        $this->numColumns = CellHelper::getColumnIndexFromCellIndex($lastCellIndex) + 1;
                    }

                } else if ($this->xmlReader->isPositionedOnStartingNode(self::XML_NODE_ROW)) {
                    // Start of the row description

                    // Reset index of the last processed column
                    $this->lastColumnIndexProcessed = -1;

                    // Read spans info if present
                    $numberOfColumnsForRow = $this->numColumns;
                    $spans = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_SPANS); // returns '1:5' for instance
                    if ($spans) {
                        list(, $numberOfColumnsForRow) = explode(':', $spans);
                        $numberOfColumnsForRow = intval($numberOfColumnsForRow);
                    }
                    $rowData = ($numberOfColumnsForRow !== 0) ? array_fill(0, $numberOfColumnsForRow, '') : [];

                } else if ($this->xmlReader->isPositionedOnStartingNode(self::XML_NODE_CELL)) {
                    // Start of a cell description
                    $currentColumnIndex = $this->getCellIndex($this->xmlReader);

                    $node = $this->xmlReader->expand();
                    $rowData[$currentColumnIndex] = $this->getCellValue($node);

                    $this->lastColumnIndexProcessed = $currentColumnIndex;

                } else if ($this->xmlReader->isPositionedOnEndingNode(self::XML_NODE_ROW)) {
                    // End of the row description
                    // If needed, we fill the empty cells
                    $rowData = ($this->numColumns !== 0) ? $rowData : CellHelper::fillMissingArrayIndexes($rowData);
                    $this->numReadRows++;
                    break;

                } else if ($this->xmlReader->isPositionedOnEndingNode(self::XML_NODE_WORKSHEET)) {
                    // The closing "</worksheet>" marks the end of the file
                    $this->hasReachedEndOfFile = true;
                    break;
                }
            }

        } catch (XMLProcessingException $exception) {
            throw new IOException("The {$this->sheetDataXMLFilePath} file cannot be read. [{$exception->getMessage()}]");
        }

        $this->rowDataBuffer = $rowData;
    }

    /**
     * @param \Box\Spout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<c>" tag
     * @return int
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException When the given cell index is invalid
     */
    protected function getCellIndex($xmlReader)
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
     * Return the current element, from the buffer.
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return array|null
     */
    public function current()
    {
        return $this->rowDataBuffer;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return $this->numReadRows;
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
