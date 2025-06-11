<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;

/**
 * @internal
 */
final class PossiblyUpdatedStyle
{
    private readonly Style $style;
    private readonly bool $isUpdated;

    public function __construct(Style $style, bool $isUpdated)
    {
        $this->style = $style;
        $this->isUpdated = $isUpdated;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function isUpdated(): bool
    {
        return $this->isUpdated;
    }
}
