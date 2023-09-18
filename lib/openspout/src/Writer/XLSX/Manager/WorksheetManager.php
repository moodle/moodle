<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX as XLSXEscaper;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Manager\RegisteredStyle;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Common\Manager\WorksheetManagerInterface;
use OpenSpout\Writer\XLSX\Helper\DateHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\Options;

/**
 * @internal
 */
final class WorksheetManager implements WorksheetManagerInterface
{
    /**
     * Maximum number of characters a cell can contain.
     *
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-16c69c74-3d6a-4aaf-ba35-e6eb276e8eaa [Excel 2007]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-1672b34d-7043-467e-8e27-269d656771c3 [Excel 2010]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-ca36e2dc-1f09-4620-b726-67c00b05040f [Excel 2013/2016]
     */
    public const MAX_CHARACTERS_PER_CELL = 32767;

    /** @var CommentsManager Manages comments */
    private CommentsManager $commentsManager;

    private Options $options;

    /** @var StyleManager Manages styles */
    private StyleManager $styleManager;

    /** @var StyleMerger Helper to merge styles together */
    private StyleMerger $styleMerger;

    /** @var SharedStringsManager Helper to write shared strings */
    private SharedStringsManager $sharedStringsManager;

    /** @var XLSXEscaper Strings escaper */
    private XLSXEscaper $stringsEscaper;

    /** @var StringHelper String helper */
    private StringHelper $stringHelper;

    /**
     * WorksheetManager constructor.
     */
    public function __construct(
        Options $options,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        CommentsManager $commentsManager,
        SharedStringsManager $sharedStringsManager,
        XLSXEscaper $stringsEscaper,
        StringHelper $stringHelper
    ) {
        $this->options = $options;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->commentsManager = $commentsManager;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->stringsEscaper = $stringsEscaper;
        $this->stringHelper = $stringHelper;
    }

    public function getSharedStringsManager(): SharedStringsManager
    {
        return $this->sharedStringsManager;
    }

    public function startSheet(Worksheet $worksheet): void
    {
        $sheetFilePointer = fopen($worksheet->getFilePath(), 'w');
        \assert(false !== $sheetFilePointer);

        $worksheet->setFilePointer($sheetFilePointer);
        $this->commentsManager->createWorksheetCommentFiles($worksheet);
    }

    public function addRow(Worksheet $worksheet, Row $row): void
    {
        if (!$row->isEmpty()) {
            $this->addNonEmptyRow($worksheet, $row);
            $this->commentsManager->addComments($worksheet, $row);
        }

        $worksheet->setLastWrittenRowIndex($worksheet->getLastWrittenRowIndex() + 1);
    }

    public function close(Worksheet $worksheet): void
    {
        $this->commentsManager->closeWorksheetCommentFiles($worksheet);
        fclose($worksheet->getFilePointer());
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
    private function addNonEmptyRow(Worksheet $worksheet, Row $row): void
    {
        $sheetFilePointer = $worksheet->getFilePointer();
        $rowStyle = $row->getStyle();
        $rowIndexOneBased = $worksheet->getLastWrittenRowIndex() + 1;
        $numCells = $row->getNumCells();

        $rowHeight = $row->getHeight();
        $hasCustomHeight = ($this->options->DEFAULT_ROW_HEIGHT > 0 || $rowHeight > 0) ? '1' : '0';
        $rowXML = "<row r=\"{$rowIndexOneBased}\" spans=\"1:{$numCells}\" ".($rowHeight > 0 ? "ht=\"{$rowHeight}\" " : '')."customHeight=\"{$hasCustomHeight}\">";

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
     * @throws InvalidArgumentException If the given value cannot be processed
     */
    private function getCellXML(int $rowIndexOneBased, int $columnIndexZeroBased, Cell $cell, ?int $styleId): string
    {
        $columnLetters = CellHelper::getColumnLettersFromColumnIndex($columnIndexZeroBased);
        $cellXML = '<c r="'.$columnLetters.$rowIndexOneBased.'"';
        $cellXML .= ' s="'.$styleId.'"';

        if ($cell instanceof Cell\StringCell) {
            $cellXML .= $this->getCellXMLFragmentForNonEmptyString($cell->getValue());
        } elseif ($cell instanceof Cell\BooleanCell) {
            $cellXML .= ' t="b"><v>'.(int) $cell->getValue().'</v></c>';
        } elseif ($cell instanceof Cell\NumericCell) {
            $cellXML .= '><v>'.$cell->getValue().'</v></c>';
        } elseif ($cell instanceof Cell\FormulaCell) {
            $cellXML .= '><f>'.substr($cell->getValue(), 1).'</f></c>';
        } elseif ($cell instanceof Cell\DateTimeCell) {
            $cellXML .= '><v>'.DateHelper::toExcel($cell->getValue()).'</v></c>';
        } elseif ($cell instanceof Cell\ErrorCell) {
            // only writes the error value if it's a string
            $cellXML .= ' t="e"><v>'.$cell->getRawValue().'</v></c>';
        } elseif ($cell instanceof Cell\EmptyCell) {
            if ($this->styleManager->shouldApplyStyleOnEmptyCell($styleId)) {
                $cellXML .= '/>';
            } else {
                // don't write empty cells that do no need styling
                // NOTE: not appending to $cellXML is the right behavior!!
                $cellXML = '';
            }
        }

        return $cellXML;
    }

    /**
     * Returns the XML fragment for a cell containing a non empty string.
     *
     * @param string $cellValue The cell value
     *
     * @return string The XML fragment representing the cell
     *
     * @throws InvalidArgumentException If the string exceeds the maximum number of characters allowed per cell
     */
    private function getCellXMLFragmentForNonEmptyString(string $cellValue): string
    {
        if ($this->stringHelper->getStringLength($cellValue) > self::MAX_CHARACTERS_PER_CELL) {
            throw new InvalidArgumentException('Trying to add a value that exceeds the maximum number of characters allowed in a cell (32,767)');
        }

        if ($this->options->SHOULD_USE_INLINE_STRINGS) {
            $cellXMLFragment = ' t="inlineStr"><is><t>'.$this->stringsEscaper->escape($cellValue).'</t></is></c>';
        } else {
            $sharedStringId = $this->sharedStringsManager->writeString($cellValue);
            $cellXMLFragment = ' t="s"><v>'.$sharedStringId.'</v></c>';
        }

        return $cellXMLFragment;
    }
}
