<?php

namespace Box\Spout\Reader\XLSX\Helper;

/**
 * Class CellValueFormatter
 * This class provides helper functions to format cell values
 *
 * @package Box\Spout\Reader\XLSX\Helper
 */
class CellValueFormatter
{
    /** Definition of all possible cell types */
    const CELL_TYPE_INLINE_STRING = 'inlineStr';
    const CELL_TYPE_STR = 'str';
    const CELL_TYPE_SHARED_STRING = 's';
    const CELL_TYPE_BOOLEAN = 'b';
    const CELL_TYPE_NUMERIC = 'n';
    const CELL_TYPE_DATE = 'd';
    const CELL_TYPE_ERROR = 'e';

    /** Definition of XML nodes names used to parse data */
    const XML_NODE_VALUE = 'v';
    const XML_NODE_INLINE_STRING_VALUE = 't';

    /** Definition of XML attributes used to parse data */
    const XML_ATTRIBUTE_TYPE = 't';
    const XML_ATTRIBUTE_STYLE_ID = 's';

    /** Constants used for date formatting */
    const NUM_SECONDS_IN_ONE_DAY = 86400;
    const NUM_SECONDS_IN_ONE_HOUR = 3600;
    const NUM_SECONDS_IN_ONE_MINUTE = 60;

    /**
     * February 29th, 1900 is NOT a leap year but Excel thinks it is...
     * @see https://en.wikipedia.org/wiki/Year_1900_problem#Microsoft_Excel
     */
    const ERRONEOUS_EXCEL_LEAP_YEAR_DAY = 60;

    /** @var SharedStringsHelper Helper to work with shared strings */
    protected $sharedStringsHelper;

    /** @var StyleHelper Helper to work with styles */
    protected $styleHelper;

    /** @var bool Whether date/time values should be returned as PHP objects or be formatted as strings */
    protected $shouldFormatDates;

    /** @var \Box\Spout\Common\Escaper\XLSX Used to unescape XML data */
    protected $escaper;

    /**
     * @param SharedStringsHelper $sharedStringsHelper Helper to work with shared strings
     * @param StyleHelper $styleHelper Helper to work with styles
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     */
    public function __construct($sharedStringsHelper, $styleHelper, $shouldFormatDates)
    {
        $this->sharedStringsHelper = $sharedStringsHelper;
        $this->styleHelper = $styleHelper;
        $this->shouldFormatDates = $shouldFormatDates;

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->escaper = \Box\Spout\Common\Escaper\XLSX::getInstance();
    }

    /**
     * Returns the (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @param \DOMNode $node
     * @return string|int|float|bool|\DateTime|null The value associated with the cell (null when the cell has an error)
     */
    public function extractAndFormatNodeValue($node)
    {
        // Default cell type is "n"
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE) ?: self::CELL_TYPE_NUMERIC;
        $cellStyleId = intval($node->getAttribute(self::XML_ATTRIBUTE_STYLE_ID));
        $vNodeValue = $this->getVNodeValue($node);

