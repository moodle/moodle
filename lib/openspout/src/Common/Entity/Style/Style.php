<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Common\Exception\InvalidArgumentException;

/**
 * Represents a style to be applied to a cell.
 */
final class Style
{
    /**
     * Default values.
     */
    public const DEFAULT_FONT_SIZE = 11;
    public const DEFAULT_FONT_COLOR = Color::BLACK;
    public const DEFAULT_FONT_NAME = 'Arial';

    /** @var int Style ID */
    private int $id = -1;

    /** @var bool Whether the font should be bold */
    private bool $fontBold = false;

    /** @var bool Whether the bold property was set */
    private bool $hasSetFontBold = false;

    /** @var bool Whether the font should be italic */
    private bool $fontItalic = false;

    /** @var bool Whether the italic property was set */
    private bool $hasSetFontItalic = false;

    /** @var bool Whether the font should be underlined */
    private bool $fontUnderline = false;

    /** @var bool Whether the underline property was set */
    private bool $hasSetFontUnderline = false;

    /** @var bool Whether the font should be struck through */
    private bool $fontStrikethrough = false;

    /** @var bool Whether the strikethrough property was set */
    private bool $hasSetFontStrikethrough = false;

    /** @var int Font size */
    private int $fontSize = self::DEFAULT_FONT_SIZE;

    /** @var bool Whether the font size property was set */
    private bool $hasSetFontSize = false;

    /** @var string Font color */
    private string $fontColor = self::DEFAULT_FONT_COLOR;

    /** @var bool Whether the font color property was set */
    private bool $hasSetFontColor = false;

    /** @var string Font name */
    private string $fontName = self::DEFAULT_FONT_NAME;

    /** @var bool Whether the font name property was set */
    private bool $hasSetFontName = false;

    /** @var bool Whether specific font properties should be applied */
    private bool $shouldApplyFont = false;

    /** @var bool Whether specific cell alignment should be applied */
    private bool $shouldApplyCellAlignment = false;

    /** @var string Cell alignment */
    private string $cellAlignment;

    /** @var bool Whether the cell alignment property was set */
    private bool $hasSetCellAlignment = false;

    /** @var bool Whether specific cell vertical alignment should be applied */
    private bool $shouldApplyCellVerticalAlignment = false;

    /** @var string Cell vertical alignment */
    private string $cellVerticalAlignment;

    /** @var bool Whether the cell vertical alignment property was set */
    private bool $hasSetCellVerticalAlignment = false;

    /** @var bool Whether the text should wrap in the cell (useful for long or multi-lines text) */
    private bool $shouldWrapText = false;

    /** @var bool Whether the wrap text property was set */
    private bool $hasSetWrapText = false;

    /** @var bool Whether the cell should shrink to fit to content */
    private bool $shouldShrinkToFit = false;

    /** @var bool Whether the shouldShrinkToFit text property was set */
    private bool $hasSetShrinkToFit = false;

    private ?Border $border = null;

    /** @var null|string Background color */
    private ?string $backgroundColor = null;

    /** @var null|string Format */
    private ?string $format = null;

    private bool $isRegistered = false;

    private bool $isEmpty = true;

    public function __sleep(): array
    {
        $vars = get_object_vars($this);
        unset($vars['id'], $vars['isRegistered']);

        return array_keys($vars);
    }

