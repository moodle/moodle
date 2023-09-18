<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Helper\EncodingHelper;
use OpenSpout\Reader\AbstractReader;

/**
 * @extends AbstractReader<SheetIterator>
 */
final class Reader extends AbstractReader
{
    /** @var resource Pointer to the file to be written */
    private $filePointer;

    /** @var SheetIterator To iterator over the CSV unique "sheet" */
    private SheetIterator $sheetIterator;

    /** @var string Original value for the "auto_detect_line_endings" INI value */
    private string $originalAutoDetectLineEndings;

    /** @var bool Whether the code is running with PHP >= 8.1 */
    private bool $isRunningAtLeastPhp81;

    private Options $options;
    private EncodingHelper $encodingHelper;

    public function __construct(
        ?Options $options = null,
        ?EncodingHelper $encodingHelper = null
    ) {
        $this->options = $options ?? new Options();
        $this->encodingHelper = $encodingHelper ?? EncodingHelper::factory();
        $this->isRunningAtLeastPhp81 = \PHP_VERSION_ID >= 80100;
    }

    public function getSheetIterator(): SheetIterator
    {
        $this->ensureStreamOpened();

        return $this->sheetIterator;
    }

    /**
     * Returns whether stream wrappers are supported.
     */
    protected function doesSupportStreamWrapper(): bool
    {
        return true;
    }

    /**
     * Opens the file at the given path to make it ready to be read.
     * If setEncoding() was not called, it assumes that the file is encoded in UTF-8.
     *
     * @param string $filePath Path of the CSV file to be read
     *
     * @throws \OpenSpout\Common\Exception\IOException
     */
    protected function openReader(string $filePath): void
    {
        // "auto_detect_line_endings" is deprecated in PHP 8.1
        if (!$this->isRunningAtLeastPhp81) {
            // @codeCoverageIgnoreStart
            $originalAutoDetectLineEndings = \ini_get('auto_detect_line_endings');
            \assert(false !== $originalAutoDetectLineEndings);
            $this->originalAutoDetectLineEndings = $originalAutoDetectLineEndings;
            ini_set('auto_detect_line_endings', '1');
            // @codeCoverageIgnoreEnd
        }

        $resource = fopen($filePath, 'r');
        \assert(false !== $resource);
        $this->filePointer = $resource;

        $this->sheetIterator = new SheetIterator(
            new Sheet(
                new RowIterator(
                    $this->filePointer,
                    $this->options,
                    $this->encodingHelper
                )
            )
        );
    }

    /**
     * Closes the reader. To be used after reading the file.
     */
    protected function closeReader(): void
    {
        fclose($this->filePointer);

        // "auto_detect_line_endings" is deprecated in PHP 8.1
        if (!$this->isRunningAtLeastPhp81) {
            // @codeCoverageIgnoreStart
            ini_set('auto_detect_line_endings', $this->originalAutoDetectLineEndings);
            // @codeCoverageIgnoreEnd
        }
    }
}
