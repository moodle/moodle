<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Reader\SheetInterface;

/**
 * Class Sheet
 * Represents a sheet within a XLSX file
 *
 * @package Box\Spout\Reader\XLSX
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

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $sheetName Name of the sheet
     * @param bool $isSheetActive Whether the sheet was defined as active
     * @param \Box\Spout\Reader\XLSX\ReaderOptions $options Reader's current options
     * @param Helper\SharedStringsHelper Helper to work with shared strings
     */
    public function __construct($filePath, $sheetDataXMLFilePath, $sheetIndex, $sheetName, $isSheetActive, $options, $sharedStringsHelper)
    {
        $this->rowIterator = new RowIterator($filePath, $sheetDataXMLFilePath, $options, $sharedStringsHelper);
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
    }

    /**
     * @api
     * @return \Box\Spout\Reader\XLSX\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * @api
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @api
     * @return string Name of the sheet
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @api
     * @return bool Whether the sheet was defined as active
     */
    public function isActive()
    {
        return $this->isActive;
    }
}
