<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

/**
 * @internal
 */
abstract class AbstractStyleManager implements StyleManagerInterface
{
    /** @var AbstractStyleRegistry Registry for all used styles */
    protected AbstractStyleRegistry $styleRegistry;

    public function __construct(AbstractStyleRegistry $styleRegistry)
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
    final public function registerStyle(Style $style): Style
    {
        return $this->styleRegistry->registerStyle($style);
    }

    /**
     * Apply additional styles if the given row needs it.
     * Typically, set "wrap text" if a cell contains a new line.
     *
     * @return PossiblyUpdatedStyle The eventually updated style
     */
    final public function applyExtraStylesIfNeeded(Cell $cell): PossiblyUpdatedStyle
    {
        return $this->applyWrapTextIfCellContainsNewLine($cell);
    }

    /**
     * Returns the default style.
     *
     * @return Style Default style
     */
    final protected function getDefaultStyle(): Style
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
    private function applyWrapTextIfCellContainsNewLine(Cell $cell): PossiblyUpdatedStyle
    {
        $cellStyle = $cell->getStyle();

        // if the "wrap text" option is already set, no-op
        if (!$cellStyle->hasSetWrapText() && $cell instanceof Cell\StringCell && str_contains($cell->getValue(), "\n")) {
            $cellStyle->setShouldWrapText();

            return new PossiblyUpdatedStyle($cellStyle, true);
        }

        return new PossiblyUpdatedStyle($cellStyle, false);
    }
}
