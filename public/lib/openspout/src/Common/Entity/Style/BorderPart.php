<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Writer\Exception\Border\InvalidNameException;
use OpenSpout\Writer\Exception\Border\InvalidStyleException;
use OpenSpout\Writer\Exception\Border\InvalidWidthException;

final readonly class BorderPart
{
    public const allowedStyles = [
        Border::STYLE_NONE,
        Border::STYLE_SOLID,
        Border::STYLE_DASHED,
        Border::STYLE_DOTTED,
        Border::STYLE_DOUBLE,
    ];

    public const allowedNames = [
        Border::LEFT,
        Border::RIGHT,
        Border::TOP,
        Border::BOTTOM,
    ];

    public const allowedWidths = [
        Border::WIDTH_THIN,
        Border::WIDTH_MEDIUM,
        Border::WIDTH_THICK,
    ];

    private string $style;
    private string $name;
    private string $color;
    private string $width;

    /**
     * @param string $name  @see  BorderPart::allowedNames
     * @param string $color A RGB color code
     * @param string $width @see BorderPart::allowedWidths
     * @param string $style @see BorderPart::allowedStyles
     *
     * @throws InvalidNameException
     * @throws InvalidStyleException
     * @throws InvalidWidthException
     */
    public function __construct(
        string $name,
        string $color = Color::BLACK,
        string $width = Border::WIDTH_MEDIUM,
        string $style = Border::STYLE_SOLID
    ) {
        if (!\in_array($name, self::allowedNames, true)) {
            throw new InvalidNameException($name);
        }
        if (!\in_array($style, self::allowedStyles, true)) {
            throw new InvalidStyleException($style);
        }
        if (!\in_array($width, self::allowedWidths, true)) {
            throw new InvalidWidthException($width);
        }

        $this->name = $name;
        $this->color = $color;
        $this->width = $width;
        $this->style = $style;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getWidth(): string
    {
        return $this->width;
    }
}
