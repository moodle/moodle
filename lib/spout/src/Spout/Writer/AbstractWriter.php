<?php

namespace Box\Spout\Writer;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Writer\Exception\WriterAlreadyOpenedException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Box\Spout\Writer\Style\StyleBuilder;

/**
 * Class AbstractWriter
 *
 * @package Box\Spout\Writer
 * @abstract
 */
abstract class AbstractWriter implements WriterInterface
{
    /** @var string Path to the output file */
    protected $outputFilePath;

    /** @var resource Pointer to the file/stream we will write to */
    protected $filePointer;

    /** @var bool Indicates whether the writer has been opened or not */
    protected $isWriterOpened = false;

    /** @var \Box\Spout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var Style\Style Style to be applied to the next written row(s) */
    protected $rowStyle;

    /** @var Style\Style Default row style. Each writer can have its own default style */
    protected $defaultRowStyle;

    /** @var string Content-Type value for the header - to be defined by child class */
    protected static $headerContentType;

    /**
     * Opens the streamer and makes it ready to accept data.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened
     */
    abstract protected function openWriter();

    /**
     * Adds data to the currently openned writer.
     *
     * @param  array $dataRow Array containing data to be streamed.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param Style\Style $style Style to be applied to the written row
     * @return void
     */
    abstract protected function addRowToWriter(array $dataRow, $style);

    /**
     * Closes the streamer, preventing any additional writing.
     *
     * @return void
     */
    abstract protected function closeWriter();

    /**
     *
     */
    public function __construct()
    {
        $this->defaultRowStyle = $this->getDefaultRowStyle();
        $this->resetRowStyleToDefault();
    }

    /**
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     * @return AbstractWriter
     */
    public function setGlobalFunctionsHelper($globalFunctionsHelper)
    {
        $this->globalFunctionsHelper = $globalFunctionsHelper;
        return $this;
    }

    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be written to a file.
     *
     * @api
     * @param  string $outputFilePath Path of the output file that will contain the data
     * @return AbstractWriter
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened or if the given path is not writable
     */
    public function openToFile($outputFilePath)
    {
        $this->outputFilePath = $outputFilePath;

        $this->filePointer = $this->globalFunctionsHelper->fopen($this->outputFilePath, 'wb+');
        $this->throwIfFilePointerIsNotAvailable();

        $this->openWriter();
        $this->isWriterOpened = true;

        return $this;
    }

    /**
     * Inits the writer and opens it to accept data.
     * By using this method, the data will be outputted directly to the browser.
     *
     * @codeCoverageIgnore
     *
     * @api
     * @param  string $outputFileName Name of the output file that will contain the data. If a path is passed in, only the file name will be kept
     * @return AbstractWriter
     * @throws \Box\Spout\Common\Exception\IOException If the writer cannot be opened
     */
    public function openToBrowser($outputFileName)
    {
        $this->outputFilePath = $this->globalFunctionsHelper->basename($outputFileName);

        $this->filePointer = $this->globalFunctionsHelper->fopen('php://output', 'w');
        $this->throwIfFilePointerIsNotAvailable();

        // Set headers
        $this->globalFunctionsHelper->header('Content-Type: ' . static::$headerContentType);
        $this->globalFunctionsHelper->header('Content-Disposition: attachment; filename="' . $this->outputFilePath . '"');

        /*
         * When forcing the download of a file over SSL,IE8 and lower browsers fail
         * if the Cache-Control and Pragma headers are not set.
         *
         * @see http://support.microsoft.com/KB/323308
         * @see https://github.com/liuggio/ExcelBundle/issues/45
         */
        $this->globalFunctionsHelper->header('Cache-Control: max-age=0');
        $this->globalFunctionsHelper->header('Pragma: public');

        $this->openWriter();
        $this->isWriterOpened = true;

        return $this;
    }

    /**
     * Checks if the pointer to the file/stream to write to is available.
     * Will throw an exception if not available.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the pointer is not available
     */
    protected function throwIfFilePointerIsNotAvailable()
    {
        if (!$this->filePointer) {
            throw new IOException('File pointer has not be opened');
        }
    }

