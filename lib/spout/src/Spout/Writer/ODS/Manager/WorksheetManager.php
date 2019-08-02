<?php

namespace Box\Spout\Writer\ODS\Manager;

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Helper\Escaper\ODS as ODSEscaper;
use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Writer\Common\Entity\Worksheet;
use Box\Spout\Writer\Common\Manager\Style\StyleMerger;
use Box\Spout\Writer\Common\Manager\WorksheetManagerInterface;
use Box\Spout\Writer\ODS\Manager\Style\StyleManager;

/**
 * Class WorksheetManager
 * ODS worksheet manager, providing the interfaces to work with ODS worksheets.
 */
class WorksheetManager implements WorksheetManagerInterface
{
    /** @var \Box\Spout\Common\Helper\Escaper\ODS Strings escaper */
    private $stringsEscaper;

    /** @var StringHelper String helper */
    private $stringHelper;

    /** @var StyleManager Manages styles */
    private $styleManager;

    /** @var StyleMerger Helper to merge styles together */
    private $styleMerger;

    /**
     * WorksheetManager constructor.
     *
     * @param StyleManager $styleManager
     * @param StyleMerger $styleMerger
     * @param ODSEscaper $stringsEscaper
     * @param StringHelper $stringHelper
     */
    public function __construct(
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        ODSEscaper $stringsEscaper,
        StringHelper $stringHelper
    ) {
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->stringsEscaper = $stringsEscaper;
        $this->stringHelper = $stringHelper;
    }

    /**
     * Prepares the worksheet to accept data
     *
     * @param Worksheet $worksheet The worksheet to start
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     * @return void
     */
    public function startSheet(Worksheet $worksheet)
    {
        $sheetFilePointer = fopen($worksheet->getFilePath(), 'w');
        $this->throwIfSheetFilePointerIsNotAvailable($sheetFilePointer);

        $worksheet->setFilePointer($sheetFilePointer);
    }

    /**
     * Checks if the sheet has been sucessfully created. Throws an exception if not.
     *
     * @param bool|resource $sheetFilePointer Pointer to the sheet data file or FALSE if unable to open the file
     * @throws IOException If the sheet data file cannot be opened for writing
     * @return void
     */
    private function throwIfSheetFilePointerIsNotAvailable($sheetFilePointer)
    {
        if (!$sheetFilePointer) {
            throw new IOException('Unable to open sheet for writing.');
        }
    }

    /**
     * Returns the table XML root node as string.
     *
     * @param Worksheet $worksheet
     * @return string <table> node as string
     */
    public function getTableElementStartAsString(Worksheet $worksheet)
    {
        $externalSheet = $worksheet->getExternalSheet();
        $escapedSheetName = $this->stringsEscaper->escape($externalSheet->getName());
        $tableStyleName = 'ta' . ($externalSheet->getIndex() + 1);

        $tableElement  = '<table:table table:style-name="' . $tableStyleName . '" table:name="' . $escapedSheetName . '">';
        $tableElement .= '<table:table-column table:default-cell-style-name="ce1" table:style-name="co1" table:number-columns-repeated="' . $worksheet->getMaxNumColumns() . '"/>';

        return $tableElement;
    }

