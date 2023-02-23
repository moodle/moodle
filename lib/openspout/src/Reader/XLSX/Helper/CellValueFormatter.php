<?php

namespace OpenSpout\Reader\XLSX\Helper;

use OpenSpout\Reader\Exception\InvalidValueException;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\StyleManager;

/**
 * This class provides helper functions to format cell values.
 */
class CellValueFormatter
{
    /** Definition of all possible cell types */
    public const CELL_TYPE_INLINE_STRING = 'inlineStr';
    public const CELL_TYPE_STR = 'str';
    public const CELL_TYPE_SHARED_STRING = 's';
    public const CELL_TYPE_BOOLEAN = 'b';
    public const CELL_TYPE_NUMERIC = 'n';
    public const CELL_TYPE_DATE = 'd';
    public const CELL_TYPE_ERROR = 'e';

    /** Definition of XML nodes names used to parse data */
    public const XML_NODE_VALUE = 'v';
    public const XML_NODE_INLINE_STRING_VALUE = 't';

    /** Definition of XML attributes used to parse data */
    public const XML_ATTRIBUTE_TYPE = 't';
    public const XML_ATTRIBUTE_STYLE_ID = 's';

    /** Constants used for date formatting */
    public const NUM_SECONDS_IN_ONE_DAY = 86400;

    /** @var SharedStringsManager Manages shared strings */
    protected $sharedStringsManager;

    /** @var StyleManager Manages styles */
    protected $styleManager;

    /** @var bool Whether date/time values should be returned as PHP objects or be formatted as strings */
    protected $shouldFormatDates;

    /** @var bool Whether date/time values should use a calendar starting in 1904 instead of 1900 */
    protected $shouldUse1904Dates;

    /** @var \OpenSpout\Common\Helper\Escaper\XLSX Used to unescape XML data */
    protected $escaper;

    /**
     * @param SharedStringsManager                  $sharedStringsManager Manages shared strings
     * @param StyleManager                          $styleManager         Manages styles
     * @param bool                                  $shouldFormatDates    Whether date/time values should be returned as PHP objects or be formatted as strings
     * @param bool                                  $shouldUse1904Dates   Whether date/time values should use a calendar starting in 1904 instead of 1900
     * @param \OpenSpout\Common\Helper\Escaper\XLSX $escaper              Used to unescape XML data
     */
    public function __construct($sharedStringsManager, $styleManager, $shouldFormatDates, $shouldUse1904Dates, $escaper)
    {
        $this->sharedStringsManager = $sharedStringsManager;
        $this->styleManager = $styleManager;
        $this->shouldFormatDates = $shouldFormatDates;
        $this->shouldUse1904Dates = $shouldUse1904Dates;
        $this->escaper = $escaper;
    }

    /**
     * Returns the (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @param \DOMElement $node
     *
     * @throws InvalidValueException If the value is not valid
     *
     * @return bool|\DateTime|float|int|string The value associated with the cell
     */
    public function extractAndFormatNodeValue($node)
    {
        // Default cell type is "n"
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE) ?: self::CELL_TYPE_NUMERIC;
        $cellStyleId = (int) $node->getAttribute(self::XML_ATTRIBUTE_STYLE_ID);
        $vNodeValue = $this->getVNodeValue($node);

        if (('' === $vNodeValue) && (self::CELL_TYPE_INLINE_STRING !== $cellType)) {
            return $vNodeValue;
        }

