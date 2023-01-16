<?php

namespace OpenSpout\Reader\Common\Creator;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;

/**
 * Interface EntityFactoryInterface.
 */
interface InternalEntityFactoryInterface
{
    /**
     * @param Cell[] $cells
     *
     * @return Row
     */
    public function createRow(array $cells = []);

    /**
     * @param mixed $cellValue
     *
     * @return Cell
     */
    public function createCell($cellValue);
}
