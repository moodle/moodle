<?php

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX as XLSXEscaper;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Manager\ManagesCellSize;
use OpenSpout\Writer\Common\Manager\RegisteredStyle;
use OpenSpout\Writer\Common\Manager\RowManager;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Common\Manager\WorksheetManagerInterface;
use OpenSpout\Writer\XLSX\Helper\DateHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;

/**
 * XLSX worksheet manager, providing the interfaces to work with XLSX worksheets.
 */
class WorksheetManager implements WorksheetManagerInterface
{
    use ManagesCellSize;

    /**
     * Maximum number of characters a cell can contain.
     *
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-16c69c74-3d6a-4aaf-ba35-e6eb276e8eaa [Excel 2007]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-1672b34d-7043-467e-8e27-269d656771c3 [Excel 2010]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-ca36e2dc-1f09-4620-b726-67c00b05040f [Excel 2013/2016]
     */
    public const MAX_CHARACTERS_PER_CELL = 32767;

    public const SHEET_XML_FILE_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
        EOD;

    /** @var bool Whether inline or shared strings should be used */
    protected $shouldUseInlineStrings;

    /** @var OptionsManagerInterface */
    private $optionsManager;

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

    /**
     * WorksheetManager constructor.
     */
    public function __construct(
        OptionsManagerInterface $optionsManager,
        RowManager $rowManager,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        SharedStringsManager $sharedStringsManager,
        XLSXEscaper $stringsEscaper,
        StringHelper $stringHelper
    ) {
        $this->optionsManager = $optionsManager;
        $this->shouldUseInlineStrings = $optionsManager->getOption(Options::SHOULD_USE_INLINE_STRINGS);
        $this->setDefaultColumnWidth($optionsManager->getOption(Options::DEFAULT_COLUMN_WIDTH));
        $this->setDefaultRowHeight($optionsManager->getOption(Options::DEFAULT_ROW_HEIGHT));
        $this->columnWidths = $optionsManager->getOption(Options::COLUMN_WIDTHS) ?? [];
        $this->rowManager = $rowManager;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->stringsEscaper = $stringsEscaper;
        $this->stringHelper = $stringHelper;
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
     * Construct column width references xml to inject into worksheet xml file.
     *
     * @return string
     */
    public function getXMLFragmentForColumnWidths()
    {
        if (empty($this->columnWidths)) {
            return '';
        }
        $xml = '<cols>';
        foreach ($this->columnWidths as $entry) {
            $xml .= '<col min="'.$entry[0].'" max="'.$entry[1].'" width="'.$entry[2].'" customWidth="true"/>';
        }
        $xml .= '</cols>';

        return $xml;
    }

    /**
     * Constructs default row height and width xml to inject into worksheet xml file.
     *
     * @return string
     */
    public function getXMLFragmentForDefaultCellSizing()
    {
        $rowHeightXml = empty($this->defaultRowHeight) ? '' : " defaultRowHeight=\"{$this->defaultRowHeight}\"";
        $colWidthXml = empty($this->defaultColumnWidth) ? '' : " defaultColWidth=\"{$this->defaultColumnWidth}\"";
        if (empty($colWidthXml) && empty($rowHeightXml)) {
            return '';
        }
        // Ensure that the required defaultRowHeight is set
        $rowHeightXml = empty($rowHeightXml) ? ' defaultRowHeight="0"' : $rowHeightXml;

        return "<sheetFormatPr{$colWidthXml}{$rowHeightXml}/>";
    }

    /**
     * {@inheritdoc}
     */
    public function close(Worksheet $worksheet)
    {
        $worksheetFilePointer = $worksheet->getFilePointer();

        if (!\is_resource($worksheetFilePointer)) {
            return;
        }
        $this->ensureSheetDataStated($worksheet);
        fwrite($worksheetFilePointer, '</sheetData>');

        // create nodes for merge cells
        if ($this->optionsManager->getOption(Options::MERGE_CELLS)) {
            $mergeCellString = '<mergeCells count="'.\count($this->optionsManager->getOption(Options::MERGE_CELLS)).'">';
            foreach ($this->optionsManager->getOption(Options::MERGE_CELLS) as $values) {
                $output = array_map(function ($value) {
                    return CellHelper::getColumnLettersFromColumnIndex($value[0]).$value[1];
                }, $values);
                $mergeCellString .= '<mergeCell ref="'.implode(':', $output).'"/>';
            }
            $mergeCellString .= '</mergeCells>';
            fwrite($worksheet->getFilePointer(), $mergeCellString);
        }

        fwrite($worksheetFilePointer, '</worksheet>');
        fclose($worksheetFilePointer);
    }

    /**
     * Writes the sheet data header.
     *
     * @param Worksheet $worksheet The worksheet to add the row to
     */
    private function ensureSheetDataStated(Worksheet $worksheet)
    {
        if (!$worksheet->getSheetDataStarted()) {
            $worksheetFilePointer = $worksheet->getFilePointer();
            $sheet = $worksheet->getExternalSheet();
            if ($sheet->hasSheetView()) {
                fwrite($worksheetFilePointer, '<sheetViews>'.$sheet->getSheetView()->getXml().'</sheetViews>');
            }
            fwrite($worksheetFilePointer, $this->getXMLFragmentForDefaultCellSizing());
            fwrite($worksheetFilePointer, $this->getXMLFragmentForColumnWidths());
            fwrite($worksheetFilePointer, '<sheetData>');
            $worksheet->setSheetDataStarted(true);
        }
    }

    /**
     * Checks if the sheet has been sucessfully created. Throws an exception if not.
     *
     * @param bool|resource $sheetFilePointer Pointer to the sheet data file or FALSE if unable to open the file
     *
     * @throws IOException If the sheet data file cannot be opened for writing
     */
    private function throwIfSheetFilePointerIsNotAvailable($sheetFilePointer)
    {
        if (!$sheetFilePointer) {
            throw new IOException('Unable to open sheet for writing.');
        }
    }

    /**
     * Adds non empty row to the worksheet.
     *
     * @param Worksheet $worksheet The worksheet to add the row to
     * @param Row       $row       The row to be written
     *
     * @throws InvalidArgumentException If a cell value's type is not supported
     * @throws IOException              If the data cannot be written
     */
    private function addNonEmptyRow(Worksheet $worksheet, Row $row)
    {
        $this->ensureSheetDataStated($worksheet);
        $sheetFilePointer = $worksheet->getFilePointer();
        $rowStyle = $row->getStyle();
        $rowIndexOneBased = $worksheet->getLastWrittenRowIndex() + 1;
        $numCells = $row->getNumCells();

        $hasCustomHeight = $this->defaultRowHeight > 0 ? '1' : '0';
        $rowXML = "<row r=\"{$rowIndexOneBased}\" spans=\"1:{$numCells}\" customHeight=\"{$hasCustomHeight}\">";

        foreach ($row->getCells() as $columnIndexZeroBased => $cell) {
            $registeredStyle = $this->applyStyleAndRegister($cell, $rowStyle);
            $cellStyle = $registeredStyle->getStyle();
            if ($registeredStyle->isMatchingRowStyle()) {
                $rowStyle = $cellStyle; // Replace actual rowStyle (possibly with null id) by registered style (with id)
            }
            $rowXML .= $this->getCellXML($rowIndexOneBased, $columnIndexZeroBased, $cell, $cellStyle->getId());
        }

        $rowXML .= '</row>';

        $wasWriteSuccessful = fwrite($sheetFilePointer, $rowXML);
        if (false === $wasWriteSuccessful) {
            throw new IOException("Unable to write data in {$worksheet->getFilePath()}");
        }
    }

    /**
     * Applies styles to the given style, merging the cell's style with its row's style.
     *
     * @throws InvalidArgumentException If the given value cannot be processed
     */
    private function applyStyleAndRegister(Cell $cell, Style $rowStyle): RegisteredStyle
    {
        $isMatchingRowStyle = false;
        if ($cell->getStyle()->isEmpty()) {
            $cell->setStyle($rowStyle);

            $possiblyUpdatedStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

            if ($possiblyUpdatedStyle->isUpdated()) {
                $registeredStyle = $this->styleManager->registerStyle($possiblyUpdatedStyle->getStyle());
            } else {
                $registeredStyle = $this->styleManager->registerStyle($rowStyle);
                $isMatchingRowStyle = true;
            }
        } else {
            $mergedCellAndRowStyle = $this->styleMerger->merge($cell->getStyle(), $rowStyle);
            $cell->setStyle($mergedCellAndRowStyle);

            $possiblyUpdatedStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

            if ($possiblyUpdatedStyle->isUpdated()) {
                $newCellStyle = $possiblyUpdatedStyle->getStyle();
            } else {
                $newCellStyle = $mergedCellAndRowStyle;
            }

            $registeredStyle = $this->styleManager->registerStyle($newCellStyle);
        }

        return new RegisteredStyle($registeredStyle, $isMatchingRowStyle);
    }

    /**
     * Builds and returns xml for a single cell.
     *
     * @param int $rowIndexOneBased
     * @param int $columnIndexZeroBased
     * @param int $styleId
     *
     * @throws InvalidArgumentException If the given value cannot be processed
     *
     * @return string
     */
    private function getCellXML($rowIndexOneBased, $columnIndexZeroBased, Cell $cell, $styleId)
    {
        $columnLetters = CellHelper::getColumnLettersFromColumnIndex($columnIndexZeroBased);
        $cellXML = '<c r="'.$columnLetters.$rowIndexOneBased.'"';
        $cellXML .= ' s="'.$styleId.'"';

        if ($cell->isString()) {
            $cellXML .= $this->getCellXMLFragmentForNonEmptyString($cell->getValue());
        } elseif ($cell->isBoolean()) {
            $cellXML .= ' t="b"><v>'.(int) ($cell->getValue()).'</v></c>';
        } elseif ($cell->isNumeric()) {
            $cellXML .= '><v>'.$this->stringHelper->formatNumericValue($cell->getValue()).'</v></c>';
        } elseif ($cell->isFormula()) {
            $cellXML .= '><f>'.substr($cell->getValue(), 1).'</f></c>';
        } elseif ($cell->isDate()) {
            $value = $cell->getValue();
            if ($value instanceof \DateTimeInterface) {
                $cellXML .= '><v>'.(string) DateHelper::toExcel($value).'</v></c>';
            } else {
                throw new InvalidArgumentException('Trying to add a date value with an unsupported type: '.\gettype($value));
            }
        } elseif ($cell->isError() && \is_string($cell->getValueEvenIfError())) {
            // only writes the error value if it's a string
            $cellXML .= ' t="e"><v>'.$cell->getValueEvenIfError().'</v></c>';
        } elseif ($cell->isEmpty()) {
            if ($this->styleManager->shouldApplyStyleOnEmptyCell($styleId)) {
                $cellXML .= '/>';
            } else {
                // don't write empty cells that do no need styling
                // NOTE: not appending to $cellXML is the right behavior!!
                $cellXML = '';
            }
        } else {
            throw new InvalidArgumentException('Trying to add a value with an unsupported type: '.\gettype($cell->getValue()));
        }

        return $cellXML;
    }

    /**
     * Returns the XML fragment for a cell containing a non empty string.
     *
     * @param string $cellValue The cell value
     *
     * @throws InvalidArgumentException If the string exceeds the maximum number of characters allowed per cell
     *
     * @return string The XML fragment representing the cell
     */
    private function getCellXMLFragmentForNonEmptyString($cellValue)
    {
        if ($this->stringHelper->getStringLength($cellValue) > self::MAX_CHARACTERS_PER_CELL) {
            throw new InvalidArgumentException('Trying to add a value that exceeds the maximum number of characters allowed in a cell (32,767)');
        }

        if ($this->shouldUseInlineStrings) {
            $cellXMLFragment = ' t="inlineStr"><is><t>'.$this->stringsEscaper->escape($cellValue).'</t></is></c>';
        } else {
            $sharedStringId = $this->sharedStringsManager->writeString($cellValue);
            $cellXMLFragment = ' t="s"><v>'.$sharedStringId.'</v></c>';
        }

        return $cellXMLFragment;
    }
}
