<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX;

final readonly class Properties
{
    public function __construct(
        public ?string $title = 'Untitled Spreadsheet',
        public ?string $subject = null,
        public ?string $application = 'OpenSpout',
        public ?string $creator = 'OpenSpout',
        public ?string $lastModifiedBy = 'OpenSpout',
        public ?string $keywords = null,
        public ?string $description = null,
        public ?string $category = null,
        public ?string $language = null,
        /** @var array<string, string> $customProperties */
        public array $customProperties = [],
    ) {}
}
