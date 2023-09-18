<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Reader\Common\ColumnWidth;
use OpenSpout\Reader\SheetWithVisibilityInterface;

/**
 * @implements SheetWithVisibilityInterface<RowIterator>
 */
final class Sheet implements SheetWithVisibilityInterface
{
    /** @var RowIterator To iterate over sheet's rows */
    private RowIterator $rowIterator;

    /** @var SheetHeaderReader To read the header of the sheet, containing for instance the col widths */
    private SheetHeaderReader $headerReader;

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    private int $index;

    /** @var string Name of the sheet */
    private string $name;

    /** @var bool Whether the sheet was the active one */
    private bool $isActive;

    /** @var bool Whether the sheet is visible */
    private bool $isVisible;

    /**
     * @param RowIterator $rowIterator    The corresponding row iterator
     * @param int         $sheetIndex     Index of the sheet, based on order in the workbook (zero-based)
     * @param string      $sheetName      Name of the sheet
     * @param bool        $isSheetActive  Whether the sheet was defined as active
     * @param bool        $isSheetVisible Whether the sheet is visible
     */
    public function __construct(RowIterator $rowIterator, SheetHeaderReader $headerReader, int $sheetIndex, string $sheetName, bool $isSheetActive, bool $isSheetVisible)
    {
        $this->rowIterator = $rowIterator;
        $this->headerReader = $headerReader;
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
        $this->isVisible = $isSheetVisible;
    }

    public function getRowIterator(): RowIterator
    {
        return $this->rowIterator;
    }

    /**
     * @return ColumnWidth[] a list of column-widths
     */
    public function getColumnWidths(): array
    {
        return $this->headerReader->getColumnWidths();
    }

    /**
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return string Name of the sheet
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool Whether the sheet was defined as active
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool Whether the sheet is visible
     */
    public function isVisible(): bool
    {
        return $this->isVisible;
    }
}
