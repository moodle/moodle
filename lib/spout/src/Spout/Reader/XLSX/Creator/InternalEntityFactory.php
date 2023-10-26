<?php

namespace Box\Spout\Reader\XLSX\Creator;

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\InternalEntityFactoryInterface;
use Box\Spout\Reader\Common\Entity\Options;
use Box\Spout\Reader\Common\XMLProcessor;
use Box\Spout\Reader\Wrapper\XMLReader;
use Box\Spout\Reader\XLSX\Manager\SharedStringsManager;
use Box\Spout\Reader\XLSX\RowIterator;
use Box\Spout\Reader\XLSX\Sheet;
use Box\Spout\Reader\XLSX\SheetIterator;

/**
 * Class InternalEntityFactory
 * Factory to create entities
 */
class InternalEntityFactory implements InternalEntityFactoryInterface
{
    /** @var HelperFactory */
    private $helperFactory;

    /** @var ManagerFactory */
    private $managerFactory;

    /**
     * @param ManagerFactory $managerFactory
     * @param HelperFactory $helperFactory
     */
    public function __construct(ManagerFactory $managerFactory, HelperFactory $helperFactory)
    {
        $this->managerFactory = $managerFactory;
        $this->helperFactory = $helperFactory;
    }

    /**
     * @param string $filePath Path of the file to be read
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @param SharedStringsManager $sharedStringsManager Manages shared strings
     * @return SheetIterator
     */
    public function createSheetIterator($filePath, $optionsManager, $sharedStringsManager)
    {
        $sheetManager = $this->managerFactory->createSheetManager(
            $filePath,
            $optionsManager,
            $sharedStringsManager,
            $this
        );

        return new SheetIterator($sheetManager);
    }

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $sheetName Name of the sheet
     * @param bool $isSheetActive Whether the sheet was defined as active
     * @param bool $isSheetVisible Whether the sheet is visible
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @param SharedStringsManager $sharedStringsManager Manages shared strings
     * @return Sheet
     */
    public function createSheet(
        $filePath,
        $sheetDataXMLFilePath,
        $sheetIndex,
        $sheetName,
        $isSheetActive,
        $isSheetVisible,
        $optionsManager,
        $sharedStringsManager
    ) {
        $rowIterator = $this->createRowIterator($filePath, $sheetDataXMLFilePath, $optionsManager, $sharedStringsManager);

        return new Sheet($rowIterator, $sheetIndex, $sheetName, $isSheetActive, $isSheetVisible);
    }

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     * @param SharedStringsManager $sharedStringsManager Manages shared strings
     * @return RowIterator
     */
    private function createRowIterator($filePath, $sheetDataXMLFilePath, $optionsManager, $sharedStringsManager)
    {
        $xmlReader = $this->createXMLReader();
        $xmlProcessor = $this->createXMLProcessor($xmlReader);

        $styleManager = $this->managerFactory->createStyleManager($filePath, $this);
        $rowManager = $this->managerFactory->createRowManager($this);
        $shouldFormatDates = $optionsManager->getOption(Options::SHOULD_FORMAT_DATES);
        $shouldUse1904Dates = $optionsManager->getOption(Options::SHOULD_USE_1904_DATES);

        $cellValueFormatter = $this->helperFactory->createCellValueFormatter(
            $sharedStringsManager,
            $styleManager,
            $shouldFormatDates,
            $shouldUse1904Dates
        );

        $shouldPreserveEmptyRows = $optionsManager->getOption(Options::SHOULD_PRESERVE_EMPTY_ROWS);

        return new RowIterator(
            $filePath,
            $sheetDataXMLFilePath,
            $shouldPreserveEmptyRows,
            $xmlReader,
            $xmlProcessor,
            $cellValueFormatter,
            $rowManager,
            $this
        );
    }

    /**
     * @param Cell[] $cells
     * @return Row
     */
    public function createRow(array $cells = [])
    {
        return new Row($cells, null);
    }

    /**
     * @param mixed $cellValue
     * @return Cell
     */
    public function createCell($cellValue)
    {
        return new Cell($cellValue);
    }

    /**
     * @return \ZipArchive
     */
    public function createZipArchive()
    {
        return new \ZipArchive();
    }

    /**
     * @return XMLReader
     */
    public function createXMLReader()
    {
        return new XMLReader();
    }

    /**
     * @param $xmlReader
     * @return XMLProcessor
     */
    public function createXMLProcessor($xmlReader)
    {
        return new XMLProcessor($xmlReader);
    }
}