        if (($vNodeValue === '') && ($cellType !== self::CELL_TYPE_INLINE_STRING)) {
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
                return null;
        }
    }

    /**
     * Returns the cell's string value from a node's nested value node
     *
     * @param \DOMNode $node
     * @return string The value associated with the cell
     */
    protected function getVNodeValue($node)
    {
        // for cell types having a "v" tag containing the value.
        // if not, the returned value should be empty string.
        $vNode = $node->getElementsByTagName(self::XML_NODE_VALUE)->item(0);
        return ($vNode !== null) ? $vNode->nodeValue : '';
    }

    /**
     * Returns the cell String value where string is inline.
     *
     * @param \DOMNode $node
     * @return string The value associated with the cell (null when the cell has an error)
     */
    protected function formatInlineStringCellValue($node)
    {
        // inline strings are formatted this way:
        // <c r="A1" t="inlineStr"><is><t>[INLINE_STRING]</t></is></c>
        $tNode = $node->getElementsByTagName(self::XML_NODE_INLINE_STRING_VALUE)->item(0);
        $cellValue = $this->escaper->unescape($tNode->nodeValue);
        return $cellValue;
    }

    /**
     * Returns the cell String value from shared-strings file using nodeValue index.
     *
     * @param string $nodeValue
     * @return string The value associated with the cell (null when the cell has an error)
     */
    protected function formatSharedStringCellValue($nodeValue)
    {
        // shared strings are formatted this way:
        // <c r="A1" t="s"><v>[SHARED_STRING_INDEX]</v></c>
        $sharedStringIndex = intval($nodeValue);
        $escapedCellValue = $this->sharedStringsHelper->getStringAtIndex($sharedStringIndex);
        $cellValue = $this->escaper->unescape($escapedCellValue);
        return $cellValue;
    }

    /**
     * Returns the cell String value, where string is stored in value node.
     *
     * @param string $nodeValue
     * @return string The value associated with the cell (null when the cell has an error)
     */
    protected function formatStrCellValue($nodeValue)
    {
        $escapedCellValue = trim($nodeValue);
        $cellValue = $this->escaper->unescape($escapedCellValue);
        return $cellValue;
    }

    /**
     * Returns the cell Numeric value from string of nodeValue.
     * The value can also represent a timestamp and a DateTime will be returned.
     *
     * @param string $nodeValue
     * @param int $cellStyleId 0 being the default style
     * @return int|float|\DateTime|null The value associated with the cell
     */
    protected function formatNumericCellValue($nodeValue, $cellStyleId)
    {
        // Numeric values can represent numbers as well as timestamps.
        // We need to look at the style of the cell to determine whether it is one or the other.
        $shouldFormatAsDate = $this->styleHelper->shouldFormatNumericValueAsDate($cellStyleId);

        if ($shouldFormatAsDate) {
            return $this->formatExcelTimestampValue(floatval($nodeValue), $cellStyleId);
        } else {
            $nodeIntValue = intval($nodeValue);
            return ($nodeIntValue == $nodeValue) ? $nodeIntValue : floatval($nodeValue);
        }
    }

    /**
     * Returns a cell's PHP Date value, associated to the given timestamp.
     * NOTE: The timestamp is a float representing the number of days since January 1st, 1900.
     * NOTE: The timestamp can also represent a time, if it is a value between 0 and 1.
     *
     * @param float $nodeValue
     * @param int $cellStyleId 0 being the default style
     * @return \DateTime|null The value associated with the cell or NULL if invalid date value
     */
    protected function formatExcelTimestampValue($nodeValue, $cellStyleId)
    {
        // Fix for the erroneous leap year in Excel
        if (ceil($nodeValue) > self::ERRONEOUS_EXCEL_LEAP_YEAR_DAY) {
            --$nodeValue;
        }

        if ($nodeValue >= 1) {
            // Values greater than 1 represent "dates". The value 1.0 representing the "base" date: 1900-01-01.
            return $this->formatExcelTimestampValueAsDateValue($nodeValue, $cellStyleId);
        } else if ($nodeValue >= 0) {
            // Values between 0 and 1 represent "times".
            return $this->formatExcelTimestampValueAsTimeValue($nodeValue, $cellStyleId);
        } else {
            // invalid date
            return null;
        }
    }

    /**
     * Returns a cell's PHP DateTime value, associated to the given timestamp.
     * Only the time value matters. The date part is set to Jan 1st, 1900 (base Excel date).
     *
     * @param float $nodeValue
     * @param int $cellStyleId 0 being the default style
     * @return \DateTime|string The value associated with the cell
     */
    protected function formatExcelTimestampValueAsTimeValue($nodeValue, $cellStyleId)
    {
        $time = round($nodeValue * self::NUM_SECONDS_IN_ONE_DAY);
        $hours = floor($time / self::NUM_SECONDS_IN_ONE_HOUR);
        $minutes = floor($time / self::NUM_SECONDS_IN_ONE_MINUTE) - ($hours * self::NUM_SECONDS_IN_ONE_MINUTE);
        $seconds = $time - ($hours * self::NUM_SECONDS_IN_ONE_HOUR) - ($minutes * self::NUM_SECONDS_IN_ONE_MINUTE);

        // using the base Excel date (Jan 1st, 1900) - not relevant here
        $dateObj = new \DateTime('1900-01-01');
        $dateObj->setTime($hours, $minutes, $seconds);

        if ($this->shouldFormatDates) {
            $styleNumberFormatCode = $this->styleHelper->getNumberFormatCode($cellStyleId);
            $phpDateFormat = DateFormatHelper::toPHPDateFormat($styleNumberFormatCode);
            return $dateObj->format($phpDateFormat);
        } else {
            return $dateObj;
        }
    }

    /**
     * Returns a cell's PHP Date value, associated to the given timestamp.
     * NOTE: The timestamp is a float representing the number of days since January 1st, 1900.
     *
     * @param float $nodeValue
     * @param int $cellStyleId 0 being the default style
     * @return \DateTime|string|null The value associated with the cell or NULL if invalid date value
     */
    protected function formatExcelTimestampValueAsDateValue($nodeValue, $cellStyleId)
    {
        // Do not use any unix timestamps for calculation to prevent
        // issues with numbers exceeding 2^31.
        $secondsRemainder = fmod($nodeValue, 1) * self::NUM_SECONDS_IN_ONE_DAY;
        $secondsRemainder = round($secondsRemainder, 0);

        try {
            $dateObj = \DateTime::createFromFormat('|Y-m-d', '1899-12-31');
            $dateObj->modify('+' . intval($nodeValue) . 'days');
            $dateObj->modify('+' . $secondsRemainder . 'seconds');

            if ($this->shouldFormatDates) {
                $styleNumberFormatCode = $this->styleHelper->getNumberFormatCode($cellStyleId);
                $phpDateFormat = DateFormatHelper::toPHPDateFormat($styleNumberFormatCode);
                return $dateObj->format($phpDateFormat);
            } else {
                return $dateObj;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the cell Boolean value from a specific node's Value.
     *
     * @param string $nodeValue
     * @return bool The value associated with the cell
     */
    protected function formatBooleanCellValue($nodeValue)
    {
        // !! is similar to boolval()
        $cellValue = !!$nodeValue;
        return $cellValue;
    }

    /**
     * Returns a cell's PHP Date value, associated to the given stored nodeValue.
     * @see ECMA-376 Part 1 - §18.17.4
     *
     * @param string $nodeValue ISO 8601 Date string
     * @return \DateTime|string|null The value associated with the cell or NULL if invalid date value
     */
    protected function formatDateCellValue($nodeValue)
    {
        // Mitigate thrown Exception on invalid date-time format (http://php.net/manual/en/datetime.construct.php)
        try {
            return ($this->shouldFormatDates) ? $nodeValue : new \DateTime($nodeValue);
        } catch (\Exception $e) {
            return null;
        }
    }
}
