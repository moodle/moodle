<?php

namespace Box\Spout\Writer\ODS;

use Box\Spout\Writer\AbstractMultiSheetsWriter;
use Box\Spout\Writer\Common;
use Box\Spout\Writer\ODS\Internal\Workbook;

/**
 * Class Writer
 * This class provides base support to write data to ODS files
 *
 * @package Box\Spout\Writer\ODS
 */
class Writer extends AbstractMultiSheetsWriter
{
    /** @var string Content-Type value for the header */
    protected static $headerContentType = 'application/vnd.oasis.opendocument.spreadsheet';

    /** @var string Temporary folder where the files to create the ODS will be stored */
    protected $tempFolder;

    /** @var Internal\Workbook The workbook for the ODS file */
    protected $book;

    /**
     * Sets a custom temporary folder for creating intermediate files/folders.
     * This must be set before opening the writer.
     *
     * @api
     * @param string $tempFolder Temporary folder where the files to create the ODS will be stored
     * @return Writer
     * @throws \Box\Spout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     */
    public function setTempFolder($tempFolder)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->tempFolder = $tempFolder;
        return $this;
    }

    /**
     * Configures the write and sets the current sheet pointer to a new sheet.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If unable to open the file for writing
     */
    protected function openWriter()
    {
        $tempFolder = ($this->tempFolder) ? : sys_get_temp_dir();
        $this->book = new Workbook($tempFolder, $this->shouldCreateNewSheetsAutomatically, $this->defaultRowStyle);
        $this->book->addNewSheetAndMakeItCurrent();
    }

    /**
     * @return Internal\Workbook The workbook representing the file to be written
     */
    protected function getWorkbook()
    {
        return $this->book;
    }

    /**
     * Adds data to the currently opened writer.
     * If shouldCreateNewSheetsAutomatically option is set to true, it will handle pagination
     * with the creation of new worksheets if one worksheet has reached its maximum capicity.
     *
     * @param array $dataRow Array containing data to be written.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row.
     * @return void
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the book is not created yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    protected function addRowToWriter(array $dataRow, $style)
    {
        $this->throwIfBookIsNotAvailable();
        $this->book->addRowToCurrentWorksheet($dataRow, $style);
    }

    /**
     * Closes the writer, preventing any additional writing.
     *
     * @return void
     */
    protected function closeWriter()
    {
        if ($this->book) {
            $this->book->close($this->filePointer);
        }
    }
}
