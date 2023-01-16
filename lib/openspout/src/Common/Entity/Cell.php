<?php

namespace OpenSpout\Common\Entity;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Helper\CellTypeHelper;

class Cell
{
    /**
     * Numeric cell type (whole numbers, fractional numbers, dates).
     */
    public const TYPE_NUMERIC = 0;

    /**
     * String (text) cell type.
     */
    public const TYPE_STRING = 1;

    /**
     * Formula cell type
     * Not used at the moment.
     */
    public const TYPE_FORMULA = 2;

    /**
     * Empty cell type.
     */
    public const TYPE_EMPTY = 3;

    /**
     * Boolean cell type.
     */
    public const TYPE_BOOLEAN = 4;

    /**
     * Date cell type.
     */
    public const TYPE_DATE = 5;

    /**
     * Error cell type.
     */
    public const TYPE_ERROR = 6;

    /**
     * The value of this cell.
     *
     * @var null|mixed
     */
    protected $value;

    /**
     * The cell type.
     *
     * @var null|int
     */
    protected $type;

    /**
     * The cell style.
     *
     * @var Style
     */
    protected $style;

    /**
     * @param null|mixed $value
     */
    public function __construct($value, Style $style = null)
    {
        $this->setValue($value);
        $this->setStyle($style);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * @param null|mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->type = $this->detectType($value);
    }

    /**
     * @return null|mixed
     */
    public function getValue()
    {
        return !$this->isError() ? $this->value : null;
    }

    /**
     * @return mixed
     */
    public function getValueEvenIfError()
    {
        return $this->value;
    }

    /**
     * @param null|Style $style
     */
    public function setStyle($style)
    {
        $this->style = $style ?: new Style();
    }

    /**
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @return null|int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isBoolean()
    {
        return self::TYPE_BOOLEAN === $this->type;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return self::TYPE_EMPTY === $this->type;
    }

    /**
     * @return bool
     */
    public function isNumeric()
    {
        return self::TYPE_NUMERIC === $this->type;
    }

    /**
     * @return bool
     */
    public function isString()
    {
        return self::TYPE_STRING === $this->type;
    }

    /**
     * @return bool
     */
    public function isDate()
    {
        return self::TYPE_DATE === $this->type;
    }

    /**
     * @return bool
     */
    public function isFormula()
    {
        return self::TYPE_FORMULA === $this->type;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return self::TYPE_ERROR === $this->type;
    }

    /**
     * Get the current value type.
     *
     * @param null|mixed $value
     *
     * @return int
     */
    protected function detectType($value)
    {
        if (CellTypeHelper::isBoolean($value)) {
            return self::TYPE_BOOLEAN;
        }
        if (CellTypeHelper::isEmpty($value)) {
            return self::TYPE_EMPTY;
        }
        if (CellTypeHelper::isNumeric($value)) {
            return self::TYPE_NUMERIC;
        }
        if (CellTypeHelper::isDateTimeOrDateInterval($value)) {
            return self::TYPE_DATE;
        }
        if (CellTypeHelper::isFormula($value)) {
            return self::TYPE_FORMULA;
        }
        if (CellTypeHelper::isNonEmptyString($value)) {
            return self::TYPE_STRING;
        }

        return self::TYPE_ERROR;
    }
}
