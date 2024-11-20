<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Exception\IOException;
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

    private readonly Options $options;
    private readonly EncodingHelper $encodingHelper;

    public function __construct(
        ?Options $options = null,
        ?EncodingHelper $encodingHelper = null
    ) {
        $this->options = $options ?? new Options();
        $this->encodingHelper = $encodingHelper ?? EncodingHelper::factory();
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
     * @throws IOException
     */
    protected function openReader(string $filePath): void
    {
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
    }
}
