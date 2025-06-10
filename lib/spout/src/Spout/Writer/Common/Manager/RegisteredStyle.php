<?php

namespace Box\Spout\Writer\Common\Manager;

use Box\Spout\Common\Entity\Style\Style;

/**
 * Class RegisteredStyle
 * Allow to know if this style must replace actual row style.
 */
class RegisteredStyle
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var bool
     */
    private $isMatchingRowStyle;

    public function __construct(Style $style, bool $isMatchingRowStyle)
    {
        $this->style = $style;
        $this->isMatchingRowStyle = $isMatchingRowStyle;
    }

    public function getStyle() : Style
    {
        return $this->style;
    }

    public function isMatchingRowStyle() : bool
    {
        return $this->isMatchingRowStyle;
    }
}
