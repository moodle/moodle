<?php

namespace Box\Spout\Writer\Style;

/**
 * Class Style
 * Represents a style to be applied to a cell
 *
 * @package Box\Spout\Writer\Style
 */
class Style
{
    /** Default font values */
    const DEFAULT_FONT_SIZE = 11;
    const DEFAULT_FONT_COLOR = Color::BLACK;
    const DEFAULT_FONT_NAME = 'Arial';

    /** @var int|null Style ID */
    protected $id = null;

    /** @var bool Whether the font should be bold */
    protected $fontBold = false;
    /** @var bool Whether the bold property was set */
    protected $hasSetFontBold = false;

    /** @var bool Whether the font should be italic */
    protected $fontItalic = false;
    /** @var bool Whether the italic property was set */
    protected $hasSetFontItalic = false;

    /** @var bool Whether the font should be underlined */
    protected $fontUnderline = false;
    /** @var bool Whether the underline property was set */
    protected $hasSetFontUnderline = false;

    /** @var bool Whether the font should be struck through */
    protected $fontStrikethrough = false;
    /** @var bool Whether the strikethrough property was set */
    protected $hasSetFontStrikethrough = false;

    /** @var int Font size */
    protected $fontSize = self::DEFAULT_FONT_SIZE;
    /** @var bool Whether the font size property was set */
    protected $hasSetFontSize = false;

    /** @var string Font color */
    protected $fontColor = self::DEFAULT_FONT_COLOR;
    /** @var bool Whether the font color property was set */
    protected $hasSetFontColor = false;

    /** @var string Font name */
    protected $fontName = self::DEFAULT_FONT_NAME;
    /** @var bool Whether the font name property was set */
    protected $hasSetFontName = false;

    /** @var bool Whether specific font properties should be applied */
    protected $shouldApplyFont = false;

    /** @var bool Whether the text should wrap in the cell (useful for long or multi-lines text) */
    protected $shouldWrapText = false;
    /** @var bool Whether the wrap text property was set */
    protected $hasSetWrapText = false;

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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
     */
    public function shouldWrapText()
    {
        return $this->shouldWrapText;
    }

    /**
     * @return Style
     */
    public function setShouldWrapText()
    {
        $this->shouldWrapText = true;
        $this->hasSetWrapText = true;
        return $this;
    }

    /**
     * @return bool Whether specific font properties should be applied
     */
    public function shouldApplyFont()
    {
        return $this->shouldApplyFont;
    }

    /**
     * Serializes the style for future comparison with other styles.
     * The ID is excluded from the comparison, as we only care about
     * actual style properties.
     *
     * @return string The serialized style
     */
    public function serialize()
    {
        // In order to be able to properly compare style, set static ID value
        $currentId = $this->id;
        $this->setId(0);

        $serializedStyle = serialize($this);

        $this->setId($currentId);

        return $serializedStyle;
    }

    /**
     * Merges the current style with the given style, using the given style as a base. This means that:
     *   - if current style and base style both have property A set, use current style property's value
     *   - if current style has property A set but base style does not, use current style property's value
     *   - if base style has property A set but current style does not, use base style property's value
     *
     * @NOTE: This function returns a new style.
     *
     * @param Style $baseStyle
     * @return Style New style corresponding to the merge of the 2 styles
     */
    public function mergeWith($baseStyle)
    {
        $mergedStyle = clone $this;

        if (!$this->hasSetFontBold && $baseStyle->isFontBold()) {
            $mergedStyle->setFontBold();
        }
        if (!$this->hasSetFontItalic && $baseStyle->isFontItalic()) {
            $mergedStyle->setFontItalic();
        }
        if (!$this->hasSetFontUnderline && $baseStyle->isFontUnderline()) {
            $mergedStyle->setFontUnderline();
        }
        if (!$this->hasSetFontStrikethrough && $baseStyle->isFontStrikethrough()) {
            $mergedStyle->setFontStrikethrough();
        }
        if (!$this->hasSetFontSize && $baseStyle->getFontSize() !== self::DEFAULT_FONT_SIZE) {
            $mergedStyle->setFontSize($baseStyle->getFontSize());
        }
        if (!$this->hasSetFontColor && $baseStyle->getFontColor() !== self::DEFAULT_FONT_COLOR) {
            $mergedStyle->setFontColor($baseStyle->getFontColor());
        }
        if (!$this->hasSetFontName && $baseStyle->getFontName() !== self::DEFAULT_FONT_NAME) {
            $mergedStyle->setFontName($baseStyle->getFontName());
        }
        if (!$this->hasSetWrapText && $baseStyle->shouldWrapText()) {
            $mergedStyle->setShouldWrapText();
        }

        return $mergedStyle;
    }
}
