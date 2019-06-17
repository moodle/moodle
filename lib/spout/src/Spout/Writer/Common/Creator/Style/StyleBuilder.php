<?php

namespace Box\Spout\Writer\Common\Creator\Style;

use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\Style;

/**
 * Class StyleBuilder
 * Builder to create new styles
 */
class StyleBuilder
{
    /** @var Style Style to be created */
    protected $style;

    /**
     *
     */
    public function __construct()
    {
        $this->style = new Style();
    }

    /**
     * Makes the font bold.
     *
     * @return StyleBuilder
     */
    public function setFontBold()
    {
        $this->style->setFontBold();

        return $this;
    }

    /**
     * Makes the font italic.
     *
     * @return StyleBuilder
     */
    public function setFontItalic()
    {
        $this->style->setFontItalic();

        return $this;
    }

    /**
     * Makes the font underlined.
     *
     * @return StyleBuilder
     */
    public function setFontUnderline()
    {
        $this->style->setFontUnderline();

        return $this;
    }

    /**
     * Makes the font struck through.
     *
     * @return StyleBuilder
     */
    public function setFontStrikethrough()
    {
        $this->style->setFontStrikethrough();

        return $this;
    }

    /**
     * Sets the font size.
     *
     * @param int $fontSize Font size, in pixels
     * @return StyleBuilder
     */
    public function setFontSize($fontSize)
    {
        $this->style->setFontSize($fontSize);

        return $this;
    }

    /**
     * Sets the font color.
     *
     * @param string $fontColor ARGB color (@see Color)
     * @return StyleBuilder
     */
    public function setFontColor($fontColor)
    {
        $this->style->setFontColor($fontColor);

        return $this;
    }

    /**
     * Sets the font name.
     *
     * @param string $fontName Name of the font to use
     * @return StyleBuilder
     */
    public function setFontName($fontName)
    {
        $this->style->setFontName($fontName);

        return $this;
    }

    /**
     * Makes the text wrap in the cell if requested
     *
     * @param bool $shouldWrap Should the text be wrapped
     * @return StyleBuilder
     */
    public function setShouldWrapText($shouldWrap = true)
    {
        $this->style->setShouldWrapText($shouldWrap);

        return $this;
    }

    /**
     * Set a border
     *
     * @param Border $border
     * @return $this
     */
    public function setBorder(Border $border)
    {
        $this->style->setBorder($border);

        return $this;
    }

    /**
     *  Sets a background color
     *
     * @param string $color ARGB color (@see Color)
     * @return StyleBuilder
     */
    public function setBackgroundColor($color)
    {
        $this->style->setBackgroundColor($color);

        return $this;
    }

    /**
     * Returns the configured style. The style is cached and can be reused.
     *
     * @return Style
     */
    public function build()
    {
        return $this->style;
    }
}
