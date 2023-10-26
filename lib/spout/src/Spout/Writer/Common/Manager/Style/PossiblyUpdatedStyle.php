<?php

namespace Box\Spout\Writer\Common\Manager\Style;

use Box\Spout\Common\Entity\Style\Style;

/**
 * Class PossiblyUpdatedStyle
 * Indicates if style is updated.
 * It allow to know if style registration must be done.
 */
class PossiblyUpdatedStyle
{
    private $style;
    private $isUpdated;

    public function __construct(Style $style, bool $isUpdated)
    {
        $this->style = $style;
        $this->isUpdated = $isUpdated;
    }

    public function getStyle() : Style
    {
        return $this->style;
    }

    public function isUpdated() : bool
    {
        return $this->isUpdated;
    }
}
