<?php

namespace Box\Spout\Writer\Common;

use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Writer\Exception\InvalidSheetNameException;

/**
 * Class Sheet
 * External representation of a worksheet within a ODS file
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

    /** @var array Associative array [SHEET_INDEX] => [SHEET_NAME] keeping track of sheets' name to enforce uniqueness */
    protected static $SHEETS_NAME_USED = [];

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    protected $index;

    /** @var string Name of the sheet */
    protected $name;

    /** @var \Box\Spout\Common\Helper\StringHelper */
    protected $stringHelper;

    /**
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     */
    public function __construct($sheetIndex)
    {
        $this->index = $sheetIndex;
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
        if (!$this->isNameValid($name)) {
            $errorMessage = "The sheet's name is invalid. It did not meet at least one of these requirements:\n";
            $errorMessage .= " - It should not be blank\n";
            $errorMessage .= " - It should not exceed 31 characters\n";
            $errorMessage .= " - It should not contain these characters: \\ / ? * : [ or ]\n";
            $errorMessage .= " - It should be unique";
            throw new InvalidSheetNameException($errorMessage);
        }

        $this->name = $name;
        self::$SHEETS_NAME_USED[$this->index] = $name;

        return $this;
    }

    /**
     * Returns whether the given sheet's name is valid.
     * @see Sheet::setName for validity rules.
     *
     * @param string $name
     * @return bool TRUE if the name is valid, FALSE otherwise.
     */
    protected function isNameValid($name)
    {
        if (!is_string($name)) {
            return false;
        }

        $nameLength = $this->stringHelper->getStringLength($name);

        return (
            $nameLength > 0 &&
            $nameLength <= self::MAX_LENGTH_SHEET_NAME &&
            !$this->doesContainInvalidCharacters($name) &&
            $this->isNameUnique($name) &&
            !$this->doesStartOrEndWithSingleQuote($name)
        );
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
        foreach (self::$SHEETS_NAME_USED as $sheetIndex => $sheetName) {
            if ($sheetIndex !== $this->index && $sheetName === $name) {
                return false;
            }
        }

        return true;
    }
}
