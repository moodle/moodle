<?php

namespace Box\Spout\Writer\ODS\Internal;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Writer\Common\Helper\CellHelper;
use Box\Spout\Writer\Common\Internal\WorksheetInterface;

/**
 * Class Worksheet
 * Represents a worksheet within a ODS file. The difference with the Sheet object is
 * that this class provides an interface to write data
 *
 * @package Box\Spout\Writer\ODS\Internal
 */
class Worksheet implements WorksheetInterface
{
    /** @var \Box\Spout\Writer\Common\Sheet The "external" sheet */
    protected $externalSheet;

    /** @var string Path to the XML file that will contain the sheet data */
    protected $worksheetFilePath;

    /** @var \Box\Spout\Common\Escaper\ODS Strings escaper */
    protected $stringsEscaper;

    /** @var \Box\Spout\Common\Helper\StringHelper To help with string manipulation */
    protected $stringHelper;

    /** @var Resource Pointer to the temporary sheet data file (e.g. worksheets-temp/sheet1.xml) */
    protected $sheetFilePointer;

    /** @var int Maximum number of columns among all the written rows */
    protected $maxNumColumns = 1;

    /** @var int Index of the last written row */
    protected $lastWrittenRowIndex = 0;

    /**
     * @param \Box\Spout\Writer\Common\Sheet $externalSheet The associated "external" sheet
     * @param string $worksheetFilesFolder Temporary folder where the files to create the ODS will be stored
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    public function __construct($externalSheet, $worksheetFilesFolder)
    {
        $this->externalSheet = $externalSheet;
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->stringsEscaper = \Box\Spout\Common\Escaper\ODS::getInstance();
        $this->worksheetFilePath = $worksheetFilesFolder . '/sheet' . $externalSheet->getIndex() . '.xml';

        $this->stringHelper = new StringHelper();

        $this->startSheet();
    }

    /**
     * Prepares the worksheet to accept data
     * The XML file does not contain the "<table:table>" node as it contains the sheet's name
     * which may change during the execution of the program. It will be added at the end.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    protected function startSheet()
    {
        $this->sheetFilePointer = fopen($this->worksheetFilePath, 'w');
        $this->throwIfSheetFilePointerIsNotAvailable();
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
     * @return string Path to the temporary sheet content XML file
     */
    public function getWorksheetFilePath()
    {
        return $this->worksheetFilePath;
    }

    /**
     * Returns the table XML root node as string.
     *
     * @return string <table> node as string
     */
    public function getTableElementStartAsString()
    {
        $escapedSheetName = $this->stringsEscaper->escape($this->externalSheet->getName());
        $tableStyleName = 'ta' . ($this->externalSheet->getIndex() + 1);

        $tableElement  = '<table:table table:style-name="' . $tableStyleName . '" table:name="' . $escapedSheetName . '">';
        $tableElement .= '<table:table-column table:default-cell-style-name="ce1" table:style-name="co1" table:number-columns-repeated="' . $this->maxNumColumns . '"/>';

        return $tableElement;
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
        // $dataRow can be an associative array. We need to transform
        // it into a regular array, as we'll use the numeric indexes.
        $dataRowWithNumericIndexes = array_values($dataRow);

        $styleIndex = ($style->getId() + 1); // 1-based
        $cellsCount = count($dataRow);
        $this->maxNumColumns = max($this->maxNumColumns, $cellsCount);

        $data = '<table:table-row table:style-name="ro1">';

        $currentCellIndex = 0;
        $nextCellIndex = 1;

        for ($i = 0; $i < $cellsCount; $i++) {
            $currentCellValue = $dataRowWithNumericIndexes[$currentCellIndex];

            // Using isset here because it is way faster than array_key_exists...
            if (!isset($dataRowWithNumericIndexes[$nextCellIndex]) ||
                $currentCellValue !== $dataRowWithNumericIndexes[$nextCellIndex]) {

                $numTimesValueRepeated = ($nextCellIndex - $currentCellIndex);
                $data .= $this->getCellXML($currentCellValue, $styleIndex, $numTimesValueRepeated);

                $currentCellIndex = $nextCellIndex;
            }

            $nextCellIndex++;
        }

        $data .= '</table:table-row>';

        $wasWriteSuccessful = fwrite($this->sheetFilePointer, $data);
        if ($wasWriteSuccessful === false) {
            throw new IOException("Unable to write data in {$this->worksheetFilePath}");
        }

        // only update the count if the write worked
        $this->lastWrittenRowIndex++;
    }

    /**
     * Returns the cell XML content, given its value.
     *
     * @param mixed $cellValue The value to be written
     * @param int $styleIndex Index of the used style
     * @param int $numTimesValueRepeated Number of times the value is consecutively repeated
     * @return string The cell XML content
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    protected function getCellXML($cellValue, $styleIndex, $numTimesValueRepeated)
    {
        $data = '<table:table-cell table:style-name="ce' . $styleIndex . '"';

        if ($numTimesValueRepeated !== 1) {
            $data .= ' table:number-columns-repeated="' . $numTimesValueRepeated . '"';
        }

        if (CellHelper::isNonEmptyString($cellValue)) {
            $data .= ' office:value-type="string" calcext:value-type="string">';

            $cellValueLines = explode("\n", $cellValue);
            foreach ($cellValueLines as $cellValueLine) {
                $data .= '<text:p>' . $this->stringsEscaper->escape($cellValueLine) . '</text:p>';
            }

            $data .= '</table:table-cell>';
        } else if (CellHelper::isBoolean($cellValue)) {
            $data .= ' office:value-type="boolean" calcext:value-type="boolean" office:boolean-value="' . $cellValue . '">';
            $data .= '<text:p>' . $cellValue . '</text:p>';
            $data .= '</table:table-cell>';
        } else if (CellHelper::isNumeric($cellValue)) {
            $data .= ' office:value-type="float" calcext:value-type="float" office:value="' . $cellValue . '">';
            $data .= '<text:p>' . $cellValue . '</text:p>';
            $data .= '</table:table-cell>';
        } else if (empty($cellValue)) {
            $data .= '/>';
        } else {
            throw new InvalidArgumentException('Trying to add a value with an unsupported type: ' . gettype($cellValue));
        }

        return $data;
    }

    /**
     * Closes the worksheet
     *
     * @return void
     */
    public function close()
    {
        if (!is_resource($this->sheetFilePointer)) {
            return;
        }

        fclose($this->sheetFilePointer);
    }
}
