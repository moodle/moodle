<?php

namespace Box\Spout\Writer\XLSX\Manager;

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Helper\Escaper\XLSX as XLSXEscaper;
use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Common\Manager\OptionsManagerInterface;
use Box\Spout\Writer\Common\Creator\InternalEntityFactory;
use Box\Spout\Writer\Common\Entity\Options;
use Box\Spout\Writer\Common\Entity\Worksheet;
use Box\Spout\Writer\Common\Helper\CellHelper;
use Box\Spout\Writer\Common\Manager\RowManager;
use Box\Spout\Writer\Common\Manager\Style\StyleMerger;
use Box\Spout\Writer\Common\Manager\WorksheetManagerInterface;
use Box\Spout\Writer\XLSX\Manager\Style\StyleManager;

/**
 * Class WorksheetManager
 * XLSX worksheet manager, providing the interfaces to work with XLSX worksheets.
 */
class WorksheetManager implements WorksheetManagerInterface
{
    /**
     * Maximum number of characters a cell can contain
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-16c69c74-3d6a-4aaf-ba35-e6eb276e8eaa [Excel 2007]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-1672b34d-7043-467e-8e27-269d656771c3 [Excel 2010]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-ca36e2dc-1f09-4620-b726-67c00b05040f [Excel 2013/2016]
     */
    const MAX_CHARACTERS_PER_CELL = 32767;

    const SHEET_XML_FILE_HEADER = <<<'EOD'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
EOD;

    /** @var bool Whether inline or shared strings should be used */
    protected $shouldUseInlineStrings;

    /** @var RowManager Manages rows */
    private $rowManager;

    /** @var StyleManager Manages styles */
    private $styleManager;

    /** @var StyleMerger Helper to merge styles together */
    private $styleMerger;

    /** @var SharedStringsManager Helper to write shared strings */
    private $sharedStringsManager;

    /** @var XLSXEscaper Strings escaper */
    private $stringsEscaper;

    /** @var StringHelper String helper */
    private $stringHelper;

    /** @var InternalEntityFactory Factory to create entities */
    private $entityFactory;

