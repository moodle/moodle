<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class ErrorCell extends Cell
{
    private readonly string $value;

    public function __construct(string $value, ?Style $style)
    {
        $this->value = $value;
        parent::__construct($style);
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getRawValue(): string
    {
        return $this->value;
    }
}
