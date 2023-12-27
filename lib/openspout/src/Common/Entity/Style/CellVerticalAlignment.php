<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

/**
 * This class provides constants to work with text vertical alignment.
 */
final class CellVerticalAlignment
{
    public const AUTO = 'auto';
    public const BASELINE = 'baseline';
    public const BOTTOM = 'bottom';
    public const CENTER = 'center';
    public const DISTRIBUTED = 'distributed';
    public const JUSTIFY = 'justify';
    public const TOP = 'top';

    private const VALID_ALIGNMENTS = [
        self::AUTO => 1,
        self::BASELINE => 1,
        self::BOTTOM => 1,
        self::CENTER => 1,
        self::DISTRIBUTED => 1,
        self::JUSTIFY => 1,
        self::TOP => 1,
    ];

    /**
     * @return bool Whether the given cell vertical alignment is valid
     */
    public static function isValid(string $cellVerticalAlignment): bool
    {
        return isset(self::VALID_ALIGNMENTS[$cellVerticalAlignment]);
    }
}
