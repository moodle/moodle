<?php

namespace Box\Spout\Writer\Style;

/**
 * Class StyleBuilder
 * Builder to create new styles
 *
 * @package Box\Spout\Writer\Style
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
     * @api
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
     * @api
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
     * @api
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
     * @api
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
     * @api
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
     * @api
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
     * @api
     * @param string $fontName Name of the font to use
     * @return StyleBuilder
     */
    public function setFontName($fontName)
    {
        $this->style->setFontName($fontName);
        return $this;
    }

    /**
     * Makes the text wrap in the cell if it's too long or
     * on multiple lines.
     *
     * @api
     * @return StyleBuilder
     */
    public function setShouldWrapText()
    {
        $this->style->setShouldWrapText();
        return $this;
    }

    /**
     * Returns the configured style. The style is cached and can be reused.
     *
     * @api
     * @return Style
     */
    public function build()
    {
        return $this->style;
    }
}
