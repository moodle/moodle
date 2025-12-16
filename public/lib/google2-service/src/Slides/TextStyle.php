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

namespace Google\Service\Slides;

class TextStyle extends \Google\Model
{
  /**
   * The text's baseline offset is inherited from the parent.
   */
  public const BASELINE_OFFSET_BASELINE_OFFSET_UNSPECIFIED = 'BASELINE_OFFSET_UNSPECIFIED';
  /**
   * The text is not vertically offset.
   */
  public const BASELINE_OFFSET_NONE = 'NONE';
  /**
   * The text is vertically offset upwards (superscript).
   */
  public const BASELINE_OFFSET_SUPERSCRIPT = 'SUPERSCRIPT';
  /**
   * The text is vertically offset downwards (subscript).
   */
  public const BASELINE_OFFSET_SUBSCRIPT = 'SUBSCRIPT';
  protected $backgroundColorType = OptionalColor::class;
  protected $backgroundColorDataType = '';
  /**
   * The text's vertical offset from its normal position. Text with
   * `SUPERSCRIPT` or `SUBSCRIPT` baseline offsets is automatically rendered in
   * a smaller font size, computed based on the `font_size` field. The
   * `font_size` itself is not affected by changes in this field.
   *
   * @var string
   */
  public $baselineOffset;
  /**
   * Whether or not the text is rendered as bold.
   *
   * @var bool
   */
  public $bold;
  /**
   * The font family of the text. The font family can be any font from the Font
   * menu in Slides or from [Google Fonts] (https://fonts.google.com/). If the
   * font name is unrecognized, the text is rendered in `Arial`. Some fonts can
   * affect the weight of the text. If an update request specifies values for
   * both `font_family` and `bold`, the explicitly-set `bold` value is used.
   *
   * @var string
   */
  public $fontFamily;
  protected $fontSizeType = Dimension::class;
  protected $fontSizeDataType = '';
  protected $foregroundColorType = OptionalColor::class;
  protected $foregroundColorDataType = '';
  /**
   * Whether or not the text is italicized.
   *
   * @var bool
   */
  public $italic;
  protected $linkType = Link::class;
  protected $linkDataType = '';
  /**
   * Whether or not the text is in small capital letters.
   *
   * @var bool
   */
  public $smallCaps;
  /**
   * Whether or not the text is struck through.
   *
   * @var bool
   */
  public $strikethrough;
  /**
   * Whether or not the text is underlined.
   *
   * @var bool
   */
  public $underline;
  protected $weightedFontFamilyType = WeightedFontFamily::class;
  protected $weightedFontFamilyDataType = '';

