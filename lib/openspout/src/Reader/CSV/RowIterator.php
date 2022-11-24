<?php

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Helper\EncodingHelper;
use OpenSpout\Common\Helper\GlobalFunctionsHelper;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Reader\Common\Entity\Options;
use OpenSpout\Reader\CSV\Creator\InternalEntityFactory;
use OpenSpout\Reader\RowIteratorInterface;

/**
 * Iterate over CSV rows.
 */
class RowIterator implements RowIteratorInterface
{
    /**
     * Value passed to fgetcsv. 0 means "unlimited" (slightly slower but accomodates for very long lines).
     */
    public const MAX_READ_BYTES_PER_LINE = 0;

    /** @var null|resource Pointer to the CSV file to read */
    protected $filePointer;

    /** @var int Number of read rows */
    protected $numReadRows = 0;

    /** @var null|Row Buffer used to store the current row, while checking if there are more rows to read */
    protected $rowBuffer;

    /** @var bool Indicates whether all rows have been read */
    protected $hasReachedEndOfFile = false;

    /** @var string Defines the character used to delimit fields (one character only) */
    protected $fieldDelimiter;

    /** @var string Defines the character used to enclose fields (one character only) */
    protected $fieldEnclosure;

    /** @var string Encoding of the CSV file to be read */
    protected $encoding;

    /** @var bool Whether empty rows should be returned or skipped */
    protected $shouldPreserveEmptyRows;

    /** @var \OpenSpout\Common\Helper\EncodingHelper Helper to work with different encodings */
    protected $encodingHelper;

    /** @var \OpenSpout\Reader\CSV\Creator\InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var \OpenSpout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /**
     * @param resource $filePointer Pointer to the CSV file to read
     */
    public function __construct(
        $filePointer,
        OptionsManagerInterface $optionsManager,
        EncodingHelper $encodingHelper,
        InternalEntityFactory $entityFactory,
        GlobalFunctionsHelper $globalFunctionsHelper
    ) {
        $this->filePointer = $filePointer;
        $this->fieldDelimiter = $optionsManager->getOption(Options::FIELD_DELIMITER);
        $this->fieldEnclosure = $optionsManager->getOption(Options::FIELD_ENCLOSURE);
        $this->encoding = $optionsManager->getOption(Options::ENCODING);
        $this->shouldPreserveEmptyRows = $optionsManager->getOption(Options::SHOULD_PRESERVE_EMPTY_ROWS);
        $this->encodingHelper = $encodingHelper;
        $this->entityFactory = $entityFactory;
        $this->globalFunctionsHelper = $globalFunctionsHelper;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     */
    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->rewindAndSkipBom();

        $this->numReadRows = 0;
        $this->rowBuffer = null;

        $this->next();
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        return $this->filePointer && !$this->hasReachedEndOfFile;
    }

    /**
     * Move forward to next element. Reads data for the next unprocessed row.
     *
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @throws \OpenSpout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     */
    #[\ReturnTypeWillChange]
    public function next(): void
    {
        $this->hasReachedEndOfFile = $this->globalFunctionsHelper->feof($this->filePointer);

        if (!$this->hasReachedEndOfFile) {
            $this->readDataForNextRow();
        }
    }

    /**
     * Return the current element from the buffer.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    #[\ReturnTypeWillChange]
    public function current(): ?Row
    {
        return $this->rowBuffer;
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    #[\ReturnTypeWillChange]
    public function key(): int
    {
        return $this->numReadRows;
    }

    /**
     * Cleans up what was created to iterate over the object.
     */
    #[\ReturnTypeWillChange]
    public function end(): void
    {
        // do nothing
    }

    /**
     * This rewinds and skips the BOM if inserted at the beginning of the file
     * by moving the file pointer after it, so that it is not read.
     */
    protected function rewindAndSkipBom()
    {
        $byteOffsetToSkipBom = $this->encodingHelper->getBytesOffsetToSkipBOM($this->filePointer, $this->encoding);

        // sets the cursor after the BOM (0 means no BOM, so rewind it)
        $this->globalFunctionsHelper->fseek($this->filePointer, $byteOffsetToSkipBom);
    }

    /**
     * @throws \OpenSpout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     */
    protected function readDataForNextRow()
    {
        do {
            $rowData = $this->getNextUTF8EncodedRow();
        } while ($this->shouldReadNextRow($rowData));

        if (false !== $rowData) {
            // array_map will replace NULL values by empty strings
            $rowDataBufferAsArray = array_map(function ($value) { return (string) $value; }, $rowData);
            $this->rowBuffer = $this->entityFactory->createRowFromArray($rowDataBufferAsArray);
            ++$this->numReadRows;
        } else {
            // If we reach this point, it means end of file was reached.
            // This happens when the last lines are empty lines.
            $this->hasReachedEndOfFile = true;
        }
    }

    /**
     * @param array|bool $currentRowData
     *
     * @return bool Whether the data for the current row can be returned or if we need to keep reading
     */
    protected function shouldReadNextRow($currentRowData)
    {
        $hasSuccessfullyFetchedRowData = (false !== $currentRowData);
        $hasNowReachedEndOfFile = $this->globalFunctionsHelper->feof($this->filePointer);
        $isEmptyLine = $this->isEmptyLine($currentRowData);

        return
            (!$hasSuccessfullyFetchedRowData && !$hasNowReachedEndOfFile)
            || (!$this->shouldPreserveEmptyRows && $isEmptyLine)
        ;
    }

    /**
     * Returns the next row, converted if necessary to UTF-8.
     * As fgetcsv() does not manage correctly encoding for non UTF-8 data,
     * we remove manually whitespace with ltrim or rtrim (depending on the order of the bytes).
     *
     * @throws \OpenSpout\Common\Exception\EncodingConversionException If unable to convert data to UTF-8
     *
     * @return array|false The row for the current file pointer, encoded in UTF-8 or FALSE if nothing to read
     */
    protected function getNextUTF8EncodedRow()
    {
        $encodedRowData = $this->globalFunctionsHelper->fgetcsv($this->filePointer, self::MAX_READ_BYTES_PER_LINE, $this->fieldDelimiter, $this->fieldEnclosure);
        if (false === $encodedRowData) {
            return false;
        }

        foreach ($encodedRowData as $cellIndex => $cellValue) {
            switch ($this->encoding) {
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
     * @param array|bool $lineData Array containing the cells value for the line
     *
     * @return bool Whether the given line is empty
     */
    protected function isEmptyLine($lineData)
    {
        return \is_array($lineData) && 1 === \count($lineData) && null === $lineData[0];
    }
}