    /**
     * Adds a row to the given worksheet.
     *
     * @param Worksheet $worksheet The worksheet to add the row to
     * @param Row $row The row to be added
     * @throws IOException If the data cannot be written
     * @throws InvalidArgumentException If a cell value's type is not supported
     * @return void
     */
    public function addRow(Worksheet $worksheet, Row $row)
    {
        $cells = $row->getCells();
        $rowStyle = $row->getStyle();

        $data = '<table:table-row table:style-name="ro1">';

        $currentCellIndex = 0;
        $nextCellIndex = 1;

        for ($i = 0; $i < $row->getNumCells(); $i++) {
            /** @var Cell $cell */
            $cell = $cells[$currentCellIndex];
            /** @var Cell|null $nextCell */
            $nextCell = isset($cells[$nextCellIndex]) ? $cells[$nextCellIndex] : null;

            if ($nextCell === null || $cell->getValue() !== $nextCell->getValue()) {
                $data .= $this->applyStyleAndGetCellXML($cell, $rowStyle, $currentCellIndex, $nextCellIndex);
                $currentCellIndex = $nextCellIndex;
            }

            $nextCellIndex++;
        }

        $data .= '</table:table-row>';

        $wasWriteSuccessful = fwrite($worksheet->getFilePointer(), $data);
        if ($wasWriteSuccessful === false) {
            throw new IOException("Unable to write data in {$worksheet->getFilePath()}");
        }

        // only update the count if the write worked
        $lastWrittenRowIndex = $worksheet->getLastWrittenRowIndex();
        $worksheet->setLastWrittenRowIndex($lastWrittenRowIndex + 1);
    }

    /**
     * Applies styles to the given style, merging the cell's style with its row's style
     * Then builds and returns xml for the cell.
     *
     * @param Cell $cell
     * @param Style $rowStyle
     * @param int $currentCellIndex
     * @param int $nextCellIndex
     * @throws InvalidArgumentException If a cell value's type is not supported
     * @return string
     */
    private function applyStyleAndGetCellXML(Cell $cell, Style $rowStyle, $currentCellIndex, $nextCellIndex)
    {
        // Apply row and extra styles
        $mergedCellAndRowStyle = $this->styleMerger->merge($cell->getStyle(), $rowStyle);
        $cell->setStyle($mergedCellAndRowStyle);
        $newCellStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

        $registeredStyle = $this->styleManager->registerStyle($newCellStyle);
        $styleIndex = $registeredStyle->getId() + 1; // 1-based

        $numTimesValueRepeated = ($nextCellIndex - $currentCellIndex);

        return $this->getCellXML($cell, $styleIndex, $numTimesValueRepeated);
    }

    /**
     * Returns the cell XML content, given its value.
     *
     * @param Cell $cell The cell to be written
     * @param int $styleIndex Index of the used style
     * @param int $numTimesValueRepeated Number of times the value is consecutively repeated
     * @throws InvalidArgumentException If a cell value's type is not supported
     * @return string The cell XML content
     */
    private function getCellXML(Cell $cell, $styleIndex, $numTimesValueRepeated)
    {
        $data = '<table:table-cell table:style-name="ce' . $styleIndex . '"';

        if ($numTimesValueRepeated !== 1) {
            $data .= ' table:number-columns-repeated="' . $numTimesValueRepeated . '"';
        }

        if ($cell->isString()) {
            $data .= ' office:value-type="string" calcext:value-type="string">';

            $cellValueLines = explode("\n", $cell->getValue());
            foreach ($cellValueLines as $cellValueLine) {
                $data .= '<text:p>' . $this->stringsEscaper->escape($cellValueLine) . '</text:p>';
            }

            $data .= '</table:table-cell>';
        } elseif ($cell->isBoolean()) {
            $data .= ' office:value-type="boolean" calcext:value-type="boolean" office:boolean-value="' . $cell->getValue() . '">';
            $data .= '<text:p>' . $cell->getValue() . '</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell->isNumeric()) {
            $data .= ' office:value-type="float" calcext:value-type="float" office:value="' . $cell->getValue() . '">';
            $data .= '<text:p>' . $cell->getValue() . '</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell->isEmpty()) {
            $data .= '/>';
        } else {
            throw new InvalidArgumentException('Trying to add a value with an unsupported type: ' . gettype($cell->getValue()));
        }

        return $data;
    }

    /**
     * Closes the worksheet
     *
     * @param Worksheet $worksheet
     * @return void
     */
    public function close(Worksheet $worksheet)
    {
        $worksheetFilePointer = $worksheet->getFilePointer();

        if (!is_resource($worksheetFilePointer)) {
            return;
        }

        fclose($worksheetFilePointer);
    }
}
