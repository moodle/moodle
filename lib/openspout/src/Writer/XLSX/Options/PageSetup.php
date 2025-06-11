<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final class PageSetup
{
    public readonly bool $fitToPage;

    public function __construct(
        public readonly ?PageOrientation $pageOrientation,
        public readonly ?PaperSize $paperSize,
        public readonly ?int $fitToHeight = null,
        public readonly ?int $fitToWidth = null,
    ) {
        $this->fitToPage = null !== $fitToHeight || null !== $fitToWidth;
    }
}
