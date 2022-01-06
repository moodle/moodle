<?php

namespace Box\Spout\Writer\XLSX\Manager;

use Box\Spout\Writer\Common\Entity\Sheet;
use Box\Spout\Writer\Common\Manager\WorkbookManagerAbstract;
use Box\Spout\Writer\XLSX\Helper\FileSystemHelper;
use Box\Spout\Writer\XLSX\Manager\Style\StyleManager;

/**
 * Class WorkbookManager
 * XLSX workbook manager, providing the interfaces to work with workbook.
 */
class WorkbookManager extends WorkbookManagerAbstract
{
    /**
     * Maximum number of rows a XLSX sheet can contain
     * @see http://office.microsoft.com/en-us/excel-help/excel-specifications-and-limits-HP010073849.aspx
     */
    protected static $maxRowsPerWorksheet = 1048576;

    /** @var WorksheetManager Object used to manage worksheets */
    protected $worksheetManager;

    /** @var StyleManager Manages styles */
    protected $styleManager;

    /** @var FileSystemHelper Helper to perform file system operations */
    protected $fileSystemHelper;

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
        $worksheetFilesFolder = $this->fileSystemHelper->getXlWorksheetsFolder();

        return $worksheetFilesFolder . '/' . strtolower($sheet->getName()) . '.xml';
    }

    /**
     * Closes custom objects that are still opened
     *
     * @return void
     */
    protected function closeRemainingObjects()
    {
        $this->worksheetManager->getSharedStringsManager()->close();
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

        $this->fileSystemHelper
            ->createContentTypesFile($worksheets)
            ->createWorkbookFile($worksheets)
            ->createWorkbookRelsFile($worksheets)
            ->createStylesFile($this->styleManager)
            ->zipRootFolderAndCopyToStream($finalFilePointer);
    }
}
