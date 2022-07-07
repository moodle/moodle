<?php

namespace Box\Spout\Common\Entity\Style;

use Box\Spout\Common\Exception\InvalidColorException;

/**
 * Class Color
 * This class provides constants and functions to work with colors
 */
class Color
{
    /** Standard colors - based on Office Online */
    const BLACK = '000000';
    const WHITE = 'FFFFFF';
    const RED = 'FF0000';
    const DARK_RED = 'C00000';
    const ORANGE = 'FFC000';
    const YELLOW = 'FFFF00';
    const LIGHT_GREEN = '92D040';
    const GREEN = '00B050';
    const LIGHT_BLUE = '00B0E0';
    const BLUE = '0070C0';
    const DARK_BLUE = '002060';
    const PURPLE = '7030A0';

    /**
     * Returns an RGB color from R, G and B values
     *
     * @param int $red Red component, 0 - 255
     * @param int $green Green component, 0 - 255
     * @param int $blue Blue component, 0 - 255
     * @return string RGB color
     */
    public static function rgb($red, $green, $blue)
    {
        self::throwIfInvalidColorComponentValue($red);
        self::throwIfInvalidColorComponentValue($green);
        self::throwIfInvalidColorComponentValue($blue);

        return strtoupper(
            self::convertColorComponentToHex($red) .
            self::convertColorComponentToHex($green) .
            self::convertColorComponentToHex($blue)
        );
    }

    /**
     * Throws an exception is the color component value is outside of bounds (0 - 255)
     *
     * @param int $colorComponent
     * @throws \Box\Spout\Common\Exception\InvalidColorException
     * @return void
     */
    protected static function throwIfInvalidColorComponentValue($colorComponent)
    {
        if (!is_int($colorComponent) || $colorComponent < 0 || $colorComponent > 255) {
            throw new InvalidColorException("The RGB components must be between 0 and 255. Received: $colorComponent");
        }
    }

    /**
     * Converts the color component to its corresponding hexadecimal value
     *
     * @param int $colorComponent Color component, 0 - 255
     * @return string Corresponding hexadecimal value, with a leading 0 if needed. E.g "0f", "2d"
     */
    protected static function convertColorComponentToHex($colorComponent)
    {
        return str_pad(dechex($colorComponent), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the ARGB color of the given RGB color,
     * assuming that alpha value is always 1.
     *
     * @param string $rgbColor RGB color like "FF08B2"
     * @return string ARGB color
     */
    public static function toARGB($rgbColor)
    {
        return 'FF' . $rgbColor;
    }
}
