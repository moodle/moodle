<?php

namespace OpenSpout\Writer\ODS\Helper;

use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;

/**
 * The fo:border, fo:border-top, fo:border-bottom, fo:border-left and fo:border-right attributes
 * specify border properties
 * http://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html#__RefHeading__1419780_253892949.
 *
 * Example table-cell-properties
 *
 * <style:table-cell-properties
 * fo:border-bottom="0.74pt solid #ffc000" style:diagonal-bl-tr="none"
 * style:diagonal-tl-br="none" fo:border-left="none" fo:border-right="none"
 * style:rotation-align="none" fo:border-top="none"/>
 */
class BorderHelper
{
    /**
     * Width mappings.
     *
     * @var array
     */
    protected static $widthMap = [
        Border::WIDTH_THIN => '0.75pt',
        Border::WIDTH_MEDIUM => '1.75pt',
        Border::WIDTH_THICK => '2.5pt',
    ];

    /**
     * Style mapping.
     *
     * @var array
     */
    protected static $styleMap = [
        Border::STYLE_SOLID => 'solid',
        Border::STYLE_DASHED => 'dashed',
        Border::STYLE_DOTTED => 'dotted',
        Border::STYLE_DOUBLE => 'double',
    ];

    /**
     * @return string
     */
    public static function serializeBorderPart(BorderPart $borderPart)
    {
        $definition = 'fo:border-%s="%s"';

        if (Border::STYLE_NONE === $borderPart->getStyle()) {
            $borderPartDefinition = sprintf($definition, $borderPart->getName(), 'none');
        } else {
            $attributes = [
                self::$widthMap[$borderPart->getWidth()],
                self::$styleMap[$borderPart->getStyle()],
                '#'.$borderPart->getColor(),
            ];
            $borderPartDefinition = sprintf($definition, $borderPart->getName(), implode(' ', $attributes));
        }

        return $borderPartDefinition;
    }
}
