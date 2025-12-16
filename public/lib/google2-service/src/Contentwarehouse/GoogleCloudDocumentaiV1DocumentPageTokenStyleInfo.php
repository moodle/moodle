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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentPageTokenStyleInfo extends \Google\Model
{
  protected $backgroundColorType = GoogleTypeColor::class;
  protected $backgroundColorDataType = '';
  /**
   * Whether the text is bold (equivalent to font_weight is at least `700`).
   *
   * @var bool
   */
  public $bold;
  /**
   * Font size in points (`1` point is `¹⁄₇₂` inches).
   *
   * @var int
   */
  public $fontSize;
  /**
   * Name or style of the font.
   *
   * @var string
   */
  public $fontType;
  /**
   * TrueType weight on a scale `100` (thin) to `1000` (ultra-heavy). Normal is
   * `400`, bold is `700`.
   *
   * @var int
   */
  public $fontWeight;
  /**
   * Whether the text is handwritten.
   *
   * @var bool
   */
  public $handwritten;
  /**
   * Whether the text is italic.
   *
   * @var bool
   */
  public $italic;
  /**
   * Letter spacing in points.
   *
   * @var 
   */
  public $letterSpacing;
  /**
   * Font size in pixels, equal to _unrounded font_size_ * _resolution_ ÷
   * `72.0`.
   *
   * @var 
   */
  public $pixelFontSize;
  /**
   * Whether the text is in small caps. This feature is not supported yet.
   *
   * @var bool
   */
  public $smallcaps;
  /**
   * Whether the text is strikethrough. This feature is not supported yet.
   *
   * @var bool
   */
  public $strikeout;
  /**
   * Whether the text is a subscript. This feature is not supported yet.
   *
   * @var bool
   */
  public $subscript;
  /**
   * Whether the text is a superscript. This feature is not supported yet.
   *
   * @var bool
   */
  public $superscript;
  protected $textColorType = GoogleTypeColor::class;
  protected $textColorDataType = '';
  /**
   * Whether the text is underlined.
   *
   * @var bool
   */
  public $underlined;

  /**
   * Color of the background.
   *
   * @param GoogleTypeColor $backgroundColor
   */
  public function setBackgroundColor(GoogleTypeColor $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * Whether the text is bold (equivalent to font_weight is at least `700`).
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
   * Font size in points (`1` point is `¹⁄₇₂` inches).
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
   * Name or style of the font.
   *
   * @param string $fontType
   */
  public function setFontType($fontType)
  {
    $this->fontType = $fontType;
  }
  /**
   * @return string
   */
  public function getFontType()
  {
    return $this->fontType;
  }
  /**
   * TrueType weight on a scale `100` (thin) to `1000` (ultra-heavy). Normal is
   * `400`, bold is `700`.
   *
   * @param int $fontWeight
   */
  public function setFontWeight($fontWeight)
  {
    $this->fontWeight = $fontWeight;
  }
  /**
   * @return int
   */
  public function getFontWeight()
  {
    return $this->fontWeight;
  }
  /**
   * Whether the text is handwritten.
   *
   * @param bool $handwritten
   */
  public function setHandwritten($handwritten)
  {
    $this->handwritten = $handwritten;
  }
  /**
   * @return bool
   */
  public function getHandwritten()
  {
    return $this->handwritten;
  }
  /**
   * Whether the text is italic.
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
  public function setLetterSpacing($letterSpacing)
  {
    $this->letterSpacing = $letterSpacing;
  }
  public function getLetterSpacing()
  {
    return $this->letterSpacing;
  }
  public function setPixelFontSize($pixelFontSize)
  {
    $this->pixelFontSize = $pixelFontSize;
  }
  public function getPixelFontSize()
  {
    return $this->pixelFontSize;
  }
  /**
   * Whether the text is in small caps. This feature is not supported yet.
   *
   * @param bool $smallcaps
   */
  public function setSmallcaps($smallcaps)
  {
    $this->smallcaps = $smallcaps;
  }
  /**
   * @return bool
   */
  public function getSmallcaps()
  {
    return $this->smallcaps;
  }
  /**
   * Whether the text is strikethrough. This feature is not supported yet.
   *
   * @param bool $strikeout
   */
  public function setStrikeout($strikeout)
  {
    $this->strikeout = $strikeout;
  }
  /**
   * @return bool
   */
  public function getStrikeout()
  {
    return $this->strikeout;
  }
  /**
   * Whether the text is a subscript. This feature is not supported yet.
   *
   * @param bool $subscript
   */
  public function setSubscript($subscript)
  {
    $this->subscript = $subscript;
  }
  /**
   * @return bool
   */
  public function getSubscript()
  {
    return $this->subscript;
  }
  /**
   * Whether the text is a superscript. This feature is not supported yet.
   *
   * @param bool $superscript
   */
  public function setSuperscript($superscript)
  {
    $this->superscript = $superscript;
  }
  /**
   * @return bool
   */
  public function getSuperscript()
  {
    return $this->superscript;
  }
  /**
   * Color of the text.
   *
   * @param GoogleTypeColor $textColor
   */
  public function setTextColor(GoogleTypeColor $textColor)
  {
    $this->textColor = $textColor;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getTextColor()
  {
    return $this->textColor;
  }
  /**
   * Whether the text is underlined.
   *
   * @param bool $underlined
   */
  public function setUnderlined($underlined)
  {
    $this->underlined = $underlined;
  }
  /**
   * @return bool
   */
  public function getUnderlined()
  {
    return $this->underlined;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageTokenStyleInfo::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentPageTokenStyleInfo');