    public function getId(): int
    {
        \assert(0 <= $this->id);

        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBorder(): ?Border
    {
        return $this->border;
    }

    public function setBorder(Border $border): self
    {
        $this->border = $border;
        $this->isEmpty = false;

        return $this;
    }

    public function isFontBold(): bool
    {
        return $this->fontBold;
    }

    public function setFontBold(): self
    {
        $this->fontBold = true;
        $this->hasSetFontBold = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontBold(): bool
    {
        return $this->hasSetFontBold;
    }

    public function isFontItalic(): bool
    {
        return $this->fontItalic;
    }

    public function setFontItalic(): self
    {
        $this->fontItalic = true;
        $this->hasSetFontItalic = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontItalic(): bool
    {
        return $this->hasSetFontItalic;
    }

    public function isFontUnderline(): bool
    {
        return $this->fontUnderline;
    }

    public function setFontUnderline(): self
    {
        $this->fontUnderline = true;
        $this->hasSetFontUnderline = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontUnderline(): bool
    {
        return $this->hasSetFontUnderline;
    }

    public function isFontStrikethrough(): bool
    {
        return $this->fontStrikethrough;
    }

    public function setFontStrikethrough(): self
    {
        $this->fontStrikethrough = true;
        $this->hasSetFontStrikethrough = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontStrikethrough(): bool
    {
        return $this->hasSetFontStrikethrough;
    }

    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize Font size, in pixels
     */
    public function setFontSize(int $fontSize): self
    {
        $this->fontSize = $fontSize;
        $this->hasSetFontSize = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontSize(): bool
    {
        return $this->hasSetFontSize;
    }

    public function getFontColor(): string
    {
        return $this->fontColor;
    }

    /**
     * Sets the font color.
     *
     * @param string $fontColor ARGB color (@see Color)
     */
    public function setFontColor(string $fontColor): self
    {
        $this->fontColor = $fontColor;
        $this->hasSetFontColor = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontColor(): bool
    {
        return $this->hasSetFontColor;
    }

    public function getFontName(): string
    {
        return $this->fontName;
    }

    /**
     * @param string $fontName Name of the font to use
     */
    public function setFontName(string $fontName): self
    {
        $this->fontName = $fontName;
        $this->hasSetFontName = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontName(): bool
    {
        return $this->hasSetFontName;
    }

    public function getCellAlignment(): string
    {
        return $this->cellAlignment;
    }

    public function getCellVerticalAlignment(): string
    {
        return $this->cellVerticalAlignment;
    }

    /**
     * @param string $cellAlignment The cell alignment
     */
    public function setCellAlignment(string $cellAlignment): self
    {
        if (!CellAlignment::isValid($cellAlignment)) {
            throw new InvalidArgumentException('Invalid cell alignment value');
        }

        $this->cellAlignment = $cellAlignment;
        $this->hasSetCellAlignment = true;
        $this->shouldApplyCellAlignment = true;
        $this->isEmpty = false;

        return $this;
    }

    /**
     * @param string $cellVerticalAlignment The cell vertical alignment
     */
    public function setCellVerticalAlignment(string $cellVerticalAlignment): self
    {
        if (!CellVerticalAlignment::isValid($cellVerticalAlignment)) {
            throw new InvalidArgumentException('Invalid cell vertical alignment value');
        }

        $this->cellVerticalAlignment = $cellVerticalAlignment;
        $this->hasSetCellVerticalAlignment = true;
        $this->shouldApplyCellVerticalAlignment = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetCellAlignment(): bool
    {
        return $this->hasSetCellAlignment;
    }

    public function hasSetCellVerticalAlignment(): bool
    {
        return $this->hasSetCellVerticalAlignment;
    }

    /**
     * @return bool Whether specific cell alignment should be applied
     */
    public function shouldApplyCellAlignment(): bool
    {
        return $this->shouldApplyCellAlignment;
    }

    public function shouldApplyCellVerticalAlignment(): bool
    {
        return $this->shouldApplyCellVerticalAlignment;
    }

    public function shouldWrapText(): bool
    {
        return $this->shouldWrapText;
    }

    /**
     * @param bool $shouldWrap Should the text be wrapped
     */
    public function setShouldWrapText(bool $shouldWrap = true): self
    {
        $this->shouldWrapText = $shouldWrap;
        $this->hasSetWrapText = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetWrapText(): bool
    {
        return $this->hasSetWrapText;
    }

    /**
     * @return bool Whether specific font properties should be applied
     */
    public function shouldApplyFont(): bool
    {
        return $this->shouldApplyFont;
    }

    /**
     * Sets the background color.
     *
     * @param string $color ARGB color (@see Color)
     */
    public function setBackgroundColor(string $color): self
    {
        $this->backgroundColor = $color;
        $this->isEmpty = false;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    /**
     * Sets format.
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        $this->isEmpty = false;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    public function markAsRegistered(?int $id): void
    {
        $this->setId($id);
        $this->isRegistered = true;
    }

    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    /**
     * Sets should shrink to fit.
     */
    public function setShouldShrinkToFit(bool $shrinkToFit = true): self
    {
        $this->hasSetShrinkToFit = true;
        $this->shouldShrinkToFit = $shrinkToFit;

        return $this;
    }

    /**
     * @return bool Whether format should be applied
     */
    public function shouldShrinkToFit(): bool
    {
        return $this->shouldShrinkToFit;
    }

    public function hasSetShrinkToFit(): bool
    {
        return $this->hasSetShrinkToFit;
    }
}
