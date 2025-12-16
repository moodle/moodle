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

class BandingProperties extends \Google\Model
{
  protected $firstBandColorType = Color::class;
  protected $firstBandColorDataType = '';
  protected $firstBandColorStyleType = ColorStyle::class;
  protected $firstBandColorStyleDataType = '';
  protected $footerColorType = Color::class;
  protected $footerColorDataType = '';
  protected $footerColorStyleType = ColorStyle::class;
  protected $footerColorStyleDataType = '';
  protected $headerColorType = Color::class;
  protected $headerColorDataType = '';
  protected $headerColorStyleType = ColorStyle::class;
  protected $headerColorStyleDataType = '';
  protected $secondBandColorType = Color::class;
  protected $secondBandColorDataType = '';
  protected $secondBandColorStyleType = ColorStyle::class;
  protected $secondBandColorStyleDataType = '';

  /**
   * The first color that is alternating. (Required) Deprecated: Use
   * first_band_color_style.
   *
   * @deprecated
   * @param Color $firstBandColor
   */
  public function setFirstBandColor(Color $firstBandColor)
  {
    $this->firstBandColor = $firstBandColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getFirstBandColor()
  {
    return $this->firstBandColor;
  }
  /**
   * The first color that is alternating. (Required) If first_band_color is also
   * set, this field takes precedence.
   *
   * @param ColorStyle $firstBandColorStyle
   */
  public function setFirstBandColorStyle(ColorStyle $firstBandColorStyle)
  {
    $this->firstBandColorStyle = $firstBandColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getFirstBandColorStyle()
  {
    return $this->firstBandColorStyle;
  }
  /**
   * The color of the last row or column. If this field is not set, the last row
   * or column is filled with either first_band_color or second_band_color,
   * depending on the color of the previous row or column. Deprecated: Use
   * footer_color_style.
   *
   * @deprecated
   * @param Color $footerColor
   */
  public function setFooterColor(Color $footerColor)
  {
    $this->footerColor = $footerColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getFooterColor()
  {
    return $this->footerColor;
  }
  /**
   * The color of the last row or column. If this field is not set, the last row
   * or column is filled with either first_band_color or second_band_color,
   * depending on the color of the previous row or column. If footer_color is
   * also set, this field takes precedence.
   *
   * @param ColorStyle $footerColorStyle
   */
  public function setFooterColorStyle(ColorStyle $footerColorStyle)
  {
    $this->footerColorStyle = $footerColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getFooterColorStyle()
  {
    return $this->footerColorStyle;
  }
  /**
   * The color of the first row or column. If this field is set, the first row
   * or column is filled with this color and the colors alternate between
   * first_band_color and second_band_color starting from the second row or
   * column. Otherwise, the first row or column is filled with first_band_color
   * and the colors proceed to alternate as they normally would. Deprecated: Use
   * header_color_style.
   *
   * @deprecated
   * @param Color $headerColor
   */
  public function setHeaderColor(Color $headerColor)
  {
    $this->headerColor = $headerColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getHeaderColor()
  {
    return $this->headerColor;
  }
  /**
   * The color of the first row or column. If this field is set, the first row
   * or column is filled with this color and the colors alternate between
   * first_band_color and second_band_color starting from the second row or
   * column. Otherwise, the first row or column is filled with first_band_color
   * and the colors proceed to alternate as they normally would. If header_color
   * is also set, this field takes precedence.
   *
   * @param ColorStyle $headerColorStyle
   */
  public function setHeaderColorStyle(ColorStyle $headerColorStyle)
  {
    $this->headerColorStyle = $headerColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getHeaderColorStyle()
  {
    return $this->headerColorStyle;
  }
  /**
   * The second color that is alternating. (Required) Deprecated: Use
   * second_band_color_style.
   *
   * @deprecated
   * @param Color $secondBandColor
   */
  public function setSecondBandColor(Color $secondBandColor)
  {
    $this->secondBandColor = $secondBandColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getSecondBandColor()
  {
    return $this->secondBandColor;
  }
  /**
   * The second color that is alternating. (Required) If second_band_color is
   * also set, this field takes precedence.
   *
   * @param ColorStyle $secondBandColorStyle
   */
  public function setSecondBandColorStyle(ColorStyle $secondBandColorStyle)
  {
    $this->secondBandColorStyle = $secondBandColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getSecondBandColorStyle()
  {
    return $this->secondBandColorStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BandingProperties::class, 'Google_Service_Sheets_BandingProperties');
