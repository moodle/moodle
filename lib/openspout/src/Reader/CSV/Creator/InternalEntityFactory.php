<?php

namespace OpenSpout\Reader\CSV\Creator;

use OpenSpout\Common\Creator\HelperFactory;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Helper\GlobalFunctionsHelper;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Reader\Common\Creator\InternalEntityFactoryInterface;
use OpenSpout\Reader\CSV\RowIterator;
use OpenSpout\Reader\CSV\Sheet;
use OpenSpout\Reader\CSV\SheetIterator;

/**
 * Factory to create entities.
 */
class InternalEntityFactory implements InternalEntityFactoryInterface
{
    /** @var HelperFactory */
    private $helperFactory;

    public function __construct(HelperFactory $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    /**
     * @param resource                $filePointer           Pointer to the CSV file to read
     * @param OptionsManagerInterface $optionsManager
     * @param GlobalFunctionsHelper   $globalFunctionsHelper
     *
     * @return SheetIterator
     */
    public function createSheetIterator($filePointer, $optionsManager, $globalFunctionsHelper)
    {
        $rowIterator = $this->createRowIterator($filePointer, $optionsManager, $globalFunctionsHelper);
        $sheet = $this->createSheet($rowIterator);

        return new SheetIterator($sheet);
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
     * @return Row
     */
    public function createRowFromArray(array $cellValues = [])
    {
        $cells = array_map(function ($cellValue) {
            return $this->createCell($cellValue);
        }, $cellValues);

        return $this->createRow($cells);
    }

    /**
     * @param RowIterator $rowIterator
     *
     * @return Sheet
     */
    private function createSheet($rowIterator)
    {
        return new Sheet($rowIterator);
    }

    /**
     * @param resource                $filePointer           Pointer to the CSV file to read
     * @param OptionsManagerInterface $optionsManager
     * @param GlobalFunctionsHelper   $globalFunctionsHelper
     *
     * @return RowIterator
     */
    private function createRowIterator($filePointer, $optionsManager, $globalFunctionsHelper)
    {
        $encodingHelper = $this->helperFactory->createEncodingHelper($globalFunctionsHelper);

        return new RowIterator($filePointer, $optionsManager, $encodingHelper, $this, $globalFunctionsHelper);
    }
}
