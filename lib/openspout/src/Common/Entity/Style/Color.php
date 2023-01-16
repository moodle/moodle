<?php

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Common\Exception\InvalidColorException;

/**
 * This class provides constants and functions to work with colors.
 */
abstract class Color
{
    /** Standard colors - based on Office Online */
    public const BLACK = '000000';
    public const WHITE = 'FFFFFF';
    public const RED = 'FF0000';
    public const DARK_RED = 'C00000';
    public const ORANGE = 'FFC000';
    public const YELLOW = 'FFFF00';
    public const LIGHT_GREEN = '92D040';
    public const GREEN = '00B050';
    public const LIGHT_BLUE = '00B0E0';
    public const BLUE = '0070C0';
    public const DARK_BLUE = '002060';
    public const PURPLE = '7030A0';

    /**
     * Returns an RGB color from R, G and B values.
     *
     * @param int $red   Red component, 0 - 255
     * @param int $green Green component, 0 - 255
     * @param int $blue  Blue component, 0 - 255
     *
     * @return string RGB color
     */
    public static function rgb($red, $green, $blue)
    {
        self::throwIfInvalidColorComponentValue($red);
        self::throwIfInvalidColorComponentValue($green);
        self::throwIfInvalidColorComponentValue($blue);

        return strtoupper(
            self::convertColorComponentToHex($red).
            self::convertColorComponentToHex($green).
            self::convertColorComponentToHex($blue)
        );
    }

    /**
     * Returns the ARGB color of the given RGB color,
     * assuming that alpha value is always 1.
     *
     * @param string $rgbColor RGB color like "FF08B2"
     *
     * @return string ARGB color
     */
    public static function toARGB($rgbColor)
    {
        return 'FF'.$rgbColor;
    }

    /**
     * Throws an exception is the color component value is outside of bounds (0 - 255).
     *
     * @param int $colorComponent
     *
     * @throws \OpenSpout\Common\Exception\InvalidColorException
     */
    protected static function throwIfInvalidColorComponentValue($colorComponent)
    {
        if (!\is_int($colorComponent) || $colorComponent < 0 || $colorComponent > 255) {
            throw new InvalidColorException("The RGB components must be between 0 and 255. Received: {$colorComponent}");
        }
    }

    /**
     * Converts the color component to its corresponding hexadecimal value.
     *
     * @param int $colorComponent Color component, 0 - 255
     *
     * @return string Corresponding hexadecimal value, with a leading 0 if needed. E.g "0f", "2d"
     */
    protected static function convertColorComponentToHex($colorComponent)
    {
        return str_pad(dechex($colorComponent), 2, '0', STR_PAD_LEFT);
    }
}
