<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class BooleanCell extends Cell
{
    private readonly bool $value;

    public function __construct(bool $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
