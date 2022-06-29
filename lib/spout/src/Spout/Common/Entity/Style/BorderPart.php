<?php

namespace Box\Spout\Common\Entity\Style;

use Box\Spout\Writer\Exception\Border\InvalidNameException;
use Box\Spout\Writer\Exception\Border\InvalidStyleException;
use Box\Spout\Writer\Exception\Border\InvalidWidthException;

/**
 * Class BorderPart
 */
class BorderPart
{
    /**
     * @var string The style of this border part.
     */
    protected $style;

    /**
     * @var string The name of this border part.
     */
    protected $name;

    /**
     * @var string The color of this border part.
     */
    protected $color;

    /**
     * @var string The width of this border part.
     */
    protected $width;

    /**
     * @var array Allowed style constants for parts.
     */
    protected static $allowedStyles = [
        'none',
        'solid',
        'dashed',
        'dotted',
        'double',
    ];

    /**
     * @var array Allowed names constants for border parts.
     */
    protected static $allowedNames = [
        'left',
        'right',
        'top',
        'bottom',
    ];

    /**
     * @var array Allowed width constants for border parts.
     */
    protected static $allowedWidths = [
        'thin',
        'medium',
        'thick',
    ];

    /**
     * @param string $name @see  BorderPart::$allowedNames
     * @param string $color A RGB color code
     * @param string $width @see BorderPart::$allowedWidths
     * @param string $style @see BorderPart::$allowedStyles
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
     * @throws InvalidNameException
     * @return void
     */
    public function setName($name)
    {
        if (!\in_array($name, self::$allowedNames)) {
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
     * @throws InvalidStyleException
     * @return void
     */
    public function setStyle($style)
    {
        if (!\in_array($style, self::$allowedStyles)) {
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
     * @return void
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
     * @throws InvalidWidthException
     * @return void
     */
    public function setWidth($width)
    {
        if (!\in_array($width, self::$allowedWidths)) {
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
