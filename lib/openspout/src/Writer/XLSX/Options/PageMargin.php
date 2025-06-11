<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final class PageMargin
{
    public function __construct(
        public readonly float $top = 0.75,
        public readonly float $right = 0.7,
        public readonly float $bottom = 0.75,
        public readonly float $left = 0.7,
        public readonly float $header = 0.3,
        public readonly float $footer = 0.3
    ) {}
}
