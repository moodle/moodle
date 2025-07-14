<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class NumericCell extends Cell
{
    private readonly float|int $value;

    public function __construct(float|int $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): float|int
    {
        return $this->value;
    }
}
