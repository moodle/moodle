<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Manager\AbstractWorkbookManager;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\XLSX\Helper\FileSystemHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\Options;

/**
 * @internal
 *
 * @property WorksheetManager $worksheetManager
 * @property StyleManager     $styleManager
 * @property FileSystemHelper $fileSystemHelper
 * @property Options          $options
 */
final class WorkbookManager extends AbstractWorkbookManager
{
    /**
     * Maximum number of rows a XLSX sheet can contain.
     *
     * @see http://office.microsoft.com/en-us/excel-help/excel-specifications-and-limits-HP010073849.aspx
     */
    private static int $maxRowsPerWorksheet = 1048576;

    public function __construct(
        Workbook $workbook,
        Options $options,
        WorksheetManager $worksheetManager,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        FileSystemHelper $fileSystemHelper
    ) {
        parent::__construct(
            $workbook,
            $options,
            $worksheetManager,
            $styleManager,
            $styleMerger,
            $fileSystemHelper
        );
    }

    /**
     * @return int Maximum number of rows/columns a sheet can contain
     */
    protected function getMaxRowsPerWorksheet(): int
    {
        return self::$maxRowsPerWorksheet;
    }

    /**
     * Closes custom objects that are still opened.
     */
    protected function closeRemainingObjects(): void
    {
        $this->worksheetManager->getSharedStringsManager()->close();
    }

    /**
     * Writes all the necessary files to disk and zip them together to create the final file.
     *
     * @param resource $finalFilePointer Pointer to the spreadsheet that will be created
     */
    protected function writeAllFilesToDiskAndZipThem($finalFilePointer): void
    {
        $worksheets = $this->getWorksheets();

        $this->fileSystemHelper
            ->createContentFiles($this->options, $worksheets)
            ->deleteWorksheetTempFolder()
            ->createContentTypesFile($worksheets)
            ->createWorkbookFile($worksheets)
            ->createWorkbookRelsFile($worksheets)
            ->createWorksheetRelsFiles($worksheets)
            ->createStylesFile($this->styleManager)
            ->zipRootFolderAndCopyToStream($finalFilePointer)
        ;
    }
}
