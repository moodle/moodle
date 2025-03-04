<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Reader\Common\ColumnWidth;
use OpenSpout\Reader\SheetWithMergeCellsInterface;
use OpenSpout\Reader\SheetWithVisibilityInterface;

/**
 * @implements SheetWithVisibilityInterface<RowIterator>
 * @implements SheetWithMergeCellsInterface<RowIterator>
 */
final readonly class Sheet implements SheetWithVisibilityInterface, SheetWithMergeCellsInterface
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

    /** @var list<string> Merge cells list ["C7:E7", "A9:D10"] */
    private array $mergeCells;

    /**
     * @param RowIterator  $rowIterator    The corresponding row iterator
     * @param int          $sheetIndex     Index of the sheet, based on order in the workbook (zero-based)
     * @param string       $sheetName      Name of the sheet
     * @param bool         $isSheetActive  Whether the sheet was defined as active
     * @param bool         $isSheetVisible Whether the sheet is visible
     * @param list<string> $mergeCells     Merge cells list ["C7:E7", "A9:D10"]
     */
    public function __construct(
        RowIterator $rowIterator,
        SheetHeaderReader $headerReader,
        int $sheetIndex,
        string $sheetName,
        bool $isSheetActive,
        bool $isSheetVisible,
        array $mergeCells
    ) {
        $this->rowIterator = $rowIterator;
        $this->headerReader = $headerReader;
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
        $this->isVisible = $isSheetVisible;
        $this->mergeCells = $mergeCells;
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

    /**
     * @return list<string> Merge cells list ["C7:E7", "A9:D10"]
     */
    public function getMergeCells(): array
    {
        return $this->mergeCells;
    }
}
