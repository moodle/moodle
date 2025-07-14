<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

use OpenSpout\Writer\XLSX\Helper\PasswordHashHelper;

final readonly class WorkbookProtection
{
    public function __construct(
        public ?string $password = null,
        public bool $lockStructure = false,
        public bool $lockWindows = false,
        public bool $lockRevisions = false,
    ) {}

    public function getXml(): string
    {
        return '<workbookProtection'.$this->getSheetViewAttributes().'/>';
    }

    private function getSheetViewAttributes(): string
    {
        return $this->generateAttributes([
            'workbookPassword' => null !== $this->password ? PasswordHashHelper::make($this->password) : '',
            'lockStructure' => $this->lockStructure,
            'lockWindows' => $this->lockWindows,
            'lockRevisions' => $this->lockRevisions,
        ]);
    }

    /**
     * @param array<string, bool|string> $data with key containing the attribute name and value containing the attribute value
     */
    private function generateAttributes(array $data): string
    {
        // Create attribute for each key
        $attributes = array_map(static function (string $key, bool|string $value): string {
            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return $key.'="'.$value.'"';
        }, array_keys($data), $data);

        // Append all attributes
        return ' '.implode(' ', $attributes);
    }
}
