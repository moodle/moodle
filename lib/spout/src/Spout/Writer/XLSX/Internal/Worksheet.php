<?php

namespace Box\Spout\Writer\XLSX\Internal;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Helper\CellHelper;
use Box\Spout\Writer\Common\Internal\WorksheetInterface;

/**
 * Class Worksheet
 * Represents a worksheet within a XLSX file. The difference with the Sheet object is
 * that this class provides an interface to write data
 *
 * @package Box\Spout\Writer\XLSX\Internal
 */
class Worksheet implements WorksheetInterface
{
    const SHEET_XML_FILE_HEADER = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
EOD;

    /** @var \Box\Spout\Writer\Common\Sheet The "external" sheet */
    protected $externalSheet;

    /** @var string Path to the XML file that will contain the sheet data */
    protected $worksheetFilePath;

    /** @var \Box\Spout\Writer\XLSX\Helper\SharedStringsHelper Helper to write shared strings */
    protected $sharedStringsHelper;

    /** @var bool Whether inline or shared strings should be used */
    protected $shouldUseInlineStrings;

    /** @var \Box\Spout\Common\Escaper\XLSX Strings escaper */
    protected $stringsEscaper;

    /** @var Resource Pointer to the sheet data file (e.g. xl/worksheets/sheet1.xml) */
    protected $sheetFilePointer;

    /** @var int Index of the last written row */
    protected $lastWrittenRowIndex = 0;

    /**
     * @param \Box\Spout\Writer\Common\Sheet $externalSheet The associated "external" sheet
     * @param string $worksheetFilesFolder Temporary folder where the files to create the XLSX will be stored
     * @param \Box\Spout\Writer\XLSX\Helper\SharedStringsHelper $sharedStringsHelper Helper for shared strings
     * @param bool $shouldUseInlineStrings Whether inline or shared strings should be used
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    public function __construct($externalSheet, $worksheetFilesFolder, $sharedStringsHelper, $shouldUseInlineStrings)
    {
        $this->externalSheet = $externalSheet;
        $this->sharedStringsHelper = $sharedStringsHelper;
        $this->shouldUseInlineStrings = $shouldUseInlineStrings;

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->stringsEscaper = new \Box\Spout\Common\Escaper\XLSX();

        $this->worksheetFilePath = $worksheetFilesFolder . '/' . strtolower($this->externalSheet->getName()) . '.xml';
        $this->startSheet();
    }

    /**
     * Prepares the worksheet to accept data
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    protected function startSheet()
    {
        $this->sheetFilePointer = fopen($this->worksheetFilePath, 'w');
        $this->throwIfSheetFilePointerIsNotAvailable();

        fwrite($this->sheetFilePointer, self::SHEET_XML_FILE_HEADER);
        fwrite($this->sheetFilePointer, '<sheetData>');
    }

    /**
     * Checks if the book has been created. Throws an exception if not created yet.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    protected function throwIfSheetFilePointerIsNotAvailable()
    {
        if (!$this->sheetFilePointer) {
            throw new IOException('Unable to open sheet for writing.');
        }
    }

    /**
     * @return \Box\Spout\Writer\Common\Sheet The "external" sheet
     */
    public function getExternalSheet()
    {
        return $this->externalSheet;
    }

    /**
     * @return int The index of the last written row
     */
    public function getLastWrittenRowIndex()
    {
        return $this->lastWrittenRowIndex;
    }

    /**
     * @return int The ID of the worksheet
     */
    public function getId()
    {
        // sheet index is zero-based, while ID is 1-based
        return $this->externalSheet->getIndex() + 1;
    }

    /**
     * Adds data to the worksheet.
     *
     * @param array $dataRow Array containing data to be written. Cannot be empty.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row. NULL means use default style.
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the data cannot be written
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    public function addRow($dataRow, $style)
    {
        $cellNumber = 0;
        $rowIndex = $this->lastWrittenRowIndex + 1;
        $numCells = count($dataRow);

        $rowXML = '<row r="' . $rowIndex . '" spans="1:' . $numCells . '">';

        foreach($dataRow as $cellValue) {
            $columnIndex = CellHelper::getCellIndexFromColumnIndex($cellNumber);
            $cellXML = '<c r="' . $columnIndex . $rowIndex . '"';
            $cellXML .= ' s="' . $style->getId() . '"';

            if (CellHelper::isNonEmptyString($cellValue)) {
                if ($this->shouldUseInlineStrings) {
                    $cellXML .= ' t="inlineStr"><is><t>' . $this->stringsEscaper->escape($cellValue) . '</t></is></c>';
                } else {
                    $sharedStringId = $this->sharedStringsHelper->writeString($cellValue);
                    $cellXML .= ' t="s"><v>' . $sharedStringId . '</v></c>';
                }
            } else if (CellHelper::isBoolean($cellValue)) {
                    $cellXML .= ' t="b"><v>' . intval($cellValue) . '</v></c>';
            } else if (CellHelper::isNumeric($cellValue)) {
                $cellXML .= '><v>' . $cellValue . '</v></c>';
            } else if (empty($cellValue)) {
                // don't write empty cells (not appending to $cellXML is the right behavior!)
                $cellXML = '';
            } else {
                throw new InvalidArgumentException('Trying to add a value with an unsupported type: ' . gettype($cellValue));
            }

            $rowXML .= $cellXML;
            $cellNumber++;
        }

        $rowXML .= '</row>';

        $wasWriteSuccessful = fwrite($this->sheetFilePointer, $rowXML);
        if ($wasWriteSuccessful === false) {
            throw new IOException("Unable to write data in {$this->worksheetFilePath}");
        }

        // only update the count if the write worked
        $this->lastWrittenRowIndex++;
    }

    /**
     * Closes the worksheet
     *
     * @return void
     */
    public function close()
    {
        fwrite($this->sheetFilePointer, '</sheetData>');
        fwrite($this->sheetFilePointer, '</worksheet>');
        fclose($this->sheetFilePointer);
    }
}
