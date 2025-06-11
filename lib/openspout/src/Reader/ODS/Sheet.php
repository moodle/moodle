<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use OpenSpout\Reader\SheetWithVisibilityInterface;

/**
 * @implements SheetWithVisibilityInterface<RowIterator>
 */
final class Sheet implements SheetWithVisibilityInterface
{
    /** @var RowIterator To iterate over sheet's rows */
    private readonly RowIterator $rowIterator;

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    private readonly int $index;

    /** @var string Name of the sheet */
    private readonly string $name;

    /** @var bool Whether the sheet was the active one */
    private readonly bool $isActive;

    /** @var bool Whether the sheet is visible */
    private readonly bool $isVisible;

    /**
     * @param RowIterator $rowIterator    The corresponding row iterator
     * @param int         $sheetIndex     Index of the sheet, based on order in the workbook (zero-based)
     * @param string      $sheetName      Name of the sheet
     * @param bool        $isSheetActive  Whether the sheet was defined as active
     * @param bool        $isSheetVisible Whether the sheet is visible
     */
    public function __construct(RowIterator $rowIterator, int $sheetIndex, string $sheetName, bool $isSheetActive, bool $isSheetVisible)
    {
        $this->rowIterator = $rowIterator;
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
