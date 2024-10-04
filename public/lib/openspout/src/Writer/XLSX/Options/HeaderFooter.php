<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final readonly class HeaderFooter
{
    public function __construct(
        public ?string $oddHeader = null,
        public ?string $oddFooter = null,
        public ?string $evenHeader = null,
        public ?string $evenFooter = null,
        public bool $differentOddEven = false,
    ) {}
}
