<?php

namespace OpenSpout\Writer\XLSX;

use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\WriterMultiSheetsAbstract;

/**
 * This class provides base support to write data to XLSX files.
 */
class Writer extends WriterMultiSheetsAbstract
{
    /** @var string Content-Type value for the header */
    protected static $headerContentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * Sets a custom temporary folder for creating intermediate files/folders.
     * This must be set before opening the writer.
     *
     * @param string $tempFolder Temporary folder where the files to create the XLSX will be stored
     *
     * @throws \OpenSpout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     *
     * @return Writer
     */
    public function setTempFolder($tempFolder)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->optionsManager->setOption(Options::TEMP_FOLDER, $tempFolder);

        return $this;
    }

    /**
     * Use inline string to be more memory efficient. If set to false, it will use shared strings.
     * This must be set before opening the writer.
     *
     * @param bool $shouldUseInlineStrings Whether inline or shared strings should be used
     *
     * @throws \OpenSpout\Writer\Exception\WriterAlreadyOpenedException If the writer was already opened
     *
     * @return Writer
     */
    public function setShouldUseInlineStrings($shouldUseInlineStrings)
    {
        $this->throwIfWriterAlreadyOpened('Writer must be configured before opening it.');

        $this->optionsManager->setOption(Options::SHOULD_USE_INLINE_STRINGS, $shouldUseInlineStrings);

        return $this;
    }

    /**
     * Merge cells.
     * Row coordinates are indexed from 1, columns from 0 (A = 0),
     * so a merge B2:G2 looks like $writer->mergeCells([1,2], [6, 2]);.
     *
     * You may use CellHelper::getColumnLettersFromColumnIndex() to convert from "B2" to "[1,2]"
     *
     * @param int[] $range1 - top left cell's coordinate [column, row]
     * @param int[] $range2 - bottom right cell's coordinate [column, row]
     *
     * @return $this
     */
    public function mergeCells(array $range1, array $range2)
    {
        $this->optionsManager->addOption(Options::MERGE_CELLS, [$range1, $range2]);

        return $this;
    }
}
