<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use DateTimeInterface;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class DateTimeCell extends Cell
{
    private DateTimeInterface $value;

    public function __construct(DateTimeInterface $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): DateTimeInterface
    {
        return $this->value;
    }
}
