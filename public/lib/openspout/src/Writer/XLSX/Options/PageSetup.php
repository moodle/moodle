<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

final readonly class PageSetup
{
    public bool $fitToPage;

    public function __construct(
        public ?PageOrientation $pageOrientation,
        public ?PaperSize $paperSize,
        public ?int $fitToHeight = null,
        public ?int $fitToWidth = null,
    ) {
        $this->fitToPage = null !== $fitToHeight || null !== $fitToWidth;
    }
}
