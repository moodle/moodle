<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

/**
 * @template T of SheetIteratorInterface
 */
interface ReaderInterface
{
    /**
     * Prepares the reader to read the given file. It also makes sure
     * that the file exists and is readable.
     *
     * @param string $filePath Path of the file to be read
     *
     * @throws \OpenSpout\Common\Exception\IOException
     */
    public function open(string $filePath): void;

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @return T
     *
     * @throws \OpenSpout\Reader\Exception\ReaderNotOpenedException If called before opening the reader
     */
    public function getSheetIterator(): SheetIteratorInterface;

    /**
     * Closes the reader, preventing any additional reading.
     */
    public function close(): void;
}
