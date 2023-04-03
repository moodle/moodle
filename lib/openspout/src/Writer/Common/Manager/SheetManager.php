<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Exception\InvalidSheetNameException;

/**
 * @internal
 */
final class SheetManager
{
    /**
     * Sheet name should not exceed 31 characters.
     */
    public const MAX_LENGTH_SHEET_NAME = 31;

    /**
     * Invalid characters that cannot be contained in the sheet name.
     */
    private const INVALID_CHARACTERS_IN_SHEET_NAME = ['\\', '/', '?', '*', ':', '[', ']'];

    /** @var array<string, array<int, string>> Associative array [WORKBOOK_ID] => [[SHEET_INDEX] => [SHEET_NAME]] keeping track of sheets' name to enforce uniqueness per workbook */
    private static array $SHEETS_NAME_USED = [];

    private StringHelper $stringHelper;

    /**
     * SheetManager constructor.
     */
    public function __construct(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    /**
     * Throws an exception if the given sheet's name is not valid.
     *
     * @see Sheet::setName for validity rules.
     *
     * @param Sheet $sheet The sheet whose future name is checked
     *
     * @throws \OpenSpout\Writer\Exception\InvalidSheetNameException if the sheet's name is invalid
     */
    public function throwIfNameIsInvalid(string $name, Sheet $sheet): void
    {
        $failedRequirements = [];
        $nameLength = $this->stringHelper->getStringLength($name);

        if (!$this->isNameUnique($name, $sheet)) {
            $failedRequirements[] = 'It should be unique';
        } elseif (0 === $nameLength) {
            $failedRequirements[] = 'It should not be blank';
        } else {
            if ($nameLength > self::MAX_LENGTH_SHEET_NAME) {
                $failedRequirements[] = 'It should not exceed 31 characters';
            }

            if ($this->doesContainInvalidCharacters($name)) {
                $failedRequirements[] = 'It should not contain these characters: \\ / ? * : [ or ]';
            }

            if ($this->doesStartOrEndWithSingleQuote($name)) {
                $failedRequirements[] = 'It should not start or end with a single quote';
            }
        }

        if (0 !== \count($failedRequirements)) {
            $errorMessage = "The sheet's name (\"{$name}\") is invalid. It did not respect these rules:\n - ";
            $errorMessage .= implode("\n - ", $failedRequirements);

            throw new InvalidSheetNameException($errorMessage);
        }
    }

    /**
     * @param string $workbookId Workbook ID associated to a Sheet
     */
    public function markWorkbookIdAsUsed(string $workbookId): void
    {
        if (!isset(self::$SHEETS_NAME_USED[$workbookId])) {
            self::$SHEETS_NAME_USED[$workbookId] = [];
        }
    }

    public function markSheetNameAsUsed(Sheet $sheet): void
    {
        self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()][$sheet->getIndex()] = $sheet->getName();
    }

    /**
     * Returns whether the given name contains at least one invalid character.
     *
     * @return bool TRUE if the name contains invalid characters, FALSE otherwise
     */
    private function doesContainInvalidCharacters(string $name): bool
    {
        return str_replace(self::INVALID_CHARACTERS_IN_SHEET_NAME, '', $name) !== $name;
    }

    /**
     * Returns whether the given name starts or ends with a single quote.
     *
     * @return bool TRUE if the name starts or ends with a single quote, FALSE otherwise
     */
    private function doesStartOrEndWithSingleQuote(string $name): bool
    {
        $startsWithSingleQuote = (0 === $this->stringHelper->getCharFirstOccurrencePosition('\'', $name));
        $endsWithSingleQuote = ($this->stringHelper->getCharLastOccurrencePosition('\'', $name) === ($this->stringHelper->getStringLength($name) - 1));

        return $startsWithSingleQuote || $endsWithSingleQuote;
    }

    /**
     * Returns whether the given name is unique.
     *
     * @param Sheet $sheet The sheet whose future name is checked
     *
     * @return bool TRUE if the name is unique, FALSE otherwise
     */
    private function isNameUnique(string $name, Sheet $sheet): bool
    {
        foreach (self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()] as $sheetIndex => $sheetName) {
            if ($sheetIndex !== $sheet->getIndex() && $sheetName === $name) {
                return false;
            }
        }

        return true;
    }
}
