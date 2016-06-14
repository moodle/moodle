<?php

namespace Box\Spout\Reader\ODS\Helper;

/**
 * Class CellValueFormatter
 * This class provides helper functions to format cell values
 *
 * @package Box\Spout\Reader\ODS\Helper
 */
class CellValueFormatter
{
    /** Definition of all possible cell types */
    const CELL_TYPE_STRING = 'string';
    const CELL_TYPE_FLOAT = 'float';
    const CELL_TYPE_BOOLEAN = 'boolean';
    const CELL_TYPE_DATE = 'date';
    const CELL_TYPE_TIME = 'time';
    const CELL_TYPE_CURRENCY = 'currency';
    const CELL_TYPE_PERCENTAGE = 'percentage';
    const CELL_TYPE_VOID = 'void';

    /** Definition of XML nodes names used to parse data */
    const XML_NODE_P = 'p';
    const XML_NODE_S = 'text:s';

    /** Definition of XML attribute used to parse data */
    const XML_ATTRIBUTE_TYPE = 'office:value-type';
    const XML_ATTRIBUTE_VALUE = 'office:value';
    const XML_ATTRIBUTE_BOOLEAN_VALUE = 'office:boolean-value';
    const XML_ATTRIBUTE_DATE_VALUE = 'office:date-value';
    const XML_ATTRIBUTE_TIME_VALUE = 'office:time-value';
    const XML_ATTRIBUTE_CURRENCY = 'office:currency';
    const XML_ATTRIBUTE_C = 'text:c';

    /** @var \Box\Spout\Common\Escaper\ODS Used to unescape XML data */
    protected $escaper;

    /**
     *
     */
    public function __construct()
    {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->escaper = new \Box\Spout\Common\Escaper\ODS();
    }

    /**
     * Returns the (unescaped) correctly marshalled, cell value associated to the given XML node.
     * @see http://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html#refTable13
     *
     * @param \DOMNode $node
     * @return string|int|float|bool|\DateTime|\DateInterval|null The value associated with the cell, empty string if cell's type is void/undefined, null on error
     */
    public function extractAndFormatNodeValue($node)
    {
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE);

        switch ($cellType) {
            case self::CELL_TYPE_STRING:
                return $this->formatStringCellValue($node);
            case self::CELL_TYPE_FLOAT:
                return $this->formatFloatCellValue($node);
            case self::CELL_TYPE_BOOLEAN:
                return $this->formatBooleanCellValue($node);
            case self::CELL_TYPE_DATE:
                return $this->formatDateCellValue($node);
            case self::CELL_TYPE_TIME:
                return $this->formatTimeCellValue($node);
            case self::CELL_TYPE_CURRENCY:
                return $this->formatCurrencyCellValue($node);
            case self::CELL_TYPE_PERCENTAGE:
                return $this->formatPercentageCellValue($node);
            case self::CELL_TYPE_VOID:
            default:
                return '';
        }
    }

    /**
     * Returns the cell String value.
     *
     * @param \DOMNode $node
     * @return string The value associated with the cell
     */
    protected function formatStringCellValue($node)
    {
        $pNodeValues = [];
        $pNodes = $node->getElementsByTagName(self::XML_NODE_P);

        foreach ($pNodes as $pNode) {
            $currentPValue = '';

            foreach ($pNode->childNodes as $childNode) {
                if ($childNode instanceof \DOMText) {
                    $currentPValue .= $childNode->nodeValue;
                } else if ($childNode->nodeName === self::XML_NODE_S) {
                    $spaceAttribute = $childNode->getAttribute(self::XML_ATTRIBUTE_C);
                    $numSpaces = (!empty($spaceAttribute)) ? intval($spaceAttribute) : 1;
                    $currentPValue .= str_repeat(' ', $numSpaces);
                }
            }

            $pNodeValues[] = $currentPValue;
        }

        $escapedCellValue = implode("\n", $pNodeValues);
        $cellValue = $this->escaper->unescape($escapedCellValue);
        return $cellValue;
    }

    /**
     * Returns the cell Numeric value from the given node.
     *
     * @param \DOMNode $node
     * @return int|float The value associated with the cell
     */
    protected function formatFloatCellValue($node)
    {
        $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);
        $nodeIntValue = intval($nodeValue);
        $cellValue = ($nodeIntValue == $nodeValue) ? $nodeIntValue : floatval($nodeValue);
        return $cellValue;
    }

    /**
     * Returns the cell Boolean value from the given node.
     *
     * @param \DOMNode $node
     * @return bool The value associated with the cell
     */
    protected function formatBooleanCellValue($node)
    {
        $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_BOOLEAN_VALUE);
        // !! is similar to boolval()
        $cellValue = !!$nodeValue;
        return $cellValue;
    }

    /**
     * Returns the cell Date value from the given node.
     *
     * @param \DOMNode $node
     * @return \DateTime|null The value associated with the cell or NULL if invalid date value
     */
    protected function formatDateCellValue($node)
    {
        try {
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_DATE_VALUE);
            return new \DateTime($nodeValue);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the cell Time value from the given node.
     *
     * @param \DOMNode $node
     * @return \DateInterval|null The value associated with the cell or NULL if invalid time value
     */
    protected function formatTimeCellValue($node)
    {
        try {
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_TIME_VALUE);
            return new \DateInterval($nodeValue);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the cell Currency value from the given node.
     *
     * @param \DOMNode $node
     * @return string The value associated with the cell (e.g. "100 USD" or "9.99 EUR")
     */
    protected function formatCurrencyCellValue($node)
    {
        $value = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);
        $currency = $node->getAttribute(self::XML_ATTRIBUTE_CURRENCY);

        return "$value $currency";
    }

    /**
     * Returns the cell Percentage value from the given node.
     *
     * @param \DOMNode $node
     * @return int|float The value associated with the cell
     */
    protected function formatPercentageCellValue($node)
    {
        // percentages are formatted like floats
        return $this->formatFloatCellValue($node);
    }
}
