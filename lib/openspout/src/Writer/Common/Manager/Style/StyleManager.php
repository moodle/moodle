<?php

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

/**
 * Manages styles to be applied to a cell.
 */
class StyleManager implements StyleManagerInterface
{
    /** @var StyleRegistry Registry for all used styles */
    protected $styleRegistry;

    public function __construct(StyleRegistry $styleRegistry)
    {
        $this->styleRegistry = $styleRegistry;
    }

    /**
     * Registers the given style as a used style.
     * Duplicate styles won't be registered more than once.
     *
     * @param Style $style The style to be registered
     *
     * @return Style the registered style, updated with an internal ID
     */
    public function registerStyle($style)
    {
        return $this->styleRegistry->registerStyle($style);
    }

    /**
     * Apply additional styles if the given row needs it.
     * Typically, set "wrap text" if a cell contains a new line.
     *
     * @return PossiblyUpdatedStyle The eventually updated style
     */
    public function applyExtraStylesIfNeeded(Cell $cell): PossiblyUpdatedStyle
    {
        return $this->applyWrapTextIfCellContainsNewLine($cell);
    }

    /**
     * Returns the default style.
     *
     * @return Style Default style
     */
    protected function getDefaultStyle()
    {
        // By construction, the default style has ID 0
        return $this->styleRegistry->getRegisteredStyles()[0];
    }

    /**
     * Set the "wrap text" option if a cell of the given row contains a new line.
     *
     * @NOTE: There is a bug on the Mac version of Excel (2011 and below) where new lines
     *        are ignored even when the "wrap text" option is set. This only occurs with
     *        inline strings (shared strings do work fine).
     *        A workaround would be to encode "\n" as "_x000D_" but it does not work
     *        on the Windows version of Excel...
     *
     * @param Cell $cell The cell the style should be applied to
     *
     * @return PossiblyUpdatedStyle The eventually updated style
     */
    protected function applyWrapTextIfCellContainsNewLine(Cell $cell): PossiblyUpdatedStyle
    {
        $cellStyle = $cell->getStyle();

        // if the "wrap text" option is already set, no-op
        if (!$cellStyle->hasSetWrapText() && $cell->isString() && false !== strpos($cell->getValue(), "\n")) {
            $cellStyle->setShouldWrapText();

            return new PossiblyUpdatedStyle($cellStyle, true);
        }

        return new PossiblyUpdatedStyle($cellStyle, false);
    }
}
