<?php

namespace block_learnerscript\Spout\Writer\XLSX\Internal;

use block_learnerscript\Spout\Writer\Common\Internal\AbstractWorkbook;
use block_learnerscript\Spout\Writer\XLSX\Helper\FileSystemHelper;
use block_learnerscript\Spout\Writer\XLSX\Helper\SharedStringsHelper;
use block_learnerscript\Spout\Writer\XLSX\Helper\StyleHelper;
use block_learnerscript\Spout\Writer\Common\Sheet;

/**
 * Class Workbook
 * Represents a workbook within a XLSX file.
 * It provides the functions to work with worksheets.
 *
 * @package block_learnerscript\Spout\Writer\XLSX\Internal
 */
class Workbook extends AbstractWorkbook
{
    /**
     * Maximum number of rows a XLSX sheet can contain
     * @see http://office.microsoft.com/en-us/excel-help/excel-specifications-and-limits-HP010073849.aspx
     */
    protected static $maxRowsPerWorksheet = 1048576;

    /** @var bool Whether inline or shared strings should be used */
    protected $shouldUseInlineStrings;

    /** @var \block_learnerscript\Spout\Writer\XLSX\Helper\FileSystemHelper Helper to perform file system operations */
    protected $fileSystemHelper;

    /** @var \block_learnerscript\Spout\Writer\XLSX\Helper\SharedStringsHelper Helper to write shared strings */
    protected $sharedStringsHelper;

    /** @var \block_learnerscript\Spout\Writer\XLSX\Helper\StyleHelper Helper to apply styles */
    protected $styleHelper;

    /**
     * @param string $tempFolder
     * @param bool $shouldUseInlineStrings
     * @param bool $shouldCreateNewSheetsAutomatically
     * @param \block_learnerscript\Spout\Writer\Style\Style $defaultRowStyle
     * @throws \block_learnerscript\Spout\Common\Exception\IOException If unable to create at least one of the base folders
     */
    public function __construct($tempFolder, $shouldUseInlineStrings, $shouldCreateNewSheetsAutomatically, $defaultRowStyle)
    {
        parent::__construct($shouldCreateNewSheetsAutomatically, $defaultRowStyle);

        $this->shouldUseInlineStrings = $shouldUseInlineStrings;

        $this->fileSystemHelper = new FileSystemHelper($tempFolder);
        $this->fileSystemHelper->createBaseFilesAndFolders();

        $this->styleHelper = new StyleHelper($defaultRowStyle);

        // This helper will be shared by all sheets
        $xlFolder = $this->fileSystemHelper->getXlFolder();
        $this->sharedStringsHelper = new SharedStringsHelper($xlFolder);
    }

    /**
     * @return \block_learnerscript\Spout\Writer\XLSX\Helper\StyleHelper Helper to apply styles to XLSX files
     */
    protected function getStyleHelper()
    {
        return $this->styleHelper;
    }

    /**
     * @return int Maximum number of rows/columns a sheet can contain
     */
    protected function getMaxRowsPerWorksheet()
    {
        return self::$maxRowsPerWorksheet;
    }

    /**
     * Creates a new sheet in the workbook. The current sheet remains unchanged.
     *
     * @return Worksheet The created sheet
     * @throws \block_learnerscript\Spout\Common\Exception\IOException If unable to open the sheet for writing
     */
    public function addNewSheet()
    {
        $newSheetIndex = count($this->worksheets);
        $sheet = new Sheet($newSheetIndex);

        $worksheetFilesFolder = $this->fileSystemHelper->getXlWorksheetsFolder();
        $worksheet = new Worksheet($sheet, $worksheetFilesFolder, $this->sharedStringsHelper, $this->styleHelper, $this->shouldUseInlineStrings);
        $this->worksheets[] = $worksheet;

        return $worksheet;
    }

    /**
     * Closes the workbook and all its associated sheets.
     * All the necessary files are written to disk and zipped together to create the XLSX file.
     * All the temporary files are then deleted.
     *
     * @param resource $finalFilePointer Pointer to the XLSX that will be created
     * @return void
     */
    public function close($finalFilePointer)
    {
        /** @var Worksheet[] $worksheets */
        $worksheets = $this->worksheets;

        foreach ($worksheets as $worksheet) {
            $worksheet->close();
        }

        $this->sharedStringsHelper->close();

        // Finish creating all the necessary files before zipping everything together
        $this->fileSystemHelper
            ->createContentTypesFile($worksheets)
            ->createWorkbookFile($worksheets)
            ->createWorkbookRelsFile($worksheets)
            ->createStylesFile($this->styleHelper)
            ->zipRootFolderAndCopyToStream($finalFilePointer);

        $this->cleanupTempFolder();
    }

    /**
     * Deletes the root folder created in the temp folder and all its contents.
     *
     * @return void
     */
    protected function cleanupTempFolder()
    {
        $xlsxRootFolder = $this->fileSystemHelper->getRootFolder();
        $this->fileSystemHelper->deleteFolderRecursively($xlsxRootFolder);
    }
}
