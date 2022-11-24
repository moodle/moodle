<?php

namespace OpenSpout\Reader;

/**
 * Interface ReaderInterface.
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
    public function open($filePath);

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @throws \OpenSpout\Reader\Exception\ReaderNotOpenedException If called before opening the reader
     *
     * @return SheetIteratorInterface To iterate over sheets
     */
    public function getSheetIterator();

    /**
     * Closes the reader, preventing any additional reading.
     */
    public function close();
}
