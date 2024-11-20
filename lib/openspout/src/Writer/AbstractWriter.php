<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;

abstract class AbstractWriter implements WriterInterface
{
    /** @var resource Pointer to the file/stream we will write to */
    protected $filePointer;

    /** @var string document creator */
    protected string $creator = 'OpenSpout';

    /** @var string Content-Type value for the header - to be defined by child class */
    protected static string $headerContentType;

    /** @var string Path to the output file */
    private string $outputFilePath;

    /** @var bool Indicates whether the writer has been opened or not */
    private bool $isWriterOpened = false;

    /** @var 0|positive-int */
    private int $writtenRowCount = 0;

    final public function openToFile($outputFilePath): void
    {
        $this->outputFilePath = $outputFilePath;

        $errorMessage = null;
        set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
            $errorMessage = $message;

            return true;
        });

        $resource = fopen($this->outputFilePath, 'w');
        restore_error_handler();
        if (null !== $errorMessage) {
            throw new IOException("Unable to open file {$this->outputFilePath}: {$errorMessage}");
        }
        \assert(false !== $resource);
        $this->filePointer = $resource;

        $this->openWriter();
        $this->isWriterOpened = true;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param mixed $outputFileName
     */
    final public function openToBrowser($outputFileName): void
    {
        $this->outputFilePath = basename($outputFileName);

        $resource = fopen('php://output', 'w');
        \assert(false !== $resource);
        $this->filePointer = $resource;

        // Clear any previous output (otherwise the generated file will be corrupted)
        // @see https://github.com/box/spout/issues/241
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        /*
         * Set headers
         *
         * For newer browsers such as Firefox, Chrome, Opera, Safari, etc., they all support and use `filename*`
         * specified by the new standard, even if they do not automatically decode filename; it does not matter;
         * and for older versions of Internet Explorer, they are not recognized `filename*`, will automatically
         * ignore it and use the old `filename` (the only minor flaw is that there must be an English suffix name).
         * In this way, the multi-browser multi-language compatibility problem is perfectly solved, which does not
         * require UA judgment and is more in line with the standard.
         *
         * @see https://github.com/box/spout/issues/745
         * @see https://tools.ietf.org/html/rfc6266
         * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition
         */
        header('Content-Type: '.static::$headerContentType);
        header(
            'Content-Disposition: attachment; '.
            'filename="'.rawurlencode($this->outputFilePath).'"; '.
            'filename*=UTF-8\'\''.rawurlencode($this->outputFilePath)
        );

        /*
         * When forcing the download of a file over SSL,IE8 and lower browsers fail
         * if the Cache-Control and Pragma headers are not set.
         *
         * @see http://support.microsoft.com/KB/323308
         * @see https://github.com/liuggio/ExcelBundle/issues/45
         */
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $this->openWriter();
        $this->isWriterOpened = true;
    }

    final public function addRow(Row $row): void
    {
        if (!$this->isWriterOpened) {
            throw new WriterNotOpenedException('The writer needs to be opened before adding row.');
        }

        $this->addRowToWriter($row);
        ++$this->writtenRowCount;
    }

    final public function addRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    final public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    final public function getWrittenRowCount(): int
    {
        return $this->writtenRowCount;
    }

    final public function close(): void
    {
        if (!$this->isWriterOpened) {
            return;
        }

        $this->closeWriter();

        fclose($this->filePointer);

        $this->isWriterOpened = false;
    }

    /**
     * Opens the streamer and makes it ready to accept data.
     *
     * @throws IOException If the writer cannot be opened
     */
    abstract protected function openWriter(): void;

    /**
     * Adds a row to the currently opened writer.
     *
     * @param Row $row The row containing cells and styles
     *
     * @throws WriterNotOpenedException If the workbook is not created yet
     * @throws IOException              If unable to write data
     */
    abstract protected function addRowToWriter(Row $row): void;

    /**
     * Closes the streamer, preventing any additional writing.
     */
    abstract protected function closeWriter(): void;
}
