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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta2DocumentPageTokenStyleInfo extends \Google\Model
{
  protected $backgroundColorType = GoogleTypeColor::class;
  protected $backgroundColorDataType = '';
  /**
   * @var bool
   */
  public $bold;
  /**
   * @var int
   */
  public $fontSize;
  /**
   * @var string
   */
  public $fontType;
  /**
   * @var int
   */
  public $fontWeight;
  /**
   * @var bool
   */
  public $handwritten;
  /**
   * @var bool
   */
  public $italic;
  public $letterSpacing;
  public $pixelFontSize;
  /**
   * @var bool
   */
  public $smallcaps;
  /**
   * @var bool
   */
  public $strikeout;
  /**
   * @var bool
   */
  public $subscript;
  /**
   * @var bool
   */
  public $superscript;
  protected $textColorType = GoogleTypeColor::class;
  protected $textColorDataType = '';
  /**
   * @var bool
   */
  public $underlined;

  /**
   * @param GoogleTypeColor
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
   * @param bool
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
   * @param int
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
   * @param string
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
   * @param int
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
   * @param bool
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
   * @param bool
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
   * @param bool
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
   * @param bool
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
   * @param bool
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
   * @param bool
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
   * @param GoogleTypeColor
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
   * @param bool
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
class_alias(GoogleCloudDocumentaiV1beta2DocumentPageTokenStyleInfo::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta2DocumentPageTokenStyleInfo');
