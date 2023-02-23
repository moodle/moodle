<?php

namespace OpenSpout\Reader\CSV;

use OpenSpout\Reader\SheetInterface;

class Sheet implements SheetInterface
{
    /** @var \OpenSpout\Reader\CSV\RowIterator To iterate over the CSV's rows */
    protected $rowIterator;

    /**
     * @param RowIterator $rowIterator Corresponding row iterator
     */
    public function __construct(RowIterator $rowIterator)
    {
        $this->rowIterator = $rowIterator;
    }

    /**
     * @return \OpenSpout\Reader\CSV\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * @return int Index of the sheet
     */
    public function getIndex()
    {
        return 0;
    }

    /**
     * @return string Name of the sheet - empty string since CSV does not support that
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return bool Always TRUE as there is only one sheet
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @return bool Always TRUE as the only sheet is always visible
     */
    public function isVisible()
    {
        return true;
    }
}
