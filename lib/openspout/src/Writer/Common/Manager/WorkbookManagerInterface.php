<?php

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Exception\SheetNotFoundException;
use OpenSpout\Writer\Exception\WriterException;

/**
 * Interface WorkbookManagerInterface
 * workbook manager interface, providing the generic interfaces to work with workbook.
 */
interface WorkbookManagerInterface
{
    /**
     * @return null|Workbook
     */
    public function getWorkbook();

    /**
     * Creates a new sheet in the workbook and make it the current sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @throws IOException If unable to open the sheet for writing
     *
     * @return Worksheet The created sheet
     */
    public function addNewSheetAndMakeItCurrent();

    /**
     * @return Worksheet[] All the workbook's sheets
     */
    public function getWorksheets();

    /**
     * Returns the current sheet.
     *
     * @return Worksheet The current sheet
     */
    public function getCurrentWorksheet();

    /**
     * Starts the current sheet and opens its file pointer.
     */
    public function startCurrentSheet();

    public function setDefaultColumnWidth(float $width);

    public function setDefaultRowHeight(float $height);

    /**
     * @param int ...$columns One or more columns with this width
     */
    public function setColumnWidth(float $width, ...$columns);

    /**
     * @param float $width The width to set
     * @param int   $start First column index of the range
     * @param int   $end   Last column index of the range
     */
    public function setColumnWidthForRange(float $width, int $start, int $end);

    /**
     * Sets the given sheet as the current one. New data will be written to this sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @param Sheet $sheet The "external" sheet to set as current
     *
     * @throws SheetNotFoundException If the given sheet does not exist in the workbook
     */
    public function setCurrentSheet(Sheet $sheet);

    /**
     * Adds a row to the current sheet.
     * If shouldCreateNewSheetsAutomatically option is set to true, it will handle pagination
     * with the creation of new worksheets if one worksheet has reached its maximum capicity.
     *
     * @param Row $row The row to be added
     *
     * @throws IOException     If trying to create a new sheet and unable to open the sheet for writing
     * @throws WriterException If unable to write data
     */
    public function addRowToCurrentWorksheet(Row $row);

    /**
     * Closes the workbook and all its associated sheets.
     * All the necessary files are written to disk and zipped together to create the final file.
     * All the temporary files are then deleted.
     *
     * @param resource $finalFilePointer Pointer to the spreadsheet that will be created
     */
    public function close($finalFilePointer);
}
