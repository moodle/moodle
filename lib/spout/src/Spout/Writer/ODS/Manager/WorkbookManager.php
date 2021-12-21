<?php

namespace Box\Spout\Writer\ODS\Manager;

use Box\Spout\Writer\Common\Entity\Sheet;
use Box\Spout\Writer\Common\Manager\WorkbookManagerAbstract;
use Box\Spout\Writer\ODS\Helper\FileSystemHelper;
use Box\Spout\Writer\ODS\Manager\Style\StyleManager;

/**
 * Class WorkbookManager
 * ODS workbook manager, providing the interfaces to work with workbook.
 */
class WorkbookManager extends WorkbookManagerAbstract
{
    /**
     * Maximum number of rows a ODS sheet can contain
     * @see https://ask.libreoffice.org/en/question/8631/upper-limit-to-number-of-rows-in-calc/
     */
    protected static $maxRowsPerWorksheet = 1048576;

    /** @var WorksheetManager Object used to manage worksheets */
    protected $worksheetManager;

    /** @var FileSystemHelper Helper to perform file system operations */
    protected $fileSystemHelper;

    /** @var StyleManager Manages styles */
    protected $styleManager;

    /**
     * @return int Maximum number of rows/columns a sheet can contain
     */
    protected function getMaxRowsPerWorksheet()
    {
        return self::$maxRowsPerWorksheet;
    }

    /**
     * @param Sheet $sheet
     * @return string The file path where the data for the given sheet will be stored
     */
    public function getWorksheetFilePath(Sheet $sheet)
    {
        $sheetsContentTempFolder = $this->fileSystemHelper->getSheetsContentTempFolder();

        return $sheetsContentTempFolder . '/sheet' . $sheet->getIndex() . '.xml';
    }

    /**
     * Writes all the necessary files to disk and zip them together to create the final file.
     *
     * @param resource $finalFilePointer Pointer to the spreadsheet that will be created
     * @return void
     */
    protected function writeAllFilesToDiskAndZipThem($finalFilePointer)
    {
        $worksheets = $this->getWorksheets();
        $numWorksheets = \count($worksheets);

        $this->fileSystemHelper
            ->createContentFile($this->worksheetManager, $this->styleManager, $worksheets)
            ->deleteWorksheetTempFolder()
            ->createStylesFile($this->styleManager, $numWorksheets)
            ->zipRootFolderAndCopyToStream($finalFilePointer);
    }
}
