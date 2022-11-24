<?php

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

/**
 * Interface StyleHManagernterface.
 */
interface StyleManagerInterface
{
    /**
     * Registers the given style as a used style.
     * Duplicate styles won't be registered more than once.
     *
     * @param Style $style The style to be registered
     *
     * @return Style the registered style, updated with an internal ID
     */
    public function registerStyle($style);

    /**
     * Apply additional styles if the given row needs it.
     * Typically, set "wrap text" if a cell contains a new line.
     *
     * @return PossiblyUpdatedStyle The eventually updated style
     */
    public function applyExtraStylesIfNeeded(Cell $cell): PossiblyUpdatedStyle;
}
