<?php

namespace OpenSpout\Writer\Common\Creator;

use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Manager\SheetManager;

/**
 * Factory to create internal entities.
 */
class InternalEntityFactory
{
    /**
     * @return Workbook
     */
    public function createWorkbook()
    {
        return new Workbook();
    }

    /**
     * @param string $worksheetFilePath
     *
     * @return Worksheet
     */
    public function createWorksheet($worksheetFilePath, Sheet $externalSheet)
    {
        return new Worksheet($worksheetFilePath, $externalSheet);
    }

    /**
     * @param int          $sheetIndex           Index of the sheet, based on order in the workbook (zero-based)
     * @param string       $associatedWorkbookId ID of the sheet's associated workbook
     * @param SheetManager $sheetManager         To manage sheets
     *
     * @return Sheet
     */
    public function createSheet($sheetIndex, $associatedWorkbookId, $sheetManager)
    {
        return new Sheet($sheetIndex, $associatedWorkbookId, $sheetManager);
    }

    /**
     * @return \ZipArchive
     */
    public function createZipArchive()
    {
        return new \ZipArchive();
    }
}
