<?php

namespace Box\Spout\Writer\XLSX;

use Box\Spout\Writer\AbstractMultiSheetsWriter;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\XLSX\Internal\Workbook;

/**
 * Class Writer
 * This class provides base support to write data to XLSX files
 *
 * @package Box\Spout\Writer\XLSX
 */
class Writer extends AbstractMultiSheetsWriter
{
    /** Default style font values */
    const DEFAULT_FONT_SIZE = 12;
    const DEFAULT_FONT_NAME = 'Calibri';

    /** @var string Content-Type value for the header */
    protected static $headerContentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /** @var string Temporary folder where the files to create the XLSX will be stored */
    protected $tempFolder;

    /** @var bool Whether inline or shared strings should be used - inline string is more memory efficient */
    protected $shouldUseInlineStrings = true;

    /** @var Internal\Workbook The workbook for the XLSX file */
    protected $book;

    /**
     * Sets a custom temporary folder for creating intermediate files/folders.
     * This must be set before opening the writer.
     *
     * @api
     * @param string $tempFolder Temporary folder where the files to create the XLSX will be stored
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
     * Use inline string to be more memory efficient. If set to false, it will use shared strings.
     * This must be set before opening the writer.
     *
     * @api
     * @param bool $shouldUseInlineStrings Whether inline or shared strings should be used
     * @return Writer
     * @throws \Box\Spout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     */
    public function setShouldUseInlineStrings($shouldUseInlineStrings)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->shouldUseInlineStrings = $shouldUseInlineStrings;
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
        if (!$this->book) {
            $tempFolder = ($this->tempFolder) ? : sys_get_temp_dir();
            $this->book = new Workbook($tempFolder, $this->shouldUseInlineStrings, $this->shouldCreateNewSheetsAutomatically, $this->defaultRowStyle);
            $this->book->addNewSheetAndMakeItCurrent();
        }
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
     * Returns the default style to be applied to rows.
     *
     * @return \Box\Spout\Writer\Style\Style
     */
    protected function getDefaultRowStyle()
    {
        return (new StyleBuilder())
            ->setFontSize(self::DEFAULT_FONT_SIZE)
            ->setFontName(self::DEFAULT_FONT_NAME)
            ->build();
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
