<?php

namespace OpenSpout\Common\Entity\Style;

/**
 * This class provides constants to work with text alignment.
 */
abstract class CellAlignment
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const CENTER = 'center';
    public const JUSTIFY = 'justify';

    private static $VALID_ALIGNMENTS = [
        self::LEFT => 1,
        self::RIGHT => 1,
        self::CENTER => 1,
        self::JUSTIFY => 1,
    ];

    /**
     * @param string $cellAlignment
     *
     * @return bool Whether the given cell alignment is valid
     */
    public static function isValid($cellAlignment)
    {
        return isset(self::$VALID_ALIGNMENTS[$cellAlignment]);
    }
}
