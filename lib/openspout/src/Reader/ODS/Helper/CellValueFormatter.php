<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS\Helper;

use DateInterval;
use DateTimeImmutable;
use DOMElement;
use DOMNode;
use DOMText;
use Exception;
use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Reader\Exception\InvalidValueException;

/**
 * @internal
 */
final class CellValueFormatter
{
    /**
     * Definition of all possible cell types.
     */
    public const CELL_TYPE_STRING = 'string';
    public const CELL_TYPE_FLOAT = 'float';
    public const CELL_TYPE_BOOLEAN = 'boolean';
    public const CELL_TYPE_DATE = 'date';
    public const CELL_TYPE_TIME = 'time';
    public const CELL_TYPE_CURRENCY = 'currency';
    public const CELL_TYPE_PERCENTAGE = 'percentage';
    public const CELL_TYPE_VOID = 'void';

    /**
     * Definition of XML nodes names used to parse data.
     */
    public const XML_NODE_P = 'p';
    public const XML_NODE_TEXT_A = 'text:a';
    public const XML_NODE_TEXT_SPAN = 'text:span';
    public const XML_NODE_TEXT_S = 'text:s';
    public const XML_NODE_TEXT_TAB = 'text:tab';
    public const XML_NODE_TEXT_LINE_BREAK = 'text:line-break';

    /**
     * Definition of XML attributes used to parse data.
     */
    public const XML_ATTRIBUTE_TYPE = 'office:value-type';
    public const XML_ATTRIBUTE_VALUE = 'office:value';
    public const XML_ATTRIBUTE_BOOLEAN_VALUE = 'office:boolean-value';
    public const XML_ATTRIBUTE_DATE_VALUE = 'office:date-value';
    public const XML_ATTRIBUTE_TIME_VALUE = 'office:time-value';
    public const XML_ATTRIBUTE_CURRENCY = 'office:currency';
    public const XML_ATTRIBUTE_C = 'text:c';

    /**
     * List of XML nodes representing whitespaces and their corresponding value.
     */
    private const WHITESPACE_XML_NODES = [
        self::XML_NODE_TEXT_S => ' ',
        self::XML_NODE_TEXT_TAB => "\t",
        self::XML_NODE_TEXT_LINE_BREAK => "\n",
    ];

    /** @var bool Whether date/time values should be returned as PHP objects or be formatted as strings */
    private bool $shouldFormatDates;

    /** @var ODS Used to unescape XML data */
    private ODS $escaper;

    /**
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     * @param ODS  $escaper           Used to unescape XML data
     */
    public function __construct(bool $shouldFormatDates, ODS $escaper)
    {
        $this->shouldFormatDates = $shouldFormatDates;
        $this->escaper = $escaper;
    }

    /**
     * Returns the (unescaped) correctly marshalled, cell value associated to the given XML node.
     *
     * @see http://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html#refTable13
     *
     * @return bool|DateInterval|DateTimeImmutable|float|int|string The value associated with the cell, empty string if cell's type is void/undefined
     *
     * @throws InvalidValueException If the node value is not valid
     */
    public function extractAndFormatNodeValue(DOMElement $node): bool|DateInterval|DateTimeImmutable|float|int|string
    {
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE);

