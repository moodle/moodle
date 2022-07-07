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
        if ($row->getNumCells() === 0) {
            return $row;
        }

        $rowCells = $row->getCells();
        $maxCellIndex = max(array_keys($rowCells));

        for ($cellIndex = 0; $cellIndex < $maxCellIndex; $cellIndex++) {
            if (!isset($rowCells[$cellIndex])) {
                $row->setCellAtIndex($this->entityFactory->createCell(''), $cellIndex);
            }
        }

        return $row;
    }
}
