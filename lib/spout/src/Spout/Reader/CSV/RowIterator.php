<?php

namespace Box\Spout\Reader\CSV;

use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Common\Helper\EncodingHelper;

/**
 * Class RowIterator
 * Iterate over CSV rows.
 *
 * @package Box\Spout\Reader\CSV
 */
class RowIterator implements IteratorInterface
{
    /**
     * Value passed to fgetcsv. 0 means "unlimited" (slightly slower but accomodates for very long lines).
     */
    const MAX_READ_BYTES_PER_LINE = 0;

    /** @var resource Pointer to the CSV file to read */
    protected $filePointer;

    /** @var int Number of read rows */
    protected $numReadRows = 0;

    /** @var array|null Buffer used to store the row data, while checking if there are more rows to read */
    protected $rowDataBuffer = null;

    /** @var bool Indicates whether all rows have been read */
    protected $hasReachedEndOfFile = false;

    /** @var string Defines the character used to delimit fields (one character only) */
    protected $fieldDelimiter;

    /** @var string Defines the character used to enclose fields (one character only) */
    protected $fieldEnclosure;

    /** @var string Encoding of the CSV file to be read */
    protected $encoding;

    /** @var string End of line delimiter, given by the user as input. */
    protected $inputEOLDelimiter;

    /** @var bool Whether empty rows should be returned or skipped */
    protected $shouldPreserveEmptyRows;

    /** @var \Box\Spout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var \Box\Spout\Common\Helper\EncodingHelper Helper to work with different encodings */
    protected $encodingHelper;

    /** @var string End of line delimiter, encoded using the same encoding as the CSV */
    protected $encodedEOLDelimiter;

    /**
     * @param resource $filePointer Pointer to the CSV file to read
     * @param \Box\Spout\Reader\CSV\ReaderOptions $options
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     */
    public function __construct($filePointer, $options, $globalFunctionsHelper)
    {
        $this->filePointer = $filePointer;
        $this->fieldDelimiter = $options->getFieldDelimiter();
        $this->fieldEnclosure = $options->getFieldEnclosure();
        $this->encoding = $options->getEncoding();
        $this->inputEOLDelimiter = $options->getEndOfLineCharacter();
        $this->shouldPreserveEmptyRows = $options->shouldPreserveEmptyRows();
        $this->globalFunctionsHelper = $globalFunctionsHelper;

        $this->encodingHelper = new EncodingHelper($globalFunctionsHelper);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    public function rewind()
    {
        $this->rewindAndSkipBom();

        $this->numReadRows = 0;
        $this->rowDataBuffer = null;

        $this->next();
    }

    /**
     * This rewinds and skips the BOM if inserted at the beginning of the file
     * by moving the file pointer after it, so that it is not read.
     *
     * @return void
     */
    protected function rewindAndSkipBom()
    {
        $byteOffsetToSkipBom = $this->encodingHelper->getBytesOffsetToSkipBOM($this->filePointer, $this->encoding);

        // sets the cursor after the BOM (0 means no BOM, so rewind it)
        $this->globalFunctionsHelper->fseek($this->filePointer, $byteOffsetToSkipBom);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    public function valid()
    {
        return ($this->filePointer && !$this->hasReachedEndOfFile);
    }

    /**
     * Move forward to next element. Reads data for the next unprocessed row.
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     */
    public function next()
    {
        $this->hasReachedEndOfFile = $this->globalFunctionsHelper->feof($this->filePointer);

        if (!$this->hasReachedEndOfFile) {
            $this->readDataForNextRow();
        }
    }

    /**
     * @return void
     * @throws \Box\Spout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     */
    protected function readDataForNextRow()
    {
        do {
            $rowData = $this->getNextUTF8EncodedRow();
        } while ($this->shouldReadNextRow($rowData));

        if ($rowData !== false) {
            // str_replace will replace NULL values by empty strings
            $this->rowDataBuffer = str_replace(null, null, $rowData);
            $this->numReadRows++;
        } else {
            // If we reach this point, it means end of file was reached.
            // This happens when the last lines are empty lines.
            $this->hasReachedEndOfFile = true;
        }
    }

    /**
     * @param array|bool $currentRowData
     * @return bool Whether the data for the current row can be returned or if we need to keep reading
     */
    protected function shouldReadNextRow($currentRowData)
    {
        $hasSuccessfullyFetchedRowData = ($currentRowData !== false);
        $hasNowReachedEndOfFile = $this->globalFunctionsHelper->feof($this->filePointer);
        $isEmptyLine = $this->isEmptyLine($currentRowData);

        return (
            (!$hasSuccessfullyFetchedRowData && !$hasNowReachedEndOfFile) ||
            (!$this->shouldPreserveEmptyRows && $isEmptyLine)
        );
    }

    /**
     * Returns the next row, converted if necessary to UTF-8.
     * As fgetcsv() does not manage correctly encoding for non UTF-8 data,
     * we remove manually whitespace with ltrim or rtrim (depending on the order of the bytes)
     *
     * @return array|false The row for the current file pointer, encoded in UTF-8 or FALSE if nothing to read
     * @throws \Box\Spout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     */
    protected function getNextUTF8EncodedRow()
    {
        $encodedRowData = $this->globalFunctionsHelper->fgetcsv($this->filePointer, self::MAX_READ_BYTES_PER_LINE, $this->fieldDelimiter, $this->fieldEnclosure);
        if ($encodedRowData === false) {
            return false;
        }

        foreach ($encodedRowData as $cellIndex => $cellValue) {
            switch($this->encoding) {
                case EncodingHelper::ENCODING_UTF16_LE:
                case EncodingHelper::ENCODING_UTF32_LE:
                    // remove whitespace from the beginning of a string as fgetcsv() add extra whitespace when it try to explode non UTF-8 data
                    $cellValue = ltrim($cellValue);
                    break;

                case EncodingHelper::ENCODING_UTF16_BE:
                case EncodingHelper::ENCODING_UTF32_BE:
                    // remove whitespace from the end of a string as fgetcsv() add extra whitespace when it try to explode non UTF-8 data
                    $cellValue = rtrim($cellValue);
                    break;
            }

            $encodedRowData[$cellIndex] = $this->encodingHelper->attemptConversionToUTF8($cellValue, $this->encoding);
        }

        return $encodedRowData;
    }

    /**
     * Returns the end of line delimiter, encoded using the same encoding as the CSV.
     * The return value is cached.
     *
     * @return string
     */
    protected function getEncodedEOLDelimiter()
    {
        if (!isset($this->encodedEOLDelimiter)) {
            $this->encodedEOLDelimiter = $this->encodingHelper->attemptConversionFromUTF8($this->inputEOLDelimiter, $this->encoding);
        }

        return $this->encodedEOLDelimiter;
    }

    /**
     * @param array|bool $lineData Array containing the cells value for the line
     * @return bool Whether the given line is empty
     */
    protected function isEmptyLine($lineData)
    {
        return (is_array($lineData) && count($lineData) === 1 && $lineData[0] === null);
    }

    /**
     * Return the current element from the buffer
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return array|null
     */
    public function current()
    {
        return $this->rowDataBuffer;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return $this->numReadRows;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end()
    {
        // do nothing
    }
}
