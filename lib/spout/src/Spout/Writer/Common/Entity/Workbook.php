<?php

namespace Box\Spout\Writer\Common\Entity;

/**
 * Class Workbook
 * Entity describing a workbook
 */
class Workbook
{
    /** @var Worksheet[] List of the workbook's sheets */
    private $worksheets = [];

    /** @var string Timestamp based unique ID identifying the workbook */
    private $internalId;

    /**
     * Workbook constructor.
     */
    public function __construct()
    {
        $this->internalId = \uniqid();
    }

    /**
     * @return Worksheet[]
     */
    public function getWorksheets()
    {
        return $this->worksheets;
    }

    /**
     * @param Worksheet[] $worksheets
     */
    public function setWorksheets($worksheets)
    {
        $this->worksheets = $worksheets;
    }

    /**
     * @return string
     */
    public function getInternalId()
    {
        return $this->internalId;
    }
}
