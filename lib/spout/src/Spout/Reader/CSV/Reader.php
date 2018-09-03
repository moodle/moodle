<?php

namespace Box\Spout\Reader\CSV;

use Box\Spout\Reader\AbstractReader;
use Box\Spout\Common\Exception\IOException;

/**
 * Class Reader
 * This class provides support to read data from a CSV file.
 *
 * @package Box\Spout\Reader\CSV
 */
class Reader extends AbstractReader
{
    /** @var resource Pointer to the file to be written */
    protected $filePointer;

    /** @var SheetIterator To iterator over the CSV unique "sheet" */
    protected $sheetIterator;

    /** @var string Original value for the "auto_detect_line_endings" INI value */
    protected $originalAutoDetectLineEndings;

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
     * Sets the field delimiter for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $fieldDelimiter Character that delimits fields
     * @return Reader
     */
    public function setFieldDelimiter($fieldDelimiter)
    {
        $this->getOptions()->setFieldDelimiter($fieldDelimiter);
        return $this;
    }

    /**
     * Sets the field enclosure for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $fieldEnclosure Character that enclose fields
     * @return Reader
     */
    public function setFieldEnclosure($fieldEnclosure)
    {
        $this->getOptions()->setFieldEnclosure($fieldEnclosure);
        return $this;
    }

    /**
     * Sets the encoding of the CSV file to be read.
     * Needs to be called before opening the reader.
     *
     * @param string $encoding Encoding of the CSV file to be read
     * @return Reader
     */
    public function setEncoding($encoding)
    {
        $this->getOptions()->setEncoding($encoding);
        return $this;
    }

    /**
     * Sets the EOL for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $endOfLineCharacter used to properly get lines from the CSV file.
     * @return Reader
     */
    public function setEndOfLineCharacter($endOfLineCharacter)
    {
        $this->getOptions()->setEndOfLineCharacter($endOfLineCharacter);
        return $this;
    }

    /**
     * Returns whether stream wrappers are supported
     *
     * @return bool
     */
    protected function doesSupportStreamWrapper()
    {
        return true;
    }

    /**
     * Opens the file at the given path to make it ready to be read.
     * If setEncoding() was not called, it assumes that the file is encoded in UTF-8.
     *
     * @param  string $filePath Path of the CSV file to be read
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException
     */
    protected function openReader($filePath)
    {
        $this->originalAutoDetectLineEndings = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');

        $this->filePointer = $this->globalFunctionsHelper->fopen($filePath, 'r');
        if (!$this->filePointer) {
            throw new IOException("Could not open file $filePath for reading.");
        }

        $this->sheetIterator = new SheetIterator(
            $this->filePointer,
            $this->getOptions(),
            $this->globalFunctionsHelper
        );
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
        if ($this->filePointer) {
            $this->globalFunctionsHelper->fclose($this->filePointer);
        }

        ini_set('auto_detect_line_endings', $this->originalAutoDetectLineEndings);
    }
}
