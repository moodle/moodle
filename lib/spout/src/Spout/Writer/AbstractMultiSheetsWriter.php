<?php

namespace Box\Spout\Writer;

use Box\Spout\Writer\Exception\WriterNotOpenedException;

/**
 * Class AbstractMultiSheetsWriter
 *
 * @package Box\Spout\Writer
 * @abstract
 */
abstract class AbstractMultiSheetsWriter extends AbstractWriter
{
    /** @var bool Whether new sheets should be automatically created when the max rows limit per sheet is reached */
    protected $shouldCreateNewSheetsAutomatically = true;

    /**
     * @return Common\Internal\WorkbookInterface The workbook representing the file to be written
     */
    abstract protected function getWorkbook();

    /**
     * Sets whether new sheets should be automatically created when the max rows limit per sheet is reached.
     * This must be set before opening the writer.
     *
     * @api
     * @param bool $shouldCreateNewSheetsAutomatically Whether new sheets should be automatically created when the max rows limit per sheet is reached
     * @return AbstractMultiSheetsWriter
     * @throws \Box\Spout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     */
    public function setShouldCreateNewSheetsAutomatically($shouldCreateNewSheetsAutomatically)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->shouldCreateNewSheetsAutomatically = $shouldCreateNewSheetsAutomatically;
        return $this;
    }

    /**
     * Returns all the workbook's sheets
     *
     * @api
     * @return Common\Sheet[] All the workbook's sheets
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     */
    public function getSheets()
    {
        $this->throwIfBookIsNotAvailable();

        $externalSheets = [];
        $worksheets = $this->getWorkbook()->getWorksheets();

        /** @var Common\Internal\WorksheetInterface $worksheet */
        foreach ($worksheets as $worksheet) {
            $externalSheets[] = $worksheet->getExternalSheet();
        }

        return $externalSheets;
    }

    /**
     * Creates a new sheet and make it the current sheet. The data will now be written to this sheet.
     *
     * @api
     * @return Common\Sheet The created sheet
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     */
    public function addNewSheetAndMakeItCurrent()
    {
        $this->throwIfBookIsNotAvailable();
        $worksheet = $this->getWorkbook()->addNewSheetAndMakeItCurrent();

        return $worksheet->getExternalSheet();
    }

    /**
     * Returns the current sheet
     *
     * @api
     * @return Common\Sheet The current sheet
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     */
    public function getCurrentSheet()
    {
        $this->throwIfBookIsNotAvailable();
        return $this->getWorkbook()->getCurrentWorksheet()->getExternalSheet();
    }

    /**
     * Sets the given sheet as the current one. New data will be written to this sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @api
     * @param Common\Sheet $sheet The sheet to set as current
     * @return void
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Writer\Exception\SheetNotFoundException If the given sheet does not exist in the workbook
     */
    public function setCurrentSheet($sheet)
    {
        $this->throwIfBookIsNotAvailable();
        $this->getWorkbook()->setCurrentSheet($sheet);
    }

    /**
     * Checks if the book has been created. Throws an exception if not created yet.
     *
     * @return void
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the book is not created yet
     */
    protected function throwIfBookIsNotAvailable()
    {
        if (!$this->getWorkbook()) {
            throw new WriterNotOpenedException('The writer must be opened before performing this action.');
        }
    }
}

