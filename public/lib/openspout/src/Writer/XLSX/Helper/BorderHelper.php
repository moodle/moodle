<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;

/**
 * @internal
 */
final class BorderHelper
{
    private const xlsxStyleMap = [
        Border::STYLE_SOLID => [
            Border::WIDTH_THIN => 'thin',
            Border::WIDTH_MEDIUM => 'medium',
            Border::WIDTH_THICK => 'thick',
        ],
        Border::STYLE_DOTTED => [
            Border::WIDTH_THIN => 'dotted',
            Border::WIDTH_MEDIUM => 'dotted',
            Border::WIDTH_THICK => 'dotted',
        ],
        Border::STYLE_DASHED => [
            Border::WIDTH_THIN => 'dashed',
            Border::WIDTH_MEDIUM => 'mediumDashed',
            Border::WIDTH_THICK => 'mediumDashed',
        ],
        Border::STYLE_DOUBLE => [
            Border::WIDTH_THIN => 'double',
            Border::WIDTH_MEDIUM => 'double',
            Border::WIDTH_THICK => 'double',
        ],
        Border::STYLE_NONE => [
            Border::WIDTH_THIN => 'none',
            Border::WIDTH_MEDIUM => 'none',
            Border::WIDTH_THICK => 'none',
        ],
    ];

    public static function serializeBorderPart(?BorderPart $borderPart): string
    {
        if (null === $borderPart) {
            return '';
        }

        $borderStyle = self::getBorderStyle($borderPart);

        $colorEl = \sprintf('<color rgb="%s"/>', $borderPart->getColor());
        $partEl = \sprintf(
            '<%s style="%s">%s</%s>',
            $borderPart->getName(),
            $borderStyle,
            $colorEl,
            $borderPart->getName()
        );

        return $partEl.PHP_EOL;
    }

    /**
     * Get the style definition from the style map.
     */
    private static function getBorderStyle(BorderPart $borderPart): string
    {
        return self::xlsxStyleMap[$borderPart->getStyle()][$borderPart->getWidth()];
    }
}
