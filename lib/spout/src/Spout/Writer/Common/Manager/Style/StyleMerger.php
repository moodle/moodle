<?php

namespace Box\Spout\Writer\Common\Manager\Style;

use Box\Spout\Common\Entity\Style\Style;

/**
 * Class StyleMerger
 * Takes care of merging styles together
 */
class StyleMerger
{
    /**
     * Merges the current style with the given style, using the given style as a base. This means that:
     *   - if current style and base style both have property A set, use current style property's value
     *   - if current style has property A set but base style does not, use current style property's value
     *   - if base style has property A set but current style does not, use base style property's value
     *
     * @NOTE: This function returns a new style.
     *
     * @param Style $style
     * @param Style $baseStyle
     * @return Style New style corresponding to the merge of the 2 styles
     */
    public function merge(Style $style, Style $baseStyle)
    {
        $mergedStyle = clone $style;

        $this->mergeFontStyles($mergedStyle, $style, $baseStyle);
        $this->mergeOtherFontProperties($mergedStyle, $style, $baseStyle);
        $this->mergeCellProperties($mergedStyle, $style, $baseStyle);

        return $mergedStyle;
    }

    /**
     * @param Style $styleToUpdate (passed as reference)
     * @param Style $style
     * @param Style $baseStyle
     * @return void
     */
    private function mergeFontStyles(Style $styleToUpdate, Style $style, Style $baseStyle)
    {
        if (!$style->hasSetFontBold() && $baseStyle->isFontBold()) {
            $styleToUpdate->setFontBold();
        }
        if (!$style->hasSetFontItalic() && $baseStyle->isFontItalic()) {
            $styleToUpdate->setFontItalic();
        }
        if (!$style->hasSetFontUnderline() && $baseStyle->isFontUnderline()) {
            $styleToUpdate->setFontUnderline();
        }
        if (!$style->hasSetFontStrikethrough() && $baseStyle->isFontStrikethrough()) {
            $styleToUpdate->setFontStrikethrough();
        }
    }

    /**
     * @param Style $styleToUpdate Style to update (passed as reference)
     * @param Style $style
     * @param Style $baseStyle
     * @return void
     */
    private function mergeOtherFontProperties(Style $styleToUpdate, Style $style, Style $baseStyle)
    {
        if (!$style->hasSetFontSize() && $baseStyle->getFontSize() !== Style::DEFAULT_FONT_SIZE) {
            $styleToUpdate->setFontSize($baseStyle->getFontSize());
        }
        if (!$style->hasSetFontColor() && $baseStyle->getFontColor() !== Style::DEFAULT_FONT_COLOR) {
            $styleToUpdate->setFontColor($baseStyle->getFontColor());
        }
        if (!$style->hasSetFontName() && $baseStyle->getFontName() !== Style::DEFAULT_FONT_NAME) {
            $styleToUpdate->setFontName($baseStyle->getFontName());
        }
    }

    /**
     * @param Style $styleToUpdate Style to update (passed as reference)
     * @param Style $style
     * @param Style $baseStyle
     * @return void
     */
    private function mergeCellProperties(Style $styleToUpdate, Style $style, Style $baseStyle)
    {
        if (!$style->hasSetWrapText() && $baseStyle->shouldWrapText()) {
            $styleToUpdate->setShouldWrapText();
        }
        if (!$style->hasSetCellAlignment() && $baseStyle->shouldApplyCellAlignment()) {
            $styleToUpdate->setCellAlignment($baseStyle->getCellAlignment());
        }
        if (!$style->getBorder() && $baseStyle->shouldApplyBorder()) {
            $styleToUpdate->setBorder($baseStyle->getBorder());
        }
        if (!$style->getFormat() && $baseStyle->shouldApplyFormat()) {
            $styleToUpdate->setFormat($baseStyle->getFormat());
        }
        if (!$style->shouldApplyBackgroundColor() && $baseStyle->shouldApplyBackgroundColor()) {
            $styleToUpdate->setBackgroundColor($baseStyle->getBackgroundColor());
        }
    }
}
