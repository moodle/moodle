<?php

namespace OpenSpout\Reader\ODS\Creator;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\Creator\InternalEntityFactoryInterface;
use OpenSpout\Reader\Common\Entity\Options;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\ODS\RowIterator;
use OpenSpout\Reader\ODS\Sheet;
use OpenSpout\Reader\ODS\SheetIterator;
use OpenSpout\Reader\Wrapper\XMLReader;

/**
 * Factory to create entities.
 */
class InternalEntityFactory implements InternalEntityFactoryInterface
{
    /** @var HelperFactory */
    private $helperFactory;

    /** @var ManagerFactory */
    private $managerFactory;

    public function __construct(HelperFactory $helperFactory, ManagerFactory $managerFactory)
    {
        $this->helperFactory = $helperFactory;
        $this->managerFactory = $managerFactory;
    }

    /**
     * @param string                                            $filePath       Path of the file to be read
     * @param \OpenSpout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     *
     * @return SheetIterator
     */
    public function createSheetIterator($filePath, $optionsManager)
    {
        $escaper = $this->helperFactory->createStringsEscaper();
        $settingsHelper = $this->helperFactory->createSettingsHelper($this);

        return new SheetIterator($filePath, $optionsManager, $escaper, $settingsHelper, $this);
    }

    /**
     * @param XMLReader                                         $xmlReader      XML Reader
     * @param int                                               $sheetIndex     Index of the sheet, based on order in the workbook (zero-based)
     * @param string                                            $sheetName      Name of the sheet
     * @param bool                                              $isSheetActive  Whether the sheet was defined as active
     * @param bool                                              $isSheetVisible Whether the sheet is visible
     * @param \OpenSpout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     *
     * @return Sheet
     */
    public function createSheet($xmlReader, $sheetIndex, $sheetName, $isSheetActive, $isSheetVisible, $optionsManager)
    {
        $rowIterator = $this->createRowIterator($xmlReader, $optionsManager);

        return new Sheet($rowIterator, $sheetIndex, $sheetName, $isSheetActive, $isSheetVisible);
    }

    /**
     * @param Cell[] $cells
     *
     * @return Row
     */
    public function createRow(array $cells = [])
    {
        return new Row($cells, null);
    }

    /**
     * @param mixed $cellValue
     *
     * @return Cell
     */
    public function createCell($cellValue)
    {
        return new Cell($cellValue);
    }

    /**
     * @return XMLReader
     */
    public function createXMLReader()
    {
        return new XMLReader();
    }

    /**
     * @return \ZipArchive
     */
    public function createZipArchive()
    {
        return new \ZipArchive();
    }

    /**
     * @param XMLReader                                         $xmlReader      XML Reader
     * @param \OpenSpout\Common\Manager\OptionsManagerInterface $optionsManager Reader's options manager
     *
     * @return RowIterator
     */
    private function createRowIterator($xmlReader, $optionsManager)
    {
        $shouldFormatDates = $optionsManager->getOption(Options::SHOULD_FORMAT_DATES);
        $cellValueFormatter = $this->helperFactory->createCellValueFormatter($shouldFormatDates);
        $xmlProcessor = $this->createXMLProcessor($xmlReader);
        $rowManager = $this->managerFactory->createRowManager($this);

        return new RowIterator($xmlReader, $optionsManager, $cellValueFormatter, $xmlProcessor, $rowManager, $this);
    }

    /**
     * @param XMLReader $xmlReader
     *
     * @return XMLProcessor
     */
    private function createXMLProcessor($xmlReader)
    {
        return new XMLProcessor($xmlReader);
    }
}
