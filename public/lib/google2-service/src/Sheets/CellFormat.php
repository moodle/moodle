<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Sheets;

class CellFormat extends \Google\Model
{
  /**
   * The horizontal alignment is not specified. Do not use this.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGN_UNSPECIFIED = 'HORIZONTAL_ALIGN_UNSPECIFIED';
  /**
   * The text is explicitly aligned to the left of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_LEFT = 'LEFT';
  /**
   * The text is explicitly aligned to the center of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * The text is explicitly aligned to the right of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_RIGHT = 'RIGHT';
  /**
   * The default value: the hyperlink is rendered. Do not use this.
   */
  public const HYPERLINK_DISPLAY_TYPE_HYPERLINK_DISPLAY_TYPE_UNSPECIFIED = 'HYPERLINK_DISPLAY_TYPE_UNSPECIFIED';
  /**
   * A hyperlink should be explicitly rendered.
   */
  public const HYPERLINK_DISPLAY_TYPE_LINKED = 'LINKED';
  /**
   * A hyperlink should not be rendered.
   */
  public const HYPERLINK_DISPLAY_TYPE_PLAIN_TEXT = 'PLAIN_TEXT';
  /**
   * The text direction is not specified. Do not use this.
   */
  public const TEXT_DIRECTION_TEXT_DIRECTION_UNSPECIFIED = 'TEXT_DIRECTION_UNSPECIFIED';
  /**
   * The text direction of left-to-right was set by the user.
   */
  public const TEXT_DIRECTION_LEFT_TO_RIGHT = 'LEFT_TO_RIGHT';
  /**
   * The text direction of right-to-left was set by the user.
   */
  public const TEXT_DIRECTION_RIGHT_TO_LEFT = 'RIGHT_TO_LEFT';
  /**
   * The vertical alignment is not specified. Do not use this.
   */
  public const VERTICAL_ALIGNMENT_VERTICAL_ALIGN_UNSPECIFIED = 'VERTICAL_ALIGN_UNSPECIFIED';
  /**
   * The text is explicitly aligned to the top of the cell.
   */
  public const VERTICAL_ALIGNMENT_TOP = 'TOP';
  /**
   * The text is explicitly aligned to the middle of the cell.
   */
  public const VERTICAL_ALIGNMENT_MIDDLE = 'MIDDLE';
  /**
   * The text is explicitly aligned to the bottom of the cell.
   */
  public const VERTICAL_ALIGNMENT_BOTTOM = 'BOTTOM';
  /**
   * The default value, do not use.
   */
  public const WRAP_STRATEGY_WRAP_STRATEGY_UNSPECIFIED = 'WRAP_STRATEGY_UNSPECIFIED';
  /**
   * Lines that are longer than the cell width will be written in the next cell
   * over, so long as that cell is empty. If the next cell over is non-empty,
   * this behaves the same as `CLIP`. The text will never wrap to the next line
   * unless the user manually inserts a new line. Example: | First sentence. | |
   * Manual newline that is very long. <- Text continues into next cell | Next
   * newline. |
   */
  public const WRAP_STRATEGY_OVERFLOW_CELL = 'OVERFLOW_CELL';
  /**
   * This wrap strategy represents the old Google Sheets wrap strategy where
   * words that are longer than a line are clipped rather than broken. This
   * strategy is not supported on all platforms and is being phased out.
   * Example: | Cell has a | | loooooooooo| <- Word is clipped. | word. |
   */
  public const WRAP_STRATEGY_LEGACY_WRAP = 'LEGACY_WRAP';
  /**
   * Lines that are longer than the cell width will be clipped. The text will
   * never wrap to the next line unless the user manually inserts a new line.
   * Example: | First sentence. | | Manual newline t| <- Text is clipped | Next
   * newline. |
   */
  public const WRAP_STRATEGY_CLIP = 'CLIP';
  /**
   * Words that are longer than a line are wrapped at the character level rather
   * than clipped. Example: | Cell has a | | loooooooooo| <- Word is broken. |
   * ong word. |
   */
  public const WRAP_STRATEGY_WRAP = 'WRAP';
  protected $backgroundColorType = Color::class;
  protected $backgroundColorDataType = '';
  protected $backgroundColorStyleType = ColorStyle::class;
  protected $backgroundColorStyleDataType = '';
  protected $bordersType = Borders::class;
  protected $bordersDataType = '';
  /**
   * The horizontal alignment of the value in the cell.
   *
   * @var string
   */
  public $horizontalAlignment;
  /**
   * If one exists, how a hyperlink should be displayed in the cell.
   *
   * @var string
   */
  public $hyperlinkDisplayType;
  protected $numberFormatType = NumberFormat::class;
  protected $numberFormatDataType = '';
  protected $paddingType = Padding::class;
  protected $paddingDataType = '';
  /**
   * The direction of the text in the cell.
   *
   * @var string
   */
  public $textDirection;
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';
  protected $textRotationType = TextRotation::class;
  protected $textRotationDataType = '';
  /**
   * The vertical alignment of the value in the cell.
   *
   * @var string
   */
  public $verticalAlignment;
  /**
   * The wrap strategy for the value in the cell.
   *
   * @var string
   */
  public $wrapStrategy;

