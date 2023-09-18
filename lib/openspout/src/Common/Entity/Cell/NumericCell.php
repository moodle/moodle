<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class NumericCell extends Cell
{
    private int|float $value;

    public function __construct(int|float $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): int|float
    {
        return $this->value;
    }
}
