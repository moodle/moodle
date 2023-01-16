<?php

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Writer\Exception\Border\InvalidNameException;
use OpenSpout\Writer\Exception\Border\InvalidStyleException;
use OpenSpout\Writer\Exception\Border\InvalidWidthException;

class BorderPart
{
    /**
     * @var string the style of this border part
     */
    protected $style;

    /**
     * @var string the name of this border part
     */
    protected $name;

    /**
     * @var string the color of this border part
     */
    protected $color;

    /**
     * @var string the width of this border part
     */
    protected $width;

    /**
     * @var array allowed style constants for parts
     */
    protected static $allowedStyles = [
        'none',
        'solid',
        'dashed',
        'dotted',
        'double',
    ];

    /**
     * @var array allowed names constants for border parts
     */
    protected static $allowedNames = [
        'left',
        'right',
        'top',
        'bottom',
    ];

    /**
     * @var array allowed width constants for border parts
     */
    protected static $allowedWidths = [
        'thin',
        'medium',
        'thick',
    ];

    /**
     * @param string $name  @see  BorderPart::$allowedNames
     * @param string $color A RGB color code
     * @param string $width @see BorderPart::$allowedWidths
     * @param string $style @see BorderPart::$allowedStyles
     *
     * @throws InvalidNameException
     * @throws InvalidStyleException
     * @throws InvalidWidthException
     */
    public function __construct($name, $color = Color::BLACK, $width = Border::WIDTH_MEDIUM, $style = Border::STYLE_SOLID)
    {
        $this->setName($name);
        $this->setColor($color);
        $this->setWidth($width);
        $this->setStyle($style);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name The name of the border part @see BorderPart::$allowedNames
     *
     * @throws InvalidNameException
     */
    public function setName($name)
    {
        if (!\in_array($name, self::$allowedNames, true)) {
            throw new InvalidNameException($name);
        }
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style The style of the border part @see BorderPart::$allowedStyles
     *
     * @throws InvalidStyleException
     */
    public function setStyle($style)
    {
        if (!\in_array($style, self::$allowedStyles, true)) {
            throw new InvalidStyleException($style);
        }
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color The color of the border part @see Color::rgb()
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width The width of the border part @see BorderPart::$allowedWidths
     *
     * @throws InvalidWidthException
     */
    public function setWidth($width)
    {
        if (!\in_array($width, self::$allowedWidths, true)) {
            throw new InvalidWidthException($width);
        }
        $this->width = $width;
    }

    /**
     * @return array
     */
    public static function getAllowedStyles()
    {
        return self::$allowedStyles;
    }

    /**
     * @return array
     */
    public static function getAllowedNames()
    {
        return self::$allowedNames;
    }

    /**
     * @return array
     */
    public static function getAllowedWidths()
    {
        return self::$allowedWidths;
    }
}
