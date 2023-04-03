<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Entity\Worksheet;

/**
 * @internal
 */
interface WorksheetManagerInterface
{
    /**
     * Adds a row to the worksheet.
     *
     * @param Worksheet $worksheet The worksheet to add the row to
     * @param Row       $row       The row to be added
     *
     * @throws \OpenSpout\Common\Exception\IOException              If the data cannot be written
     * @throws \OpenSpout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    public function addRow(Worksheet $worksheet, Row $row): void;

    /**
     * Prepares the worksheet to accept data.
     *
     * @param Worksheet $worksheet The worksheet to start
     *
     * @throws \OpenSpout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    public function startSheet(Worksheet $worksheet): void;

    /**
     * Closes the worksheet.
     */
    public function close(Worksheet $worksheet): void;
}
