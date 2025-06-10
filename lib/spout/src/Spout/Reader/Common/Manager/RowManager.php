<?php

namespace Box\Spout\Reader\Common\Manager;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\InternalEntityFactoryInterface;

/**
 * Class RowManager
 */
class RowManager
{
    /** @var InternalEntityFactoryInterface Factory to create entities */
    private $entityFactory;

    /**
     * @param InternalEntityFactoryInterface $entityFactory Factory to create entities
     */
    public function __construct(InternalEntityFactoryInterface $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * Detect whether a row is considered empty.
     * An empty row has all of its cells empty.
     *
     * @param Row $row
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

    /**
     * Fills the missing indexes of a row with empty cells.
     *
     * @param Row $row
     * @return Row
     */
    public function fillMissingIndexesWithEmptyCells(Row $row)
    {
        $numCells = $row->getNumCells();

        if ($numCells === 0) {
            return $row;
        }

        $rowCells = $row->getCells();
        $maxCellIndex = $numCells;

        // If the row has empty cells, calling "setCellAtIndex" will add the cell
        // but in the wrong place (the new cell is added at the end of the array).
        // Therefore, we need to sort the array using keys to have proper order.
        // @see https://github.com/box/spout/issues/740
        $needsSorting = false;

        for ($cellIndex = 0; $cellIndex < $maxCellIndex; $cellIndex++) {
            if (!isset($rowCells[$cellIndex])) {
                $row->setCellAtIndex($this->entityFactory->createCell(''), $cellIndex);
                $needsSorting = true;
            }
        }

        if ($needsSorting) {
            $rowCells = $row->getCells();
            ksort($rowCells);
            $row->setCells($rowCells);
        }

        return $row;
    }
}
