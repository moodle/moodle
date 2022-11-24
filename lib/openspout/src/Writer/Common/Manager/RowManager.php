<?php

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;

class RowManager
{
    /**
     * Detect whether a row is considered empty.
     * An empty row has all of its cells empty.
     *
     * @return bool
     */
    public function isEmpty(Row $row)
    {
        foreach ($row->getCells() as $cell) {
            if (!$cell->isEmpty()) {
                return false;
            }
        }

        return true;
    }
}
