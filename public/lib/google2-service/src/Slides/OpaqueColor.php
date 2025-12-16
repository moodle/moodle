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

class OpaqueColor extends \Google\Model
{
  /**
   * Unspecified theme color. This value should not be used.
   */
  public const THEME_COLOR_THEME_COLOR_TYPE_UNSPECIFIED = 'THEME_COLOR_TYPE_UNSPECIFIED';
  /**
   * Represents the first dark color.
   */
  public const THEME_COLOR_DARK1 = 'DARK1';
  /**
   * Represents the first light color.
   */
  public const THEME_COLOR_LIGHT1 = 'LIGHT1';
  /**
   * Represents the second dark color.
   */
  public const THEME_COLOR_DARK2 = 'DARK2';
  /**
   * Represents the second light color.
   */
  public const THEME_COLOR_LIGHT2 = 'LIGHT2';
  /**
   * Represents the first accent color.
   */
  public const THEME_COLOR_ACCENT1 = 'ACCENT1';
  /**
   * Represents the second accent color.
   */
  public const THEME_COLOR_ACCENT2 = 'ACCENT2';
  /**
   * Represents the third accent color.
   */
  public const THEME_COLOR_ACCENT3 = 'ACCENT3';
  /**
   * Represents the fourth accent color.
   */
  public const THEME_COLOR_ACCENT4 = 'ACCENT4';
  /**
   * Represents the fifth accent color.
   */
  public const THEME_COLOR_ACCENT5 = 'ACCENT5';
  /**
   * Represents the sixth accent color.
   */
  public const THEME_COLOR_ACCENT6 = 'ACCENT6';
  /**
   * Represents the color to use for hyperlinks.
   */
  public const THEME_COLOR_HYPERLINK = 'HYPERLINK';
  /**
   * Represents the color to use for visited hyperlinks.
   */
  public const THEME_COLOR_FOLLOWED_HYPERLINK = 'FOLLOWED_HYPERLINK';
  /**
   * Represents the first text color.
   */
  public const THEME_COLOR_TEXT1 = 'TEXT1';
  /**
   * Represents the first background color.
   */
  public const THEME_COLOR_BACKGROUND1 = 'BACKGROUND1';
  /**
   * Represents the second text color.
   */
  public const THEME_COLOR_TEXT2 = 'TEXT2';
  /**
   * Represents the second background color.
   */
  public const THEME_COLOR_BACKGROUND2 = 'BACKGROUND2';
  protected $rgbColorType = RgbColor::class;
  protected $rgbColorDataType = '';
  /**
   * An opaque theme color.
   *
   * @var string
   */
  public $themeColor;

  /**
   * An opaque RGB color.
   *
   * @param RgbColor $rgbColor
   */
  public function setRgbColor(RgbColor $rgbColor)
  {
    $this->rgbColor = $rgbColor;
  }
  /**
   * @return RgbColor
   */
  public function getRgbColor()
  {
    return $this->rgbColor;
  }
  /**
   * An opaque theme color.
   *
   * Accepted values: THEME_COLOR_TYPE_UNSPECIFIED, DARK1, LIGHT1, DARK2,
   * LIGHT2, ACCENT1, ACCENT2, ACCENT3, ACCENT4, ACCENT5, ACCENT6, HYPERLINK,
   * FOLLOWED_HYPERLINK, TEXT1, BACKGROUND1, TEXT2, BACKGROUND2
   *
   * @param self::THEME_COLOR_* $themeColor
   */
  public function setThemeColor($themeColor)
  {
    $this->themeColor = $themeColor;
  }
  /**
   * @return self::THEME_COLOR_*
   */
  public function getThemeColor()
  {
    return $this->themeColor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OpaqueColor::class, 'Google_Service_Slides_OpaqueColor');
