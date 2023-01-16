<?php

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use OpenSpout\Writer\Common\Creator\ManagerFactoryInterface;
use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Manager\Style\StyleManagerInterface;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Exception\SheetNotFoundException;

/**
 * Abstract workbook manager, providing the generic interfaces to work with workbook.
 */
abstract class WorkbookManagerAbstract implements WorkbookManagerInterface
{
    /** @var null|Workbook The workbook to manage */
    protected $workbook;

    /** @var OptionsManagerInterface */
    protected $optionsManager;

    /** @var WorksheetManagerInterface */
    protected $worksheetManager;

    /** @var StyleManagerInterface Manages styles */
    protected $styleManager;

    /** @var StyleMerger Helper to merge styles */
    protected $styleMerger;

    /** @var FileSystemWithRootFolderHelperInterface Helper to perform file system operations */
    protected $fileSystemHelper;

    /** @var InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var ManagerFactoryInterface Factory to create managers */
    protected $managerFactory;

    /** @var Worksheet The worksheet where data will be written to */
    protected $currentWorksheet;

    public function __construct(
        Workbook $workbook,
        OptionsManagerInterface $optionsManager,
        WorksheetManagerInterface $worksheetManager,
        StyleManagerInterface $styleManager,
        StyleMerger $styleMerger,
        FileSystemWithRootFolderHelperInterface $fileSystemHelper,
        InternalEntityFactory $entityFactory,
        ManagerFactoryInterface $managerFactory
    ) {
        $this->workbook = $workbook;
        $this->optionsManager = $optionsManager;
        $this->worksheetManager = $worksheetManager;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->fileSystemHelper = $fileSystemHelper;
        $this->entityFactory = $entityFactory;
        $this->managerFactory = $managerFactory;
    }

    /**
     * @return null|Workbook
     */
    public function getWorkbook()
    {
        return $this->workbook;
    }

    /**
     * Creates a new sheet in the workbook and make it the current sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @return Worksheet The created sheet
     */
    public function addNewSheetAndMakeItCurrent()
    {
        $worksheet = $this->addNewSheet();
        $this->setCurrentWorksheet($worksheet);

        return $worksheet;
    }

    /**
     * @return Worksheet[] All the workbook's sheets
     */
    public function getWorksheets()
    {
        return $this->workbook->getWorksheets();
    }

    /**
     * Returns the current sheet.
     *
     * @return Worksheet The current sheet
     */
    public function getCurrentWorksheet()
    {
        return $this->currentWorksheet;
    }

    /**
     * Starts the current sheet and opens the file pointer.
     *
     * @throws IOException
     */
    public function startCurrentSheet()
    {
        $this->worksheetManager->startSheet($this->getCurrentWorksheet());
    }

    /**
     * Sets the given sheet as the current one. New data will be written to this sheet.
     * The writing will resume where it stopped (i.e. data won't be truncated).
     *
     * @param Sheet $sheet The "external" sheet to set as current
     *
     * @throws SheetNotFoundException If the given sheet does not exist in the workbook
     */
    public function setCurrentSheet(Sheet $sheet)
    {
        $worksheet = $this->getWorksheetFromExternalSheet($sheet);
        if (null !== $worksheet) {
            $this->currentWorksheet = $worksheet;
        } else {
            throw new SheetNotFoundException('The given sheet does not exist in the workbook.');
        }
    }

    /**
     * Adds a row to the current sheet.
     * If shouldCreateNewSheetsAutomatically option is set to true, it will handle pagination
     * with the creation of new worksheets if one worksheet has reached its maximum capicity.
     *
     * @param Row $row The row to be added
     *
     * @throws IOException                                          If trying to create a new sheet and unable to open the sheet for writing
     * @throws \OpenSpout\Common\Exception\InvalidArgumentException
     */
    public function addRowToCurrentWorksheet(Row $row)
    {
        $currentWorksheet = $this->getCurrentWorksheet();
        $hasReachedMaxRows = $this->hasCurrentWorksheetReachedMaxRows();

        // if we reached the maximum number of rows for the current sheet...
        if ($hasReachedMaxRows) {
            // ... continue writing in a new sheet if option set
            if ($this->optionsManager->getOption(Options::SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY)) {
                $currentWorksheet = $this->addNewSheetAndMakeItCurrent();

                $this->addRowToWorksheet($currentWorksheet, $row);
            }
            // otherwise, do nothing as the data won't be written anyways
        } else {
            $this->addRowToWorksheet($currentWorksheet, $row);
        }
    }

    public function setDefaultColumnWidth(float $width)
    {
        $this->worksheetManager->setDefaultColumnWidth($width);
    }

    public function setDefaultRowHeight(float $height)
    {
        $this->worksheetManager->setDefaultRowHeight($height);
    }

    /**
     * @param int ...$columns One or more columns with this width
     */
    public function setColumnWidth(float $width, ...$columns)
    {
        $this->worksheetManager->setColumnWidth($width, ...$columns);
    }

