<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Cell;

use DateInterval;
use DateTimeImmutable;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;

final class FormulaCell extends Cell
{
    public function __construct(
        private readonly string $value,
        ?Style $style,
        private readonly null|bool|DateInterval|DateTimeImmutable|float|int|string $computedValue = null,
    ) {
        parent::__construct($style);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getComputedValue(): null|bool|DateInterval|DateTimeImmutable|float|int|string
    {
        return $this->computedValue;
    }
}
