<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Reader\SheetInterface;

/**
 * Class Sheet
 * Represents a sheet within a XLSX file
 */
class Sheet implements SheetInterface
{
    /** @var \Box\Spout\Reader\XLSX\RowIterator To iterate over sheet's rows */
    protected $rowIterator;

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    protected $index;

    /** @var string Name of the sheet */
    protected $name;

    /** @var bool Whether the sheet was the active one */
    protected $isActive;

    /** @var bool Whether the sheet is visible */
    protected $isVisible;

    /**
     * @param RowIterator $rowIterator The corresponding row iterator
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $sheetName Name of the sheet
     * @param bool $isSheetActive Whether the sheet was defined as active
     * @param bool $isSheetVisible Whether the sheet is visible
     */
    public function __construct($rowIterator, $sheetIndex, $sheetName, $isSheetActive, $isSheetVisible)
    {
        $this->rowIterator = $rowIterator;
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
        $this->isVisible = $isSheetVisible;
    }

    /**
     * @return \Box\Spout\Reader\XLSX\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string Name of the sheet
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool Whether the sheet was defined as active
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return bool Whether the sheet is visible
     */
    public function isVisible()
    {
        return $this->isVisible;
    }
}
