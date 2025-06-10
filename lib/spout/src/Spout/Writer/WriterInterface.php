<?php

namespace Box\Spout\Writer;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Style;

/**
 * Interface WriterInterface
 */
interface WriterInterface
{
    /**
     * Initializes the writer and opens it to accept data.
     * By using this method, the data will be written to a file.
     *
     * @param  string $outputFilePath Path of the output file that will contain the data
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened or if the given path is not writable
     * @return WriterInterface
     */
    public function openToFile($outputFilePath);

    /**
     * Initializes the writer and opens it to accept data.
     * By using this method, the data will be outputted directly to the browser.
     *
     * @param  string $outputFileName Name of the output file that will contain the data. If a path is passed in, only the file name will be kept
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened
     * @return WriterInterface
     */
    public function openToBrowser($outputFileName);

    /**
     * Sets the default styles for all rows added with "addRow".
     * Overriding the default style instead of using "addRowWithStyle" improves performance by 20%.
     * @see https://github.com/box/spout/issues/272
     *
     * @param Style $defaultStyle
     * @return WriterInterface
     */
    public function setDefaultRowStyle(Style $defaultStyle);

    /**
     * Appends a row to the end of the stream.
     *
     * @param Row $row The row to be appended to the stream
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     * @return WriterInterface
     */
    public function addRow(Row $row);

    /**
     * Appends the rows to the end of the stream.
     *
     * @param Row[] $rows The rows to be appended to the stream
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     * @return WriterInterface
     */
    public function addRows(array $rows);

    /**
     * Closes the writer. This will close the streamer as well, preventing new data
     * to be written to the file.
     *
     * @return void
     */
    public function close();
}
