<?php

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Exception\InvalidSheetNameException;

/**
 * Sheet manager.
 */
class SheetManager
{
    /** Sheet name should not exceed 31 characters */
    public const MAX_LENGTH_SHEET_NAME = 31;

    /** @var array Invalid characters that cannot be contained in the sheet name */
    private static $INVALID_CHARACTERS_IN_SHEET_NAME = ['\\', '/', '?', '*', ':', '[', ']'];

    /** @var array Associative array [WORKBOOK_ID] => [[SHEET_INDEX] => [SHEET_NAME]] keeping track of sheets' name to enforce uniqueness per workbook */
    private static $SHEETS_NAME_USED = [];

    /** @var StringHelper */
    private $stringHelper;

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
     * @param string $name
     * @param Sheet  $sheet The sheet whose future name is checked
     *
     * @throws \OpenSpout\Writer\Exception\InvalidSheetNameException if the sheet's name is invalid
     */
    public function throwIfNameIsInvalid($name, Sheet $sheet)
    {
        if (!\is_string($name)) {
            $actualType = \gettype($name);
            $errorMessage = "The sheet's name is invalid. It must be a string ({$actualType} given).";

            throw new InvalidSheetNameException($errorMessage);
        }

        $failedRequirements = [];
        $nameLength = $this->stringHelper->getStringLength($name);

        if (!$this->isNameUnique($name, $sheet)) {
            $failedRequirements[] = 'It should be unique';
        } else {
            if (0 === $nameLength) {
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
    public function markWorkbookIdAsUsed($workbookId)
    {
        if (!isset(self::$SHEETS_NAME_USED[$workbookId])) {
            self::$SHEETS_NAME_USED[$workbookId] = [];
        }
    }

    public function markSheetNameAsUsed(Sheet $sheet)
    {
        self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()][$sheet->getIndex()] = $sheet->getName();
    }

    /**
     * Returns whether the given name contains at least one invalid character.
     *
     * @see Sheet::$INVALID_CHARACTERS_IN_SHEET_NAME for the full list.
     *
     * @param string $name
     *
     * @return bool TRUE if the name contains invalid characters, FALSE otherwise
     */
    private function doesContainInvalidCharacters($name)
    {
        return str_replace(self::$INVALID_CHARACTERS_IN_SHEET_NAME, '', $name) !== $name;
    }

    /**
     * Returns whether the given name starts or ends with a single quote.
     *
     * @param string $name
     *
     * @return bool TRUE if the name starts or ends with a single quote, FALSE otherwise
     */
    private function doesStartOrEndWithSingleQuote($name)
    {
        $startsWithSingleQuote = (0 === $this->stringHelper->getCharFirstOccurrencePosition('\'', $name));
        $endsWithSingleQuote = ($this->stringHelper->getCharLastOccurrencePosition('\'', $name) === ($this->stringHelper->getStringLength($name) - 1));

        return $startsWithSingleQuote || $endsWithSingleQuote;
    }

    /**
     * Returns whether the given name is unique.
     *
     * @param string $name
     * @param Sheet  $sheet The sheet whose future name is checked
     *
     * @return bool TRUE if the name is unique, FALSE otherwise
     */
    private function isNameUnique($name, Sheet $sheet)
    {
        foreach (self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()] as $sheetIndex => $sheetName) {
            if ($sheetIndex !== $sheet->getIndex() && $sheetName === $name) {
                return false;
            }
        }

        return true;
    }
}
