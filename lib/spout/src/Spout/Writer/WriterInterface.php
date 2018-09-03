<?php

namespace Box\Spout\Writer;

/**
 * Interface WriterInterface
 *
 * @package Box\Spout\Writer
 */
interface WriterInterface
{
    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be written to a file.
     *
     * @param  string $outputFilePath Path of the output file that will contain the data
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened or if the given path is not writable
     */
    public function openToFile($outputFilePath);

    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be outputted directly to the browser.
     *
     * @param  string $outputFileName Name of the output file that will contain the data. If a path is passed in, only the file name will be kept
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened
     */
    public function openToBrowser($outputFileName);

    /**
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @param  array $dataRow Array containing data to be streamed.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @return WriterInterface
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRow(array $dataRow);

    /**
     * Write given data to the output and apply the given style.
     * @see addRow
     *
     * @param array $dataRow Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the row.
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowWithStyle(array $dataRow, $style);

    /**
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @param  array $dataRows Array of array containing data to be streamed.
     *          Example $dataRow = [
     *              ['data11', 12, , '', 'data13'],
     *              ['data21', 'data22', null],
     *          ];
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If the writer has not been opened yet
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRows(array $dataRows);

    /**
     * Write given data to the output and apply the given style.
     * @see addRows
     *
     * @param array $dataRows Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the rows.
     * @return WriterInterface
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowsWithStyle(array $dataRows, $style);

    /**
     * Closes the writer. This will close the streamer as well, preventing new data
     * to be written to the file.
     *
     * @return void
     */
    public function close();
}
