<?php

namespace Box\Spout\Reader;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;

/**
 * Class AbstractReader
 *
 * @package Box\Spout\Reader
 * @abstract
 */
abstract class AbstractReader implements ReaderInterface
{
    /** @var bool Indicates whether the stream is currently open */
    protected $isStreamOpened = false;

    /** @var \Box\Spout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var bool Whether date/time values should be returned as PHP objects or be formatted as strings */
    protected $shouldFormatDates = false;

    /**
     * Returns whether stream wrappers are supported
     *
     * @return bool
     */
    abstract protected function doesSupportStreamWrapper();

    /**
     * Opens the file at the given file path to make it ready to be read
     *
     * @param  string $filePath Path of the file to be read
     * @return void
     */
    abstract protected function openReader($filePath);

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @return \Iterator To iterate over sheets
     */
    abstract public function getConcreteSheetIterator();

    /**
     * Closes the reader. To be used after reading the file.
     *
     * @return AbstractReader
     */
    abstract protected function closeReader();

    /**
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     * @return AbstractReader
     */
    public function setGlobalFunctionsHelper($globalFunctionsHelper)
    {
        $this->globalFunctionsHelper = $globalFunctionsHelper;
        return $this;
    }

    /**
     * Sets whether date/time values should be returned as PHP objects or be formatted as strings.
     *
     * @param bool $shouldFormatDates
     * @return AbstractReader
     */
    public function setShouldFormatDates($shouldFormatDates)
    {
        $this->shouldFormatDates = $shouldFormatDates;
        return $this;
    }

    /**
     * Prepares the reader to read the given file. It also makes sure
     * that the file exists and is readable.
     *
     * @api
     * @param  string $filePath Path of the file to be read
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the file at the given path does not exist, is not readable or is corrupted
     */
    public function open($filePath)
    {
        if ($this->isStreamWrapper($filePath) && (!$this->doesSupportStreamWrapper() || !$this->isSupportedStreamWrapper($filePath))) {
            throw new IOException("Could not open $filePath for reading! Stream wrapper used is not supported for this type of file.");
        }

        if (!$this->isPhpStream($filePath)) {
            // we skip the checks if the provided file path points to a PHP stream
            if (!$this->globalFunctionsHelper->file_exists($filePath)) {
                throw new IOException("Could not open $filePath for reading! File does not exist.");
            } else if (!$this->globalFunctionsHelper->is_readable($filePath)) {
                throw new IOException("Could not open $filePath for reading! File is not readable.");
            }
        }

        try {
            $fileRealPath = $this->getFileRealPath($filePath);
            $this->openReader($fileRealPath);
            $this->isStreamOpened = true;
        } catch (\Exception $exception) {
            throw new IOException("Could not open $filePath for reading! ({$exception->getMessage()})");
        }
    }

    /**
     * Returns the real path of the given path.
     * If the given path is a valid stream wrapper, returns the path unchanged.
     *
     * @param string $filePath
     * @return string
     */
    protected function getFileRealPath($filePath)
    {
        if ($this->isSupportedStreamWrapper($filePath)) {
            return $filePath;
        }

        // Need to use realpath to fix "Can't open file" on some Windows setup
        return realpath($filePath);
    }

    /**
     * Returns the scheme of the custom stream wrapper, if the path indicates a stream wrapper is used.
     * For example, php://temp => php, s3://path/to/file => s3...
     *
     * @param string $filePath Path of the file to be read
     * @return string|null The stream wrapper scheme or NULL if not a stream wrapper
     */
    protected function getStreamWrapperScheme($filePath)
    {
        $streamScheme = null;
        if (preg_match('/^(\w+):\/\//', $filePath, $matches)) {
            $streamScheme = $matches[1];
        }
        return $streamScheme;
    }

    /**
     * Checks if the given path is an unsupported stream wrapper
     * (like local path, php://temp, mystream://foo/bar...).
     *
     * @param string $filePath Path of the file to be read
     * @return bool Whether the given path is an unsupported stream wrapper
     */
    protected function isStreamWrapper($filePath)
    {
        return ($this->getStreamWrapperScheme($filePath) !== null);
    }

    /**
     * Checks if the given path is an supported stream wrapper
     * (like php://temp, mystream://foo/bar...).
     * If the given path is a local path, returns true.
     *
     * @param string $filePath Path of the file to be read
     * @return bool Whether the given path is an supported stream wrapper
     */
    protected function isSupportedStreamWrapper($filePath)
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);
        return ($streamScheme !== null) ?
            in_array($streamScheme, $this->globalFunctionsHelper->stream_get_wrappers()) :
            true;
    }

    /**
     * Checks if a path is a PHP stream (like php://output, php://memory, ...)
     *
     * @param string $filePath Path of the file to be read
     * @return bool Whether the given path maps to a PHP stream
     */
    protected function isPhpStream($filePath)
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);
        return ($streamScheme === 'php');
    }

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @api
     * @return \Iterator To iterate over sheets
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException If called before opening the reader
     */
    public function getSheetIterator()
    {
        if (!$this->isStreamOpened) {
            throw new ReaderNotOpenedException('Reader should be opened first.');
        }

        return $this->getConcreteSheetIterator();
    }

    /**
     * Closes the reader, preventing any additional reading
     *
     * @api
     * @return void
     */
    public function close()
    {
        if ($this->isStreamOpened) {
            $this->closeReader();

            $sheetIterator = $this->getConcreteSheetIterator();
            if ($sheetIterator) {
                $sheetIterator->end();
            }

            $this->isStreamOpened = false;
        }
    }
}
