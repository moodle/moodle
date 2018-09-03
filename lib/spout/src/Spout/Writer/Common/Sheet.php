<?php

namespace Box\Spout\Writer\Common;

use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Writer\Exception\InvalidSheetNameException;

/**
 * Class Sheet
 * External representation of a worksheet
 *
 * @package Box\Spout\Writer\Common
 */
class Sheet
{
    const DEFAULT_SHEET_NAME_PREFIX = 'Sheet';

    /** Sheet name should not exceed 31 characters */
    const MAX_LENGTH_SHEET_NAME = 31;

    /** @var array Invalid characters that cannot be contained in the sheet name */
    private static $INVALID_CHARACTERS_IN_SHEET_NAME = ['\\', '/', '?', '*', ':', '[', ']'];

    /** @var array Associative array [WORKBOOK_ID] => [[SHEET_INDEX] => [SHEET_NAME]] keeping track of sheets' name to enforce uniqueness per workbook */
    protected static $SHEETS_NAME_USED = [];

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    protected $index;

    /** @var string ID of the sheet's associated workbook. Used to restrict sheet name uniqueness enforcement to a single workbook */
    protected $associatedWorkbookId;

    /** @var string Name of the sheet */
    protected $name;

    /** @var \Box\Spout\Common\Helper\StringHelper */
    protected $stringHelper;

    /**
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $associatedWorkbookId ID of the sheet's associated workbook
     */
    public function __construct($sheetIndex, $associatedWorkbookId)
    {
        $this->index = $sheetIndex;
        $this->associatedWorkbookId = $associatedWorkbookId;
        if (!isset(self::$SHEETS_NAME_USED[$associatedWorkbookId])) {
            self::$SHEETS_NAME_USED[$associatedWorkbookId] = [];
        }

        $this->stringHelper = new StringHelper();
        $this->setName(self::DEFAULT_SHEET_NAME_PREFIX . ($sheetIndex + 1));
    }

    /**
     * @api
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @api
     * @return string Name of the sheet
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the sheet. Note that Excel has some restrictions on the name:
     *  - it should not be blank
     *  - it should not exceed 31 characters
     *  - it should not contain these characters: \ / ? * : [ or ]
     *  - it should be unique
     *
     * @api
     * @param string $name Name of the sheet
     * @return Sheet
     * @throws \Box\Spout\Writer\Exception\InvalidSheetNameException If the sheet's name is invalid.
     */
    public function setName($name)
    {
        $this->throwIfNameIsInvalid($name);

        $this->name = $name;
        self::$SHEETS_NAME_USED[$this->associatedWorkbookId][$this->index] = $name;

        return $this;
    }

    /**
     * Throws an exception if the given sheet's name is not valid.
     * @see Sheet::setName for validity rules.
     *
     * @param string $name
     * @return void
     * @throws \Box\Spout\Writer\Exception\InvalidSheetNameException If the sheet's name is invalid.
     */
    protected function throwIfNameIsInvalid($name)
    {
        if (!is_string($name)) {
            $actualType = gettype($name);
            $errorMessage = "The sheet's name is invalid. It must be a string ($actualType given).";
            throw new InvalidSheetNameException($errorMessage);
        }

        $failedRequirements = [];
        $nameLength = $this->stringHelper->getStringLength($name);

        if (!$this->isNameUnique($name)) {
            $failedRequirements[] = 'It should be unique';
        } else {
            if ($nameLength === 0) {
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

        if (count($failedRequirements) !== 0) {
            $errorMessage = "The sheet's name (\"$name\") is invalid. It did not respect these rules:\n - ";
            $errorMessage .= implode("\n - ", $failedRequirements);
            throw new InvalidSheetNameException($errorMessage);
        }
    }

    /**
     * Returns whether the given name contains at least one invalid character.
     * @see Sheet::$INVALID_CHARACTERS_IN_SHEET_NAME for the full list.
     *
     * @param string $name
     * @return bool TRUE if the name contains invalid characters, FALSE otherwise.
     */
    protected function doesContainInvalidCharacters($name)
    {
        return (str_replace(self::$INVALID_CHARACTERS_IN_SHEET_NAME, '', $name) !== $name);
    }

    /**
     * Returns whether the given name starts or ends with a single quote
     *
     * @param string $name
     * @return bool TRUE if the name starts or ends with a single quote, FALSE otherwise.
     */
    protected function doesStartOrEndWithSingleQuote($name)
    {
        $startsWithSingleQuote = ($this->stringHelper->getCharFirstOccurrencePosition('\'', $name) === 0);
        $endsWithSingleQuote = ($this->stringHelper->getCharLastOccurrencePosition('\'', $name) === ($this->stringHelper->getStringLength($name) - 1));

        return ($startsWithSingleQuote || $endsWithSingleQuote);
    }

    /**
     * Returns whether the given name is unique.
     *
     * @param string $name
     * @return bool TRUE if the name is unique, FALSE otherwise.
     */
    protected function isNameUnique($name)
    {
        foreach (self::$SHEETS_NAME_USED[$this->associatedWorkbookId] as $sheetIndex => $sheetName) {
            if ($sheetIndex !== $this->index && $sheetName === $name) {
                return false;
            }
        }

        return true;
    }
}
