<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Style\Style;

/**
 * Allow to know if this style must replace actual row style.
 *
 * @internal
 */
final class RegisteredStyle
{
    private readonly Style $style;

    private readonly bool $isMatchingRowStyle;

    public function __construct(Style $style, bool $isMatchingRowStyle)
    {
        $this->style = $style;
        $this->isMatchingRowStyle = $isMatchingRowStyle;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function isMatchingRowStyle(): bool
    {
        return $this->isMatchingRowStyle;
    }
}