    /**
     * @param float $width The width to set
     * @param int   $start First column index of the range
     * @param int   $end   Last column index of the range
     */
    public function setColumnWidthForRange(float $width, int $start, int $end)
    {
        $this->worksheetManager->setColumnWidthForRange($width, $start, $end);
    }

    /**
     * Closes the workbook and all its associated sheets.
     * All the necessary files are written to disk and zipped together to create the final file.
     * All the temporary files are then deleted.
     *
     * @param resource $finalFilePointer Pointer to the spreadsheet that will be created
     */
    public function close($finalFilePointer)
    {
        $this->closeAllWorksheets();
        $this->closeRemainingObjects();
        $this->writeAllFilesToDiskAndZipThem($finalFilePointer);
        $this->cleanupTempFolder();
    }

    /**
     * @return int Maximum number of rows/columns a sheet can contain
     */
    abstract protected function getMaxRowsPerWorksheet();

    /**
     * @return string The file path where the data for the given sheet will be stored
     */
    abstract protected function getWorksheetFilePath(Sheet $sheet);

    /**
     * Closes custom objects that are still opened.
     */
    protected function closeRemainingObjects()
    {
        // do nothing by default
    }

    /**
     * Writes all the necessary files to disk and zip them together to create the final file.
     *
     * @param resource $finalFilePointer Pointer to the spreadsheet that will be created
     */
    abstract protected function writeAllFilesToDiskAndZipThem($finalFilePointer);

    /**
     * Deletes the root folder created in the temp folder and all its contents.
     */
    protected function cleanupTempFolder()
    {
        $rootFolder = $this->fileSystemHelper->getRootFolder();
        $this->fileSystemHelper->deleteFolderRecursively($rootFolder);
    }

    /**
     * Creates a new sheet in the workbook. The current sheet remains unchanged.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to open the sheet for writing
     *
     * @return Worksheet The created sheet
     */
    private function addNewSheet()
    {
        $worksheets = $this->getWorksheets();

        $newSheetIndex = \count($worksheets);
        $sheetManager = $this->managerFactory->createSheetManager();
        $sheet = $this->entityFactory->createSheet($newSheetIndex, $this->workbook->getInternalId(), $sheetManager);

        $worksheetFilePath = $this->getWorksheetFilePath($sheet);
        $worksheet = $this->entityFactory->createWorksheet($worksheetFilePath, $sheet);

        $this->worksheetManager->startSheet($worksheet);

        $worksheets[] = $worksheet;
        $this->workbook->setWorksheets($worksheets);

        return $worksheet;
    }

    /**
     * @param Worksheet $worksheet
     */
    private function setCurrentWorksheet($worksheet)
    {
        $this->currentWorksheet = $worksheet;
    }

    /**
     * Returns the worksheet associated to the given external sheet.
     *
     * @param Sheet $sheet
     *
     * @return null|Worksheet the worksheet associated to the given external sheet or null if not found
     */
    private function getWorksheetFromExternalSheet($sheet)
    {
        $worksheetFound = null;

        foreach ($this->getWorksheets() as $worksheet) {
            if ($worksheet->getExternalSheet() === $sheet) {
                $worksheetFound = $worksheet;

                break;
            }
        }

        return $worksheetFound;
    }

    /**
     * @return bool whether the current worksheet has reached the maximum number of rows per sheet
     */
    private function hasCurrentWorksheetReachedMaxRows()
    {
        $currentWorksheet = $this->getCurrentWorksheet();

        return $currentWorksheet->getLastWrittenRowIndex() >= $this->getMaxRowsPerWorksheet();
    }

    /**
     * Adds a row to the given sheet.
     *
     * @param Worksheet $worksheet Worksheet to write the row to
     * @param Row       $row       The row to be added
     *
     * @throws IOException
     * @throws \OpenSpout\Common\Exception\InvalidArgumentException
     */
    private function addRowToWorksheet(Worksheet $worksheet, Row $row)
    {
        $this->applyDefaultRowStyle($row);
        $this->worksheetManager->addRow($worksheet, $row);

        // update max num columns for the worksheet
        $currentMaxNumColumns = $worksheet->getMaxNumColumns();
        $cellsCount = $row->getNumCells();
        $worksheet->setMaxNumColumns(max($currentMaxNumColumns, $cellsCount));
    }

    private function applyDefaultRowStyle(Row $row)
    {
        $defaultRowStyle = $this->optionsManager->getOption(Options::DEFAULT_ROW_STYLE);

        if (null !== $defaultRowStyle) {
            $mergedStyle = $this->styleMerger->merge($row->getStyle(), $defaultRowStyle);
            $row->setStyle($mergedStyle);
        }
    }

    /**
     * Closes all workbook's associated sheets.
     */
    private function closeAllWorksheets()
    {
        $worksheets = $this->getWorksheets();

        foreach ($worksheets as $worksheet) {
            $this->worksheetManager->close($worksheet);
        }
    }
}