  /**
   * The background color of the cell. Deprecated: Use background_color_style.
   *
   * @deprecated
   * @param Color $backgroundColor
   */
  public function setBackgroundColor(Color $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The background color of the cell. If background_color is also set, this
   * field takes precedence.
   *
   * @param ColorStyle $backgroundColorStyle
   */
  public function setBackgroundColorStyle(ColorStyle $backgroundColorStyle)
  {
    $this->backgroundColorStyle = $backgroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getBackgroundColorStyle()
  {
    return $this->backgroundColorStyle;
  }
  /**
   * The borders of the cell.
   *
   * @param Borders $borders
   */
  public function setBorders(Borders $borders)
  {
    $this->borders = $borders;
  }
  /**
   * @return Borders
   */
  public function getBorders()
  {
    return $this->borders;
  }
  /**
   * The horizontal alignment of the value in the cell.
   *
   * Accepted values: HORIZONTAL_ALIGN_UNSPECIFIED, LEFT, CENTER, RIGHT
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
  /**
   * If one exists, how a hyperlink should be displayed in the cell.
   *
   * Accepted values: HYPERLINK_DISPLAY_TYPE_UNSPECIFIED, LINKED, PLAIN_TEXT
   *
   * @param self::HYPERLINK_DISPLAY_TYPE_* $hyperlinkDisplayType
   */
  public function setHyperlinkDisplayType($hyperlinkDisplayType)
  {
    $this->hyperlinkDisplayType = $hyperlinkDisplayType;
  }
  /**
   * @return self::HYPERLINK_DISPLAY_TYPE_*
   */
  public function getHyperlinkDisplayType()
  {
    return $this->hyperlinkDisplayType;
  }
  /**
   * A format describing how number values should be represented to the user.
   *
   * @param NumberFormat $numberFormat
   */
  public function setNumberFormat(NumberFormat $numberFormat)
  {
    $this->numberFormat = $numberFormat;
  }
  /**
   * @return NumberFormat
   */
  public function getNumberFormat()
  {
    return $this->numberFormat;
  }
  /**
   * The padding of the cell.
   *
   * @param Padding $padding
   */
  public function setPadding(Padding $padding)
  {
    $this->padding = $padding;
  }
  /**
   * @return Padding
   */
  public function getPadding()
  {
    return $this->padding;
  }
  /**
   * The direction of the text in the cell.
   *
   * Accepted values: TEXT_DIRECTION_UNSPECIFIED, LEFT_TO_RIGHT, RIGHT_TO_LEFT
   *
   * @param self::TEXT_DIRECTION_* $textDirection
   */
  public function setTextDirection($textDirection)
  {
    $this->textDirection = $textDirection;
  }
  /**
   * @return self::TEXT_DIRECTION_*
   */
  public function getTextDirection()
  {
    return $this->textDirection;
  }
  /**
   * The format of the text in the cell (unless overridden by a format run).
   * Setting a cell-level link here clears the cell's existing links. Setting
   * the link field in a TextFormatRun takes precedence over the cell-level
   * link.
   *
   * @param TextFormat $textFormat
   */
  public function setTextFormat(TextFormat $textFormat)
  {
    $this->textFormat = $textFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTextFormat()
  {
    return $this->textFormat;
  }
  /**
   * The rotation applied to text in the cell.
   *
   * @param TextRotation $textRotation
   */
  public function setTextRotation(TextRotation $textRotation)
  {
    $this->textRotation = $textRotation;
  }
  /**
   * @return TextRotation
   */
  public function getTextRotation()
  {
    return $this->textRotation;
  }
  /**
   * The vertical alignment of the value in the cell.
   *
   * Accepted values: VERTICAL_ALIGN_UNSPECIFIED, TOP, MIDDLE, BOTTOM
   *
   * @param self::VERTICAL_ALIGNMENT_* $verticalAlignment
   */
  public function setVerticalAlignment($verticalAlignment)
  {
    $this->verticalAlignment = $verticalAlignment;
  }
  /**
   * @return self::VERTICAL_ALIGNMENT_*
   */
  public function getVerticalAlignment()
  {
    return $this->verticalAlignment;
  }
  /**
   * The wrap strategy for the value in the cell.
   *
   * Accepted values: WRAP_STRATEGY_UNSPECIFIED, OVERFLOW_CELL, LEGACY_WRAP,
   * CLIP, WRAP
   *
   * @param self::WRAP_STRATEGY_* $wrapStrategy
   */
  public function setWrapStrategy($wrapStrategy)
  {
    $this->wrapStrategy = $wrapStrategy;
  }
  /**
   * @return self::WRAP_STRATEGY_*
   */
  public function getWrapStrategy()
  {
    return $this->wrapStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CellFormat::class, 'Google_Service_Sheets_CellFormat');