    /**
     * WorksheetManager constructor.
     *
     * @param OptionsManagerInterface $optionsManager
     * @param RowManager $rowManager
     * @param StyleManager $styleManager
     * @param StyleMerger $styleMerger
     * @param SharedStringsManager $sharedStringsManager
     * @param XLSXEscaper $stringsEscaper
     * @param StringHelper $stringHelper
     * @param InternalEntityFactory $entityFactory
     */
    public function __construct(
        OptionsManagerInterface $optionsManager,
        RowManager $rowManager,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        SharedStringsManager $sharedStringsManager,
        XLSXEscaper $stringsEscaper,
        StringHelper $stringHelper,
        InternalEntityFactory $entityFactory
    ) {
        $this->shouldUseInlineStrings = $optionsManager->getOption(Options::SHOULD_USE_INLINE_STRINGS);
        $this->rowManager = $rowManager;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->stringsEscaper = $stringsEscaper;
        $this->stringHelper = $stringHelper;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return SharedStringsManager
     */
    public function getSharedStringsManager()
    {
        return $this->sharedStringsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function startSheet(Worksheet $worksheet)
    {
        $sheetFilePointer = fopen($worksheet->getFilePath(), 'w');
        $this->throwIfSheetFilePointerIsNotAvailable($sheetFilePointer);

        $worksheet->setFilePointer($sheetFilePointer);

        fwrite($sheetFilePointer, self::SHEET_XML_FILE_HEADER);
        fwrite($sheetFilePointer, '<sheetData>');
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
     * {@inheritdoc}
     */
    public function addRow(Worksheet $worksheet, Row $row)
    {
        if (!$this->rowManager->isEmpty($row)) {
            $this->addNonEmptyRow($worksheet, $row);
        }

        $worksheet->setLastWrittenRowIndex($worksheet->getLastWrittenRowIndex() + 1);
    }

    /**
     * Adds non empty row to the worksheet.
     *
     * @param Worksheet $worksheet The worksheet to add the row to
     * @param Row $row The row to be written
     * @throws IOException If the data cannot be written
     * @throws InvalidArgumentException If a cell value's type is not supported
     * @return void
     */
    private function addNonEmptyRow(Worksheet $worksheet, Row $row)
    {
        $cellIndex = 0;
        $rowStyle = $row->getStyle();
        $rowIndex = $worksheet->getLastWrittenRowIndex() + 1;
        $numCells = $row->getNumCells();

        $rowXML = '<row r="' . $rowIndex . '" spans="1:' . $numCells . '">';

        foreach ($row->getCells() as $cell) {
            $rowXML .= $this->applyStyleAndGetCellXML($cell, $rowStyle, $rowIndex, $cellIndex);
            $cellIndex++;
        }

        $rowXML .= '</row>';

        $wasWriteSuccessful = fwrite($worksheet->getFilePointer(), $rowXML);
        if ($wasWriteSuccessful === false) {
            throw new IOException("Unable to write data in {$worksheet->getFilePath()}");
        }
    }

    /**
     * Applies styles to the given style, merging the cell's style with its row's style
     * Then builds and returns xml for the cell.
     *
     * @param Cell $cell
     * @param Style $rowStyle
     * @param int $rowIndex
     * @param int $cellIndex
     * @throws InvalidArgumentException If the given value cannot be processed
     * @return string
     */
    private function applyStyleAndGetCellXML(Cell $cell, Style $rowStyle, $rowIndex, $cellIndex)
    {
        // Apply row and extra styles
        $mergedCellAndRowStyle = $this->styleMerger->merge($cell->getStyle(), $rowStyle);
        $cell->setStyle($mergedCellAndRowStyle);
        $newCellStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

        $registeredStyle = $this->styleManager->registerStyle($newCellStyle);

        return $this->getCellXML($rowIndex, $cellIndex, $cell, $registeredStyle->getId());
    }

    /**
     * Builds and returns xml for a single cell.
     *
     * @param int $rowIndex
     * @param int $cellNumber
     * @param Cell $cell
     * @param int $styleId
     * @throws InvalidArgumentException If the given value cannot be processed
     * @return string
     */
    private function getCellXML($rowIndex, $cellNumber, Cell $cell, $styleId)
    {
        $columnIndex = CellHelper::getCellIndexFromColumnIndex($cellNumber);
        $cellXML = '<c r="' . $columnIndex . $rowIndex . '"';
        $cellXML .= ' s="' . $styleId . '"';

        if ($cell->isString()) {
            $cellXML .= $this->getCellXMLFragmentForNonEmptyString($cell->getValue());
        } elseif ($cell->isBoolean()) {
            $cellXML .= ' t="b"><v>' . (int) ($cell->getValue()) . '</v></c>';
        } elseif ($cell->isNumeric()) {
            $cellXML .= '><v>' . $cell->getValue() . '</v></c>';
        } elseif ($cell->isEmpty()) {
            if ($this->styleManager->shouldApplyStyleOnEmptyCell($styleId)) {
                $cellXML .= '/>';
            } else {
                // don't write empty cells that do no need styling
                // NOTE: not appending to $cellXML is the right behavior!!
                $cellXML = '';
            }
        } else {
            throw new InvalidArgumentException('Trying to add a value with an unsupported type: ' . gettype($cell->getValue()));
        }

        return $cellXML;
    }

    /**
     * Returns the XML fragment for a cell containing a non empty string
     *
     * @param string $cellValue The cell value
     * @throws InvalidArgumentException If the string exceeds the maximum number of characters allowed per cell
     * @return string The XML fragment representing the cell
     */
    private function getCellXMLFragmentForNonEmptyString($cellValue)
    {
        if ($this->stringHelper->getStringLength($cellValue) > self::MAX_CHARACTERS_PER_CELL) {
            throw new InvalidArgumentException('Trying to add a value that exceeds the maximum number of characters allowed in a cell (32,767)');
        }

        if ($this->shouldUseInlineStrings) {
            $cellXMLFragment = ' t="inlineStr"><is><t>' . $this->stringsEscaper->escape($cellValue) . '</t></is></c>';
        } else {
            $sharedStringId = $this->sharedStringsManager->writeString($cellValue);
            $cellXMLFragment = ' t="s"><v>' . $sharedStringId . '</v></c>';
        }

        return $cellXMLFragment;
    }

    /**
     * {@inheritdoc}
     */
    public function close(Worksheet $worksheet)
    {
        $worksheetFilePointer = $worksheet->getFilePointer();

        if (!is_resource($worksheetFilePointer)) {
            return;
        }

        fwrite($worksheetFilePointer, '</sheetData>');
        fwrite($worksheetFilePointer, '</worksheet>');
        fclose($worksheetFilePointer);
    }
}
