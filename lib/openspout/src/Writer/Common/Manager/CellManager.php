<?php

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;

class CellManager
{
    /**
     * @var StyleMerger
     */
    protected $styleMerger;

    public function __construct(StyleMerger $styleMerger)
    {
        $this->styleMerger = $styleMerger;
    }

    /**
     * Merges a Style into a cell's Style.
     */
    public function applyStyle(Cell $cell, Style $style)
    {
        $mergedStyle = $this->styleMerger->merge($cell->getStyle(), $style);
        $cell->setStyle($mergedStyle);
    }
}