    /**
     * Checks if the writer has already been opened, since some actions must be done before it gets opened.
     * Throws an exception if already opened.
     *
     * @param string $message Error message
     * @return void
     * @throws \Box\Spout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened and must not be.
     */
    protected function throwIfWriterAlreadyOpened($message)
    {
        if ($this->isWriterOpened) {
            throw new WriterAlreadyOpenedException($message);
        }
    }

    /**
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @param  array $dataRow Array containing data to be streamed.
     *                        If empty, no data is added (i.e. not even as a blank row)
     *                        Example: $dataRow = ['data1', 1234, null, '', 'data5', false];
     * @api
     * @return AbstractWriter
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRow(array $dataRow)
    {
        if ($this->isWriterOpened) {
            // empty $dataRow should not add an empty line
            if (!empty($dataRow)) {
                $this->addRowToWriter($dataRow, $this->rowStyle);
            }
        } else {
            throw new WriterNotOpenedException('The writer needs to be opened before adding row.');
        }

        return $this;
    }

    /**
     * Write given data to the output and apply the given style.
     * @see addRow
     *
     * @api
     * @param array $dataRow Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the row.
     * @return AbstractWriter
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowWithStyle(array $dataRow, $style)
    {
        if (!$style instanceof Style\Style) {
            throw new InvalidArgumentException('The "$style" argument must be a Style instance and cannot be NULL.');
        }

        $this->setRowStyle($style);
        $this->addRow($dataRow);
        $this->resetRowStyleToDefault();

        return $this;
    }

    /**
     * Write given data to the output. New data will be appended to end of stream.
     *
     * @api
     * @param  array $dataRows Array of array containing data to be streamed.
     *                         If a row is empty, it won't be added (i.e. not even as a blank row)
     *                         Example: $dataRows = [
     *                             ['data11', 12, , '', 'data13'],
     *                             ['data21', 'data22', null, false],
     *                         ];
     * @return AbstractWriter
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRows(array $dataRows)
    {
        if (!empty($dataRows)) {
            if (!is_array($dataRows[0])) {
                throw new InvalidArgumentException('The input should be an array of arrays');
            }

            foreach ($dataRows as $dataRow) {
                $this->addRow($dataRow);
            }
        }

        return $this;
    }

    /**
     * Write given data to the output and apply the given style.
     * @see addRows
     *
     * @api
     * @param array $dataRows Array of array containing data to be streamed.
     * @param Style\Style $style Style to be applied to the rows.
     * @return AbstractWriter
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If the input param is not valid
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException If this function is called before opening the writer
     * @throws \Box\Spout\Common\Exception\IOException If unable to write data
     */
    public function addRowsWithStyle(array $dataRows, $style)
    {
        if (!$style instanceof Style\Style) {
            throw new InvalidArgumentException('The "$style" argument must be a Style instance and cannot be NULL.');
        }

        $this->setRowStyle($style);
        $this->addRows($dataRows);
        $this->resetRowStyleToDefault();

        return $this;
    }

    /**
     * Returns the default style to be applied to rows.
     * Can be overriden by children to have a custom style.
     *
     * @return Style\Style
     */
    protected function getDefaultRowStyle()
    {
        return (new StyleBuilder())->build();
    }

    /**
     * Sets the style to be applied to the next written rows
     * until it is changed or reset.
     *
     * @param Style\Style $style
     * @return void
     */
    private function setRowStyle($style)
    {
        // Merge given style with the default one to inherit custom properties
        $this->rowStyle = $style->mergeWith($this->defaultRowStyle);
    }

    /**
     * Resets the style to be applied to the next written rows.
     *
     * @return void
     */
    private function resetRowStyleToDefault()
    {
        $this->rowStyle = $this->defaultRowStyle;
    }

    /**
     * Closes the writer. This will close the streamer as well, preventing new data
     * to be written to the file.
     *
     * @api
     * @return void
     */
    public function close()
    {
        $this->closeWriter();

        if (is_resource($this->filePointer)) {
            $this->globalFunctionsHelper->fclose($this->filePointer);
        }

        $this->isWriterOpened = false;
    }
}