        return match ($cellType) {
            self::CELL_TYPE_STRING => $this->formatStringCellValue($node),
            self::CELL_TYPE_FLOAT => $this->formatFloatCellValue($node),
            self::CELL_TYPE_BOOLEAN => $this->formatBooleanCellValue($node),
            self::CELL_TYPE_DATE => $this->formatDateCellValue($node),
            self::CELL_TYPE_TIME => $this->formatTimeCellValue($node),
            self::CELL_TYPE_CURRENCY => $this->formatCurrencyCellValue($node),
            self::CELL_TYPE_PERCENTAGE => $this->formatPercentageCellValue($node),
            default => '',
        };
    }

    /**
     * Returns the cell String value.
     *
     * @return string The value associated with the cell
     */
    private function formatStringCellValue(DOMElement $node): string
    {
        $pNodeValues = [];
        $pNodes = $node->getElementsByTagName(self::XML_NODE_P);

        foreach ($pNodes as $pNode) {
            $pNodeValues[] = $this->extractTextValueFromNode($pNode);
        }

        $escapedCellValue = implode("\n", $pNodeValues);

        return $this->escaper->unescape($escapedCellValue);
    }

    /**
     * Returns the cell Numeric value from the given node.
     *
     * @return float|int The value associated with the cell
     */
    private function formatFloatCellValue(DOMElement $node): float|int
    {
        $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);

        $nodeIntValue = (int) $nodeValue;
        $nodeFloatValue = (float) $nodeValue;

        return ((float) $nodeIntValue === $nodeFloatValue) ? $nodeIntValue : $nodeFloatValue;
    }

    /**
     * Returns the cell Boolean value from the given node.
     *
     * @return bool The value associated with the cell
     */
    private function formatBooleanCellValue(DOMElement $node): bool
    {
        return (bool) $node->getAttribute(self::XML_ATTRIBUTE_BOOLEAN_VALUE);
    }

    /**
     * Returns the cell Date value from the given node.
     *
     * @throws InvalidValueException If the value is not a valid date
     */
    private function formatDateCellValue(DOMElement $node): string|DateTimeImmutable
    {
        // The XML node looks like this:
        // <table:table-cell calcext:value-type="date" office:date-value="2016-05-19T16:39:00" office:value-type="date">
        //   <text:p>05/19/16 04:39 PM</text:p>
        // </table:table-cell>

        if ($this->shouldFormatDates) {
            // The date is already formatted in the "p" tag
            $nodeWithValueAlreadyFormatted = $node->getElementsByTagName(self::XML_NODE_P)->item(0);
            $cellValue = $nodeWithValueAlreadyFormatted->nodeValue;
        } else {
            // otherwise, get it from the "date-value" attribute
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_DATE_VALUE);

            try {
                $cellValue = new DateTimeImmutable($nodeValue);
            } catch (Exception $previous) {
                throw new InvalidValueException($nodeValue, '', 0, $previous);
            }
        }

        return $cellValue;
    }

    /**
     * Returns the cell Time value from the given node.
     *
     * @return DateInterval|string The value associated with the cell
     *
     * @throws InvalidValueException If the value is not a valid time
     */
    private function formatTimeCellValue(DOMElement $node): DateInterval|string
    {
        // The XML node looks like this:
        // <table:table-cell calcext:value-type="time" office:time-value="PT13H24M00S" office:value-type="time">
        //   <text:p>01:24:00 PM</text:p>
        // </table:table-cell>

        if ($this->shouldFormatDates) {
            // The date is already formatted in the "p" tag
            $nodeWithValueAlreadyFormatted = $node->getElementsByTagName(self::XML_NODE_P)->item(0);
            $cellValue = $nodeWithValueAlreadyFormatted->nodeValue;
        } else {
            // otherwise, get it from the "time-value" attribute
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_TIME_VALUE);

            try {
                $cellValue = new DateInterval($nodeValue);
            } catch (Exception $previous) {
                throw new InvalidValueException($nodeValue, '', 0, $previous);
            }
        }

        return $cellValue;
    }

    /**
     * Returns the cell Currency value from the given node.
     *
     * @return string The value associated with the cell (e.g. "100 USD" or "9.99 EUR")
     */
    private function formatCurrencyCellValue(DOMElement $node): string
    {
        $value = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);
        $currency = $node->getAttribute(self::XML_ATTRIBUTE_CURRENCY);

        return "{$value} {$currency}";
    }

    /**
     * Returns the cell Percentage value from the given node.
     *
     * @return float|int The value associated with the cell
     */
    private function formatPercentageCellValue(DOMElement $node): float|int
    {
        // percentages are formatted like floats
        return $this->formatFloatCellValue($node);
    }

    private function extractTextValueFromNode(DOMNode $pNode): string
    {
        $textValue = '';

        foreach ($pNode->childNodes as $childNode) {
            if ($childNode instanceof DOMText) {
                $textValue .= $childNode->nodeValue;
            } elseif ($this->isWhitespaceNode($childNode->nodeName) && $childNode instanceof DOMElement) {
                $textValue .= $this->transformWhitespaceNode($childNode);
            } elseif (self::XML_NODE_TEXT_A === $childNode->nodeName || self::XML_NODE_TEXT_SPAN === $childNode->nodeName) {
                $textValue .= $this->extractTextValueFromNode($childNode);
            }
        }

        return $textValue;
    }

    /**
     * Returns whether the given node is a whitespace node. It must be one of these:
     *  - <text:s />
     *  - <text:tab />
     *  - <text:line-break />.
     */
    private function isWhitespaceNode(string $nodeName): bool
    {
        return isset(self::WHITESPACE_XML_NODES[$nodeName]);
    }

    /**
     * The "<text:p>" node can contain the string value directly
     * or contain child elements. In this case, whitespaces contain in
     * the child elements should be replaced by their XML equivalent:
     *  - space => <text:s />
     *  - tab => <text:tab />
     *  - line break => <text:line-break />.
     *
     * @see https://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html#__RefHeading__1415200_253892949
     *
     * @param DOMElement $node The XML node representing a whitespace
     *
     * @return string The corresponding whitespace value
     */
    private function transformWhitespaceNode(DOMElement $node): string
    {
        $countAttribute = $node->getAttribute(self::XML_ATTRIBUTE_C); // only defined for "<text:s>"
        $numWhitespaces = '' !== $countAttribute ? (int) $countAttribute : 1;

        return str_repeat(self::WHITESPACE_XML_NODES[$node->nodeName], $numWhitespaces);
    }
}
