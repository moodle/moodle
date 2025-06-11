<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use DateInterval;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class DateIntervalCell extends Cell
{
    private readonly DateInterval $value;

    /**
     * For Excel make sure to set a format onto the style (Style::setFormat()) with the left most unit enclosed with
     *   brackets: '[h]:mm', '[hh]:mm:ss', '[m]:ss', '[s]', etc.
     * This makes sure excel knows what to do with the remaining time that exceeds this unit. Without brackets Excel
     *   will interpret the value as date time and not duration if it is greater or equal 1.
     */
    public function __construct(DateInterval $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): DateInterval
    {
        return $this->value;
    }
}
