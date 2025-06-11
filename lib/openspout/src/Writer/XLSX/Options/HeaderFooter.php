<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final class HeaderFooter
{
    public function __construct(
        public readonly ?string $oddHeader = null,
        public readonly ?string $oddFooter = null,
        public readonly ?string $evenHeader = null,
        public readonly ?string $evenFooter = null,
        public readonly bool $differentOddEven = false,
    ) {}
}
