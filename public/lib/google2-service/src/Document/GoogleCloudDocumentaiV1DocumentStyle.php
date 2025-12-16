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

class GoogleCloudDocumentaiV1DocumentStyle extends \Google\Model
{
  protected $backgroundColorType = GoogleTypeColor::class;
  protected $backgroundColorDataType = '';
  protected $colorType = GoogleTypeColor::class;
  protected $colorDataType = '';
  /**
   * Font family such as `Arial`, `Times New Roman`.
   * https://www.w3schools.com/cssref/pr_font_font-family.asp
   *
   * @var string
   */
  public $fontFamily;
  protected $fontSizeType = GoogleCloudDocumentaiV1DocumentStyleFontSize::class;
  protected $fontSizeDataType = '';
  /**
   * [Font weight](https://www.w3schools.com/cssref/pr_font_weight.asp).
   * Possible values are `normal`, `bold`, `bolder`, and `lighter`.
   *
   * @var string
   */
  public $fontWeight;
  protected $textAnchorType = GoogleCloudDocumentaiV1DocumentTextAnchor::class;
  protected $textAnchorDataType = '';
  /**
   * [Text decoration](https://www.w3schools.com/cssref/pr_text_text-
   * decoration.asp). Follows CSS standard.
   *
   * @var string
   */
  public $textDecoration;
  /**
   * [Text style](https://www.w3schools.com/cssref/pr_font_font-style.asp).
   * Possible values are `normal`, `italic`, and `oblique`.
   *
   * @var string
   */
  public $textStyle;

  /**
   * Text background color.
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
   * Text color.
   *
   * @param GoogleTypeColor $color
   */
  public function setColor(GoogleTypeColor $color)
  {
    $this->color = $color;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * Font family such as `Arial`, `Times New Roman`.
   * https://www.w3schools.com/cssref/pr_font_font-family.asp
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
   * Font size.
   *
   * @param GoogleCloudDocumentaiV1DocumentStyleFontSize $fontSize
   */
  public function setFontSize(GoogleCloudDocumentaiV1DocumentStyleFontSize $fontSize)
  {
    $this->fontSize = $fontSize;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentStyleFontSize
   */
  public function getFontSize()
  {
    return $this->fontSize;
  }
  /**
   * [Font weight](https://www.w3schools.com/cssref/pr_font_weight.asp).
   * Possible values are `normal`, `bold`, `bolder`, and `lighter`.
   *
   * @param string $fontWeight
   */
  public function setFontWeight($fontWeight)
  {
    $this->fontWeight = $fontWeight;
  }
  /**
   * @return string
   */
  public function getFontWeight()
  {
    return $this->fontWeight;
  }
  /**
   * Text anchor indexing into the Document.text.
   *
   * @param GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor
   */
  public function setTextAnchor(GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor)
  {
    $this->textAnchor = $textAnchor;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentTextAnchor
   */
  public function getTextAnchor()
  {
    return $this->textAnchor;
  }
  /**
   * [Text decoration](https://www.w3schools.com/cssref/pr_text_text-
   * decoration.asp). Follows CSS standard.
   *
   * @param string $textDecoration
   */
  public function setTextDecoration($textDecoration)
  {
    $this->textDecoration = $textDecoration;
  }
  /**
   * @return string
   */
  public function getTextDecoration()
  {
    return $this->textDecoration;
  }
  /**
   * [Text style](https://www.w3schools.com/cssref/pr_font_font-style.asp).
   * Possible values are `normal`, `italic`, and `oblique`.
   *
   * @param string $textStyle
   */
  public function setTextStyle($textStyle)
  {
    $this->textStyle = $textStyle;
  }
  /**
   * @return string
   */
  public function getTextStyle()
  {
    return $this->textStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentStyle::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentStyle');
