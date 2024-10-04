<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

use OpenSpout\Writer\XLSX\Helper\PasswordHashHelper;

final readonly class SheetProtection
{
    public function __construct(
        public ?string $password = null,
        public bool $lockSheet = false,
        public bool $lockColumnInsert = false,
        public bool $lockColumnDelete = false,
        public bool $lockColumnFormatting = false,
        public bool $lockRowInsert = false,
        public bool $lockRowDelete = false,
        public bool $lockRowFormatting = false,
        public bool $lockAutoFilter = false,
        public bool $lockSort = false,
        public bool $lockCellFormatting = false,
        public bool $lockLockedCellSelection = false,
        public bool $lockUnlockedCellsSelection = false,
        public bool $lockObjects = false,
        public bool $lockHyperlinkInsert = false,
        public bool $lockPivotTables = false,
        public bool $lockScenarios = false,
    ) {}

    public function getXml(): string
    {
        return '<sheetProtection'.$this->getSheetViewAttributes().'/>';
    }

    private function getSheetViewAttributes(): string
    {
        return $this->generateAttributes([
            'password' => null !== $this->password ? PasswordHashHelper::make($this->password) : '',
            'sheet' => $this->lockSheet,
            'objects' => $this->lockObjects,
            'scenarios' => $this->lockScenarios,
            'formatCells' => $this->lockCellFormatting,
            'formatColumns' => $this->lockColumnFormatting,
            'formatRows' => $this->lockRowFormatting,
            'insertColumns' => $this->lockColumnInsert,
            'insertRows' => $this->lockRowInsert,
            'deleteColumns' => $this->lockColumnDelete,
            'deleteRows' => $this->lockRowDelete,
            'selectLockedCells' => $this->lockLockedCellSelection,
            'selectUnlockedCells' => $this->lockUnlockedCellsSelection,
            'autoFilter' => $this->lockAutoFilter,
            'sort' => $this->lockSort,
            'hyperlink' => $this->lockHyperlinkInsert,
            'pivotTables' => $this->lockPivotTables,
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