  /**
   * The background color of the text. If set, the color is either opaque or
   * transparent, depending on if the `opaque_color` field in it is set.
   *
   * @param OptionalColor $backgroundColor
   */
  public function setBackgroundColor(OptionalColor $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return OptionalColor
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The text's vertical offset from its normal position. Text with
   * `SUPERSCRIPT` or `SUBSCRIPT` baseline offsets is automatically rendered in
   * a smaller font size, computed based on the `font_size` field. The
   * `font_size` itself is not affected by changes in this field.
   *
   * Accepted values: BASELINE_OFFSET_UNSPECIFIED, NONE, SUPERSCRIPT, SUBSCRIPT
   *
   * @param self::BASELINE_OFFSET_* $baselineOffset
   */
  public function setBaselineOffset($baselineOffset)
  {
    $this->baselineOffset = $baselineOffset;
  }
  /**
   * @return self::BASELINE_OFFSET_*
   */
  public function getBaselineOffset()
  {
    return $this->baselineOffset;
  }
  /**
   * Whether or not the text is rendered as bold.
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
   * The font family of the text. The font family can be any font from the Font
   * menu in Slides or from [Google Fonts] (https://fonts.google.com/). If the
   * font name is unrecognized, the text is rendered in `Arial`. Some fonts can
   * affect the weight of the text. If an update request specifies values for
   * both `font_family` and `bold`, the explicitly-set `bold` value is used.
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
   * The size of the text's font. When read, the `font_size` will specified in
   * points.
   *
   * @param Dimension $fontSize
   */
  public function setFontSize(Dimension $fontSize)
  {
    $this->fontSize = $fontSize;
  }
  /**
   * @return Dimension
   */
  public function getFontSize()
  {
    return $this->fontSize;
  }
  /**
   * The color of the text itself. If set, the color is either opaque or
   * transparent, depending on if the `opaque_color` field in it is set.
   *
   * @param OptionalColor $foregroundColor
   */
  public function setForegroundColor(OptionalColor $foregroundColor)
  {
    $this->foregroundColor = $foregroundColor;
  }
  /**
   * @return OptionalColor
   */
  public function getForegroundColor()
  {
    return $this->foregroundColor;
  }
  /**
   * Whether or not the text is italicized.
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
   * The hyperlink destination of the text. If unset, there is no link. Links
   * are not inherited from parent text. Changing the link in an update request
   * causes some other changes to the text style of the range: * When setting a
   * link, the text foreground color will be set to ThemeColorType.HYPERLINK and
   * the text will be underlined. If these fields are modified in the same
   * request, those values will be used instead of the link defaults. * Setting
   * a link on a text range that overlaps with an existing link will also update
   * the existing link to point to the new URL. * Links are not settable on
   * newline characters. As a result, setting a link on a text range that
   * crosses a paragraph boundary, such as `"ABC\n123"`, will separate the
   * newline character(s) into their own text runs. The link will be applied
   * separately to the runs before and after the newline. * Removing a link will
   * update the text style of the range to match the style of the preceding text
   * (or the default text styles if the preceding text is another link) unless
   * different styles are being set in the same request.
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
   * Whether or not the text is in small capital letters.
   *
   * @param bool $smallCaps
   */
  public function setSmallCaps($smallCaps)
  {
    $this->smallCaps = $smallCaps;
  }
  /**
   * @return bool
   */
  public function getSmallCaps()
  {
    return $this->smallCaps;
  }
  /**
   * Whether or not the text is struck through.
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
   * Whether or not the text is underlined.
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
  /**
   * The font family and rendered weight of the text. This field is an extension
   * of `font_family` meant to support explicit font weights without breaking
   * backwards compatibility. As such, when reading the style of a range of
   * text, the value of `weighted_font_family#font_family` will always be equal
   * to that of `font_family`. However, when writing, if both fields are
   * included in the field mask (either explicitly or through the wildcard
   * `"*"`), their values are reconciled as follows: * If `font_family` is set
   * and `weighted_font_family` is not, the value of `font_family` is applied
   * with weight `400` ("normal"). * If both fields are set, the value of
   * `font_family` must match that of `weighted_font_family#font_family`. If so,
   * the font family and weight of `weighted_font_family` is applied. Otherwise,
   * a 400 bad request error is returned. * If `weighted_font_family` is set and
   * `font_family` is not, the font family and weight of `weighted_font_family`
   * is applied. * If neither field is set, the font family and weight of the
   * text inherit from the parent. Note that these properties cannot inherit
   * separately from each other. If an update request specifies values for both
   * `weighted_font_family` and `bold`, the `weighted_font_family` is applied
   * first, then `bold`. If `weighted_font_family#weight` is not set, it
   * defaults to `400`. If `weighted_font_family` is set, then
   * `weighted_font_family#font_family` must also be set with a non-empty value.
   * Otherwise, a 400 bad request error is returned.
   *
   * @param WeightedFontFamily $weightedFontFamily
   */
  public function setWeightedFontFamily(WeightedFontFamily $weightedFontFamily)
  {
    $this->weightedFontFamily = $weightedFontFamily;
  }
  /**
   * @return WeightedFontFamily
   */
  public function getWeightedFontFamily()
  {
    return $this->weightedFontFamily;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextStyle::class, 'Google_Service_Slides_TextStyle');
