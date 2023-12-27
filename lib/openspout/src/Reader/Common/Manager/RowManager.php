<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;

/**
 * @internal
 */
final class RowManager
{
    /**
     * Fills the missing indexes of a row with empty cells.
     */
    public function fillMissingIndexesWithEmptyCells(Row $row): void
    {
        $numCells = $row->getNumCells();

        if (0 === $numCells) {
            return;
        }

        $rowCells = $row->getCells();
        $maxCellIndex = $numCells;

        /**
         * If the row has empty cells, calling "setCellAtIndex" will add the cell
         * but in the wrong place (the new cell is added at the end of the array).
         * Therefore, we need to sort the array using keys to have proper order.
         *
         * @see https://github.com/box/spout/issues/740
         */
        $needsSorting = false;

        for ($cellIndex = 0; $cellIndex < $maxCellIndex; ++$cellIndex) {
            if (!isset($rowCells[$cellIndex])) {
                $row->setCellAtIndex(Cell::fromValue(''), $cellIndex);
                $needsSorting = true;
            }
        }

        if ($needsSorting) {
            $rowCells = $row->getCells();
            ksort($rowCells);
            $row->setCells($rowCells);
        }
    }
}