        switch ($cellType) {
            case self::CELL_TYPE_INLINE_STRING:
                return $this->formatInlineStringCellValue($node);

            case self::CELL_TYPE_SHARED_STRING:
                return $this->formatSharedStringCellValue($vNodeValue);

            case self::CELL_TYPE_STR:
                return $this->formatStrCellValue($vNodeValue);

            case self::CELL_TYPE_BOOLEAN:
                return $this->formatBooleanCellValue($vNodeValue);

            case self::CELL_TYPE_NUMERIC:
                return $this->formatNumericCellValue($vNodeValue, $cellStyleId);

            case self::CELL_TYPE_DATE:
                return $this->formatDateCellValue($vNodeValue);

            default:
                throw new InvalidValueException($vNodeValue);
        }
    }

    /**
     * Returns the cell's string value from a node's nested value node.
     *
     * @param \DOMElement $node
     *
     * @return string The value associated with the cell
     */
    protected function getVNodeValue($node)
    {
        // for cell types having a "v" tag containing the value.
        // if not, the returned value should be empty string.
        $vNode = $node->getElementsByTagName(self::XML_NODE_VALUE)->item(0);

        return (null !== $vNode) ? $vNode->nodeValue : '';
    }

    /**
     * Returns the cell String value where string is inline.
     *
     * @param \DOMElement $node
     *
     * @return string The value associated with the cell
     */
    protected function formatInlineStringCellValue($node)
    {
        // inline strings are formatted this way (they can contain any number of <t> nodes):
        // <c r="A1" t="inlineStr"><is><t>[INLINE_STRING]</t><t>[INLINE_STRING_2]</t></is></c>
        $tNodes = $node->getElementsByTagName(self::XML_NODE_INLINE_STRING_VALUE);

        $cellValue = '';
        for ($i = 0; $i < $tNodes->count(); ++$i) {
            $tNode = $tNodes->item($i);
            $cellValue .= $this->escaper->unescape($tNode->nodeValue);
        }

        return $cellValue;
    }

    /**
     * Returns the cell String value from shared-strings file using nodeValue index.
     *
     * @param string $nodeValue
     *
     * @return string The value associated with the cell
     */
    protected function formatSharedStringCellValue($nodeValue)
    {
        // shared strings are formatted this way:
        // <c r="A1" t="s"><v>[SHARED_STRING_INDEX]</v></c>
        $sharedStringIndex = (int) $nodeValue;
        $escapedCellValue = $this->sharedStringsManager->getStringAtIndex($sharedStringIndex);

        return $this->escaper->unescape($escapedCellValue);
    }

    /**
     * Returns the cell String value, where string is stored in value node.
     *
     * @param string $nodeValue
     *
     * @return string The value associated with the cell
     */
    protected function formatStrCellValue($nodeValue)
    {
        $escapedCellValue = trim($nodeValue);

        return $this->escaper->unescape($escapedCellValue);
    }

    /**
     * Returns the cell Numeric value from string of nodeValue.
     * The value can also represent a timestamp and a DateTime will be returned.
     *
     * @param string $nodeValue
     * @param int    $cellStyleId 0 being the default style
     *
     * @return \DateTime|float|int The value associated with the cell
     */
    protected function formatNumericCellValue($nodeValue, $cellStyleId)
    {
        // Numeric values can represent numbers as well as timestamps.
        // We need to look at the style of the cell to determine whether it is one or the other.
        $shouldFormatAsDate = $this->styleManager->shouldFormatNumericValueAsDate($cellStyleId);

        if ($shouldFormatAsDate) {
            $cellValue = $this->formatExcelTimestampValue((float) $nodeValue, $cellStyleId);
        } else {
            $nodeIntValue = (int) $nodeValue;
            $nodeFloatValue = (float) $nodeValue;
            $cellValue = ((float) $nodeIntValue === $nodeFloatValue) ? $nodeIntValue : $nodeFloatValue;
        }

        return $cellValue;
    }

    /**
     * Returns a cell's PHP Date value, associated to the given timestamp.
     * NOTE: The timestamp is a float representing the number of days since the base Excel date:
     *       Dec 30th 1899, 1900 or Jan 1st, 1904, depending on the Workbook setting.
     * NOTE: The timestamp can also represent a time, if it is a value between 0 and 1.
     *
     * @see ECMA-376 Part 1 - §18.17.4
     *
     * @param float $nodeValue
     * @param int   $cellStyleId 0 being the default style
     *
     * @throws InvalidValueException If the value is not a valid timestamp
     *
     * @return \DateTime The value associated with the cell
     */
    protected function formatExcelTimestampValue($nodeValue, $cellStyleId)
    {
        if ($this->isValidTimestampValue($nodeValue)) {
            $cellValue = $this->formatExcelTimestampValueAsDateTimeValue($nodeValue, $cellStyleId);
        } else {
            throw new InvalidValueException($nodeValue);
        }

        return $cellValue;
    }

    /**
     * Returns whether the given timestamp is supported by SpreadsheetML.
     *
     * @see ECMA-376 Part 1 - §18.17.4 - this specifies the timestamp boundaries.
     *
     * @param float $timestampValue
     *
     * @return bool
     */
    protected function isValidTimestampValue($timestampValue)
    {
        // @NOTE: some versions of Excel don't support negative dates (e.g. Excel for Mac 2011)
        return
            $this->shouldUse1904Dates && $timestampValue >= -695055 && $timestampValue <= 2957003.9999884
            || !$this->shouldUse1904Dates && $timestampValue >= -693593 && $timestampValue <= 2958465.9999884
        ;
    }

    /**
     * Returns a cell's PHP DateTime value, associated to the given timestamp.
     * Only the time value matters. The date part is set to the base Excel date:
     * Dec 30th 1899, 1900 or Jan 1st, 1904, depending on the Workbook setting.
     *
     * @param float $nodeValue
     * @param int   $cellStyleId 0 being the default style
     *
     * @return \DateTime|string The value associated with the cell
     */
    protected function formatExcelTimestampValueAsDateTimeValue($nodeValue, $cellStyleId)
    {
        $baseDate = $this->shouldUse1904Dates ? '1904-01-01' : '1899-12-30';

        $daysSinceBaseDate = (int) $nodeValue;
        $timeRemainder = fmod($nodeValue, 1);
        $secondsRemainder = round($timeRemainder * self::NUM_SECONDS_IN_ONE_DAY, 0);

        $dateObj = \DateTime::createFromFormat('|Y-m-d', $baseDate);
        $dateObj->modify('+'.$daysSinceBaseDate.'days');
        $dateObj->modify('+'.$secondsRemainder.'seconds');

        if ($this->shouldFormatDates) {
            $styleNumberFormatCode = $this->styleManager->getNumberFormatCode($cellStyleId);
            $phpDateFormat = DateFormatHelper::toPHPDateFormat($styleNumberFormatCode);
            $cellValue = $dateObj->format($phpDateFormat);
        } else {
            $cellValue = $dateObj;
        }

        return $cellValue;
    }

    /**
     * Returns the cell Boolean value from a specific node's Value.
     *
     * @param string $nodeValue
     *
     * @return bool The value associated with the cell
     */
    protected function formatBooleanCellValue($nodeValue)
    {
        return (bool) $nodeValue;
    }

    /**
     * Returns a cell's PHP Date value, associated to the given stored nodeValue.
     *
     * @see ECMA-376 Part 1 - §18.17.4
     *
     * @param string $nodeValue ISO 8601 Date string
     *
     * @throws InvalidValueException If the value is not a valid date
     *
     * @return \DateTime|string The value associated with the cell
     */
    protected function formatDateCellValue($nodeValue)
    {
        // Mitigate thrown Exception on invalid date-time format (http://php.net/manual/en/datetime.construct.php)
        try {
            $cellValue = ($this->shouldFormatDates) ? $nodeValue : new \DateTime($nodeValue);
        } catch (\Exception $e) {
            throw new InvalidValueException($nodeValue);
        }

        return $cellValue;
    }
}
