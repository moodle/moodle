<?php

namespace Box\Spout\Writer\Common\Internal;

use Box\Spout\Writer\Exception\SheetNotFoundException;

/**
 * Class Workbook
 * Represents a workbook within a spreadsheet file.
 * It provides the functions to work with worksheets.
 *
 * @package Box\Spout\Writer\Common
 */
abstract class AbstractWorkbook implements WorkbookInterface
{
    /** @var bool Whether new sheets should be automatically created when the max rows limit per sheet is reached */
    protected $shouldCreateNewSheetsAutomatically;

    /** @var WorksheetInterface[] Array containing the workbook's sheets */
    protected $worksheets = [];

    /** @var WorksheetInterface The worksheet where data will be written to */
    protected $currentWorksheet;

    /**
     * @param bool $shouldCreateNewSheetsAutomatically
     * @param \Box\Spout\Writer\Style\Style $defaultRowStyle
     * @throws \Box\Spout\Common\Exception\IOException If unable to create at least one of the base folders
     */
    public function __construct($shouldCreateNewSheetsAutomatically, $defaultRowStyle)
    {
        $this->shouldCreateNewSheetsAutomatically = $shouldCreateNewSheetsAutomatically;
    }

    /**
     * @return \Box\Spout\Writer\Common\Helper\AbstractStyleHelper The specific style helper
     */
    abstract protected function getStyleHelper();

    /**
     * @return int Maximum number of rows/columns a sheet can contain
     */
    abstract protected function getMaxRowsPerWorksheet();

    /**
     * Creates a new sheet in the workbook. The current sheet remains unchanged.
     *
     * @return WorksheetInterface The created sheet
     * @throws \Box\Spout\Common\Exception\IOException If unable to open the sheet for writing
     */
    abstract public function addNewSheet();

    /**
     * Creates a new sheet in the workbook and make it the current sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @return WorksheetInterface The created sheet
     * @throws \Box\Spout\Common\Exception\IOException If unable to open the sheet for writing
     */
    public function addNewSheetAndMakeItCurrent()
    {
        $worksheet = $this->addNewSheet();
        $this->setCurrentWorksheet($worksheet);

        return $worksheet;
    }

    /**
     * @return WorksheetInterface[] All the workbook's sheets
     */
    public function getWorksheets()
    {
        return $this->worksheets;
    }

    /**
     * Returns the current sheet
     *
     * @return WorksheetInterface The current sheet
     */
    public function getCurrentWorksheet()
    {
        return $this->currentWorksheet;
    }

    /**
     * Sets the given sheet as the current one. New data will be written to this sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @param \Box\Spout\Writer\Common\Sheet $sheet The "external" sheet to set as current
     * @return void
     * @throws \Box\Spout\Writer\Exception\SheetNotFoundException If the given sheet does not exist in the workbook
     */
    public function setCurrentSheet($sheet)
    {
        $worksheet = $this->getWorksheetFromExternalSheet($sheet);
        if ($worksheet !== null) {
            $this->currentWorksheet = $worksheet;
        } else {
            throw new SheetNotFoundException('The given sheet does not exist in the workbook.');
        }
    }

    /**
     * @param WorksheetInterface $worksheet
     * @return void
     */
    protected function setCurrentWorksheet($worksheet)
    {
        $this->currentWorksheet = $worksheet;
    }

    /**
     * Returns the worksheet associated to the given external sheet.
     *
     * @param \Box\Spout\Writer\Common\Sheet $sheet
     * @return WorksheetInterface|null The worksheet associated to the given external sheet or null if not found.
     */
    protected function getWorksheetFromExternalSheet($sheet)
    {
        $worksheetFound = null;

        foreach ($this->worksheets as $worksheet) {
            if ($worksheet->getExternalSheet() === $sheet) {
                $worksheetFound = $worksheet;
                break;
            }
        }

        return $worksheetFound;
    }

    /**
     * Adds data to the current sheet.
     * If shouldCreateNewSheetsAutomatically option is set to true, it will handle pagination
     * with the creation of new worksheets if one worksheet has reached its maximum capicity.
     *
     * @param array $dataRow Array containing data to be written. Cannot be empty.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row.
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If trying to create a new sheet and unable to open the sheet for writing
     * @throws \Box\Spout\Writer\Exception\WriterException If unable to write data
     */
    public function addRowToCurrentWorksheet($dataRow, $style)
    {
        $currentWorksheet = $this->getCurrentWorksheet();
        $hasReachedMaxRows = $this->hasCurrentWorkseetReachedMaxRows();
        $styleHelper = $this->getStyleHelper();

        // if we reached the maximum number of rows for the current sheet...
        if ($hasReachedMaxRows) {
            // ... continue writing in a new sheet if option set
            if ($this->shouldCreateNewSheetsAutomatically) {
                $currentWorksheet = $this->addNewSheetAndMakeItCurrent();

                $updatedStyle = $styleHelper->applyExtraStylesIfNeeded($style, $dataRow);
                $registeredStyle = $styleHelper->registerStyle($updatedStyle);
                $currentWorksheet->addRow($dataRow, $registeredStyle);
            } else {
                // otherwise, do nothing as the data won't be read anyways
            }
        } else {
            $updatedStyle = $styleHelper->applyExtraStylesIfNeeded($style, $dataRow);
            $registeredStyle = $styleHelper->registerStyle($updatedStyle);
            $currentWorksheet->addRow($dataRow, $registeredStyle);
        }
    }

    /**
     * @return bool Whether the current worksheet has reached the maximum number of rows per sheet.
     */
    protected function hasCurrentWorkseetReachedMaxRows()
    {
        $currentWorksheet = $this->getCurrentWorksheet();
        return ($currentWorksheet->getLastWrittenRowIndex() >= $this->getMaxRowsPerWorksheet());
    }

    /**
     * Closes the workbook and all its associated sheets.
     * All the necessary files are written to disk and zipped together to create the ODS file.
     * All the temporary files are then deleted.
     *
     * @param resource $finalFilePointer Pointer to the ODS that will be created
     * @return void
     */
    abstract public function close($finalFilePointer);
}
