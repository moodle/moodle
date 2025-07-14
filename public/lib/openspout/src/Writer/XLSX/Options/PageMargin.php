<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final readonly class PageMargin
{
    public function __construct(
        public float $top = 0.75,
        public float $right = 0.7,
        public float $bottom = 0.75,
        public float $left = 0.7,
        public float $header = 0.3,
        public float $footer = 0.3
    ) {}
}
