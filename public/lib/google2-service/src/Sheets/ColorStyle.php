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

class ColorStyle extends \Google\Model
{
  /**
   * Unspecified theme color
   */
  public const THEME_COLOR_THEME_COLOR_TYPE_UNSPECIFIED = 'THEME_COLOR_TYPE_UNSPECIFIED';
  /**
   * Represents the primary text color
   */
  public const THEME_COLOR_TEXT = 'TEXT';
  /**
   * Represents the primary background color
   */
  public const THEME_COLOR_BACKGROUND = 'BACKGROUND';
  /**
   * Represents the first accent color
   */
  public const THEME_COLOR_ACCENT1 = 'ACCENT1';
  /**
   * Represents the second accent color
   */
  public const THEME_COLOR_ACCENT2 = 'ACCENT2';
  /**
   * Represents the third accent color
   */
  public const THEME_COLOR_ACCENT3 = 'ACCENT3';
  /**
   * Represents the fourth accent color
   */
  public const THEME_COLOR_ACCENT4 = 'ACCENT4';
  /**
   * Represents the fifth accent color
   */
  public const THEME_COLOR_ACCENT5 = 'ACCENT5';
  /**
   * Represents the sixth accent color
   */
  public const THEME_COLOR_ACCENT6 = 'ACCENT6';
  /**
   * Represents the color to use for hyperlinks
   */
  public const THEME_COLOR_LINK = 'LINK';
  protected $rgbColorType = Color::class;
  protected $rgbColorDataType = '';
  /**
   * Theme color.
   *
   * @var string
   */
  public $themeColor;

  /**
   * RGB color. The [`alpha`](https://developers.google.com/workspace/sheets/api
   * /reference/rest/v4/spreadsheets/other#Color.FIELDS.alpha) value in the [`Co
   * lor`](https://developers.google.com/workspace/sheets/api/reference/rest/v4/
   * spreadsheets/other#color) object isn't generally supported.
   *
   * @param Color $rgbColor
   */
  public function setRgbColor(Color $rgbColor)
  {
    $this->rgbColor = $rgbColor;
  }
  /**
   * @return Color
   */
  public function getRgbColor()
  {
    return $this->rgbColor;
  }
  /**
   * Theme color.
   *
   * Accepted values: THEME_COLOR_TYPE_UNSPECIFIED, TEXT, BACKGROUND, ACCENT1,
   * ACCENT2, ACCENT3, ACCENT4, ACCENT5, ACCENT6, LINK
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
class_alias(ColorStyle::class, 'Google_Service_Sheets_ColorStyle');
