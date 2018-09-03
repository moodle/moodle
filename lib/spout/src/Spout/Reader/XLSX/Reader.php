<?php

namespace Box\Spout\Reader\XLSX;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\AbstractReader;
use Box\Spout\Reader\XLSX\Helper\SharedStringsHelper;

/**
 * Class Reader
 * This class provides support to read data from a XLSX file
 *
 * @package Box\Spout\Reader\XLSX
 */
class Reader extends AbstractReader
{
    /** @var \ZipArchive */
    protected $zip;

    /** @var \Box\Spout\Reader\XLSX\Helper\SharedStringsHelper Helper to work with shared strings */
    protected $sharedStringsHelper;

    /** @var SheetIterator To iterator over the XLSX sheets */
    protected $sheetIterator;


    /**
     * Returns the reader's current options
     *
     * @return ReaderOptions
     */
    protected function getOptions()
    {
        if (!isset($this->options)) {
            $this->options = new ReaderOptions();
        }
        return $this->options;
    }

    /**
     * @param string $tempFolder Temporary folder where the temporary files will be created
     * @return Reader
     */
    public function setTempFolder($tempFolder)
    {
        $this->getOptions()->setTempFolder($tempFolder);
        return $this;
    }

    /**
     * Returns whether stream wrappers are supported
     *
     * @return bool
     */
    protected function doesSupportStreamWrapper()
    {
        return false;
    }

    /**
     * Opens the file at the given file path to make it ready to be read.
     * It also parses the sharedStrings.xml file to get all the shared strings available in memory
     * and fetches all the available sheets.
     *
     * @param  string $filePath Path of the file to be read
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the file at the given path or its content cannot be read
     * @throws \Box\Spout\Reader\Exception\NoSheetsFoundException If there are no sheets in the file
     */
    protected function openReader($filePath)
    {
        $this->zip = new \ZipArchive();

        if ($this->zip->open($filePath) === true) {
            $this->sharedStringsHelper = new SharedStringsHelper($filePath, $this->getOptions()->getTempFolder());

            if ($this->sharedStringsHelper->hasSharedStrings()) {
                // Extracts all the strings from the sheets for easy access in the future
                $this->sharedStringsHelper->extractSharedStrings();
            }

            $this->sheetIterator = new SheetIterator($filePath, $this->getOptions(), $this->sharedStringsHelper, $this->globalFunctionsHelper);
        } else {
            throw new IOException("Could not open $filePath for reading.");
        }
    }

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @return SheetIterator To iterate over sheets
     */
    protected function getConcreteSheetIterator()
    {
        return $this->sheetIterator;
    }

    /**
     * Closes the reader. To be used after reading the file.
     *
     * @return void
     */
    protected function closeReader()
    {
        if ($this->zip) {
            $this->zip->close();
        }

        if ($this->sharedStringsHelper) {
            $this->sharedStringsHelper->cleanup();
        }
    }
}
