<?php

namespace Box\Spout\Writer\Common\Entity;

/**
 * Class Worksheet
 * Entity describing a Worksheet
 */
class Worksheet
{
    /** @var string Path to the XML file that will contain the sheet data */
    private $filePath;

    /** @var resource Pointer to the sheet data file (e.g. xl/worksheets/sheet1.xml) */
    private $filePointer;

    /** @var Sheet The "external" sheet */
    private $externalSheet;

    /** @var int Maximum number of columns among all the written rows */
    private $maxNumColumns;

    /** @var int Index of the last written row */
    private $lastWrittenRowIndex;

    /**
     * Worksheet constructor.
     *
     * @param string $worksheetFilePath
     * @param Sheet $externalSheet
     */
    public function __construct($worksheetFilePath, Sheet $externalSheet)
    {
        $this->filePath = $worksheetFilePath;
        $this->filePointer = null;
        $this->externalSheet = $externalSheet;
        $this->maxNumColumns = 0;
        $this->lastWrittenRowIndex = 0;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return resource
     */
    public function getFilePointer()
    {
        return $this->filePointer;
    }

    /**
     * @param resource $filePointer
     */
    public function setFilePointer($filePointer)
    {
        $this->filePointer = $filePointer;
    }

    /**
     * @return Sheet
     */
    public function getExternalSheet()
    {
        return $this->externalSheet;
    }

    /**
     * @return int
     */
    public function getMaxNumColumns()
    {
        return $this->maxNumColumns;
    }

    /**
     * @param int $maxNumColumns
     */
    public function setMaxNumColumns($maxNumColumns)
    {
        $this->maxNumColumns = $maxNumColumns;
    }

    /**
     * @return int
     */
    public function getLastWrittenRowIndex()
    {
        return $this->lastWrittenRowIndex;
    }

    /**
     * @param int $lastWrittenRowIndex
     */
    public function setLastWrittenRowIndex($lastWrittenRowIndex)
    {
        $this->lastWrittenRowIndex = $lastWrittenRowIndex;
    }

    /**
     * @return int The ID of the worksheet
     */
    public function getId()
    {
        // sheet index is zero-based, while ID is 1-based
        return $this->externalSheet->getIndex() + 1;
    }
}
