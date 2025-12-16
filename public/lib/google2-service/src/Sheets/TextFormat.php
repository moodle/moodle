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

class TextFormat extends \Google\Model
{
  /**
   * True if the text is bold.
   *
   * @var bool
   */
  public $bold;
  /**
   * The font family.
   *
   * @var string
   */
  public $fontFamily;
  /**
   * The size of the font.
   *
   * @var int
   */
  public $fontSize;
  protected $foregroundColorType = Color::class;
  protected $foregroundColorDataType = '';
  protected $foregroundColorStyleType = ColorStyle::class;
  protected $foregroundColorStyleDataType = '';
  /**
   * True if the text is italicized.
   *
   * @var bool
   */
  public $italic;
  protected $linkType = Link::class;
  protected $linkDataType = '';
  /**
   * True if the text has a strikethrough.
   *
   * @var bool
   */
  public $strikethrough;
  /**
   * True if the text is underlined.
   *
   * @var bool
   */
  public $underline;

  /**
   * True if the text is bold.
   *
   * @param bool $bold
   */
  public function setBold($bold)
  {
    $this->bold = $bold;
  }
  /**
   * @return bool
   */
  public function getBold()
  {
    return $this->bold;
  }
  /**
   * The font family.
   *
   * @param string $fontFamily
   */
  public function setFontFamily($fontFamily)
  {
    $this->fontFamily = $fontFamily;
  }
  /**
   * @return string
   */
  public function getFontFamily()
  {
    return $this->fontFamily;
  }
  /**
   * The size of the font.
   *
   * @param int $fontSize
   */
  public function setFontSize($fontSize)
  {
    $this->fontSize = $fontSize;
  }
  /**
   * @return int
   */
  public function getFontSize()
  {
    return $this->fontSize;
  }
  /**
   * The foreground color of the text. Deprecated: Use foreground_color_style.
   *
   * @deprecated
   * @param Color $foregroundColor
   */
  public function setForegroundColor(Color $foregroundColor)
  {
    $this->foregroundColor = $foregroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getForegroundColor()
  {
    return $this->foregroundColor;
  }
  /**
   * The foreground color of the text. If foreground_color is also set, this
   * field takes precedence.
   *
   * @param ColorStyle $foregroundColorStyle
   */
  public function setForegroundColorStyle(ColorStyle $foregroundColorStyle)
  {
    $this->foregroundColorStyle = $foregroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getForegroundColorStyle()
  {
    return $this->foregroundColorStyle;
  }
  /**
   * True if the text is italicized.
   *
   * @param bool $italic
   */
  public function setItalic($italic)
  {
    $this->italic = $italic;
  }
  /**
   * @return bool
   */
  public function getItalic()
  {
    return $this->italic;
  }
  /**
   * The link destination of the text, if any. Setting the link field in a
   * TextFormatRun will clear the cell's existing links or a cell-level link set
   * in the same request. When a link is set, the text foreground color will be
   * set to the default link color and the text will be underlined. If these
   * fields are modified in the same request, those values will be used instead
   * of the link defaults.
   *
   * @param Link $link
   */
  public function setLink(Link $link)
  {
    $this->link = $link;
  }
  /**
   * @return Link
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * True if the text has a strikethrough.
   *
   * @param bool $strikethrough
   */
  public function setStrikethrough($strikethrough)
  {
    $this->strikethrough = $strikethrough;
  }
  /**
   * @return bool
   */
  public function getStrikethrough()
  {
    return $this->strikethrough;
  }
  /**
   * True if the text is underlined.
   *
   * @param bool $underline
   */
  public function setUnderline($underline)
  {
    $this->underline = $underline;
  }
  /**
   * @return bool
   */
  public function getUnderline()
  {
    return $this->underline;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextFormat::class, 'Google_Service_Sheets_TextFormat');
