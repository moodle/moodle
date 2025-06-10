<?php

namespace Box\Spout\Reader;

/**
 * Interface ReaderInterface
 */
interface ReaderInterface
{
    /**
     * Prepares the reader to read the given file. It also makes sure
     * that the file exists and is readable.
     *
     * @param  string $filePath Path of the file to be read
     * @throws \Box\Spout\Common\Exception\IOException
     * @return void
     */
    public function open($filePath);

    /**
     * Returns an iterator to iterate over sheets.
     *
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException If called before opening the reader
     * @return \Iterator To iterate over sheets
     */
    public function getSheetIterator();

    /**
     * Closes the reader, preventing any additional reading
     *
     * @return void
     */
    public function close();
}
