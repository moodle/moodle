<?php

namespace Box\Spout\Writer\Common\Creator\Style;

use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\BorderPart;
use Box\Spout\Common\Entity\Style\Color;

/**
 * Class BorderBuilder
 */
class BorderBuilder
{
    /**
     * @var Border
     */
    protected $border;

    public function __construct()
    {
        $this->border = new Border();
    }

    /**
     * @param string $color Border A RGB color code
     * @param string $width Border width @see BorderPart::allowedWidths
     * @param string $style Border style @see BorderPart::allowedStyles
     * @return BorderBuilder
     */
    public function setBorderTop($color = Color::BLACK, $width = Border::WIDTH_MEDIUM, $style = Border::STYLE_SOLID)
    {
        $this->border->addPart(new BorderPart(Border::TOP, $color, $width, $style));

        return $this;
    }

    /**
     * @param string $color Border A RGB color code
     * @param string $width Border width @see BorderPart::allowedWidths
     * @param string $style Border style @see BorderPart::allowedStyles
     * @return BorderBuilder
     */
    public function setBorderRight($color = Color::BLACK, $width = Border::WIDTH_MEDIUM, $style = Border::STYLE_SOLID)
    {
        $this->border->addPart(new BorderPart(Border::RIGHT, $color, $width, $style));

        return $this;
    }

    /**
     * @param string $color Border A RGB color code
     * @param string $width Border width @see BorderPart::allowedWidths
     * @param string $style Border style @see BorderPart::allowedStyles
     * @return BorderBuilder
     */
    public function setBorderBottom($color = Color::BLACK, $width = Border::WIDTH_MEDIUM, $style = Border::STYLE_SOLID)
    {
        $this->border->addPart(new BorderPart(Border::BOTTOM, $color, $width, $style));

        return $this;
    }

    /**
     * @param string $color Border A RGB color code
     * @param string $width Border width @see BorderPart::allowedWidths
     * @param string $style Border style @see BorderPart::allowedStyles
     * @return BorderBuilder
     */
    public function setBorderLeft($color = Color::BLACK, $width = Border::WIDTH_MEDIUM, $style = Border::STYLE_SOLID)
    {
        $this->border->addPart(new BorderPart(Border::LEFT, $color, $width, $style));

        return $this;
    }

    /**
     * @return Border
     */
    public function build()
    {
        return $this->border;
    }
}
