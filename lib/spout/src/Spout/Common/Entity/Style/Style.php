<?php

namespace Box\Spout\Common\Entity\Style;

/**
 * Class Style
 * Represents a style to be applied to a cell
 */
class Style
{
    /** Default font values */
    const DEFAULT_FONT_SIZE = 11;
    const DEFAULT_FONT_COLOR = Color::BLACK;
    const DEFAULT_FONT_NAME = 'Arial';

    /** @var int|null Style ID */
    private $id;

    /** @var bool Whether the font should be bold */
    private $fontBold = false;
    /** @var bool Whether the bold property was set */
    private $hasSetFontBold = false;

    /** @var bool Whether the font should be italic */
    private $fontItalic = false;
    /** @var bool Whether the italic property was set */
    private $hasSetFontItalic = false;

    /** @var bool Whether the font should be underlined */
    private $fontUnderline = false;
    /** @var bool Whether the underline property was set */
    private $hasSetFontUnderline = false;

    /** @var bool Whether the font should be struck through */
    private $fontStrikethrough = false;
    /** @var bool Whether the strikethrough property was set */
    private $hasSetFontStrikethrough = false;

    /** @var int Font size */
    private $fontSize = self::DEFAULT_FONT_SIZE;
    /** @var bool Whether the font size property was set */
    private $hasSetFontSize = false;

    /** @var string Font color */
    private $fontColor = self::DEFAULT_FONT_COLOR;
    /** @var bool Whether the font color property was set */
    private $hasSetFontColor = false;

    /** @var string Font name */
    private $fontName = self::DEFAULT_FONT_NAME;
    /** @var bool Whether the font name property was set */
    private $hasSetFontName = false;

    /** @var bool Whether specific font properties should be applied */
    private $shouldApplyFont = false;

    /** @var bool Whether the text should wrap in the cell (useful for long or multi-lines text) */
    private $shouldWrapText = false;
    /** @var bool Whether the wrap text property was set */
    private $hasSetWrapText = false;

    /** @var Border */
    private $border;

    /** @var bool Whether border properties should be applied */
    private $shouldApplyBorder = false;

    /** @var string Background color */
    private $backgroundColor;

    /** @var bool */
    private $hasSetBackgroundColor = false;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Style
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Border
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * @param Border $border
     * @return Style
     */
    public function setBorder(Border $border)
    {
        $this->shouldApplyBorder = true;
        $this->border = $border;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldApplyBorder()
    {
        return $this->shouldApplyBorder;
    }

    /**
     * @return bool
     */
    public function isFontBold()
    {
        return $this->fontBold;
    }

    /**
     * @return Style
     */
    public function setFontBold()
    {
        $this->fontBold = true;
        $this->hasSetFontBold = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontBold()
    {
        return $this->hasSetFontBold;
    }

    /**
     * @return bool
     */
    public function isFontItalic()
    {
        return $this->fontItalic;
    }

    /**
     * @return Style
     */
    public function setFontItalic()
    {
        $this->fontItalic = true;
        $this->hasSetFontItalic = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontItalic()
    {
        return $this->hasSetFontItalic;
    }

    /**
     * @return bool
     */
    public function isFontUnderline()
    {
        return $this->fontUnderline;
    }

    /**
     * @return Style
     */
    public function setFontUnderline()
    {
        $this->fontUnderline = true;
        $this->hasSetFontUnderline = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontUnderline()
    {
        return $this->hasSetFontUnderline;
    }

    /**
     * @return bool
     */
    public function isFontStrikethrough()
    {
        return $this->fontStrikethrough;
    }

    /**
     * @return Style
     */
    public function setFontStrikethrough()
    {
        $this->fontStrikethrough = true;
        $this->hasSetFontStrikethrough = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontStrikethrough()
    {
        return $this->hasSetFontStrikethrough;
    }

    /**
     * @return int
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize Font size, in pixels
     * @return Style
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        $this->hasSetFontSize = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontSize()
    {
        return $this->hasSetFontSize;
    }

    /**
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    /**
     * Sets the font color.
     *
     * @param string $fontColor ARGB color (@see Color)
     * @return Style
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
        $this->hasSetFontColor = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontColor()
    {
        return $this->hasSetFontColor;
    }

    /**
     * @return string
     */
    public function getFontName()
    {
        return $this->fontName;
    }

    /**
     * @param string $fontName Name of the font to use
     * @return Style
     */
    public function setFontName($fontName)
    {
        $this->fontName = $fontName;
        $this->hasSetFontName = true;
        $this->shouldApplyFont = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetFontName()
    {
        return $this->hasSetFontName;
    }

    /**
     * @return bool
     */
    public function shouldWrapText()
    {
        return $this->shouldWrapText;
    }

    /**
     * @param bool $shouldWrap Should the text be wrapped
     * @return Style
     */
    public function setShouldWrapText($shouldWrap = true)
    {
        $this->shouldWrapText = $shouldWrap;
        $this->hasSetWrapText = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSetWrapText()
    {
        return $this->hasSetWrapText;
    }

    /**
     * @return bool Whether specific font properties should be applied
     */
    public function shouldApplyFont()
    {
        return $this->shouldApplyFont;
    }

    /**
     * Sets the background color
     * @param string $color ARGB color (@see Color)
     * @return Style
     */
    public function setBackgroundColor($color)
    {
        $this->hasSetBackgroundColor = true;
        $this->backgroundColor = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @return bool Whether the background color should be applied
     */
    public function shouldApplyBackgroundColor()
    {
        return $this->hasSetBackgroundColor;
    }
}
