<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;

interface WriterInterface
{
    /**
     * Initializes the writer and opens it to accept data.
     * By using this method, the data will be written to a file.
     *
     * @param string $outputFilePath Path of the output file that will contain the data
     *
     * @throws IOException If the writer cannot be opened or if the given path is not writable
     */
    public function openToFile(string $outputFilePath): void;

    /**
     * Initializes the writer and opens it to accept data.
     * By using this method, the data will be outputted directly to the browser.
     *
     * @param string $outputFileName Name of the output file that will contain the data. If a path is passed in, only the file name will be kept
     *
     * @throws IOException If the writer cannot be opened
     */
    public function openToBrowser(string $outputFileName): void;

    /**
     * Appends a row to the end of the stream.
     *
     * @param Row $row The row to be appended to the stream
     *
     * @throws Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws IOException                        If unable to write data
     */
    public function addRow(Row $row): void;

    /**
     * Set document creator.
     *
     * @param string $creator document creator
     */
    public function setCreator(string $creator): void;

    /**
     * Appends the rows to the end of the stream.
     *
     * @param Row[] $rows The rows to be appended to the stream
     *
     * @throws InvalidArgumentException           If the input param is not valid
     * @throws Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws IOException                        If unable to write data
     */
    public function addRows(array $rows): void;

    /**
     * @return 0|positive-int
     */
    public function getWrittenRowCount(): int;

    /**
     * Closes the writer. This will close the streamer as well, preventing new data
     * to be written to the file.
     */
    public function close(): void;
}
