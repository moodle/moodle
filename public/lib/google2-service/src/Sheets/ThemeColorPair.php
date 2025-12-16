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

class ThemeColorPair extends \Google\Model
{
  /**
   * Unspecified theme color
   */
  public const COLOR_TYPE_THEME_COLOR_TYPE_UNSPECIFIED = 'THEME_COLOR_TYPE_UNSPECIFIED';
  /**
   * Represents the primary text color
   */
  public const COLOR_TYPE_TEXT = 'TEXT';
  /**
   * Represents the primary background color
   */
  public const COLOR_TYPE_BACKGROUND = 'BACKGROUND';
  /**
   * Represents the first accent color
   */
  public const COLOR_TYPE_ACCENT1 = 'ACCENT1';
  /**
   * Represents the second accent color
   */
  public const COLOR_TYPE_ACCENT2 = 'ACCENT2';
  /**
   * Represents the third accent color
   */
  public const COLOR_TYPE_ACCENT3 = 'ACCENT3';
  /**
   * Represents the fourth accent color
   */
  public const COLOR_TYPE_ACCENT4 = 'ACCENT4';
  /**
   * Represents the fifth accent color
   */
  public const COLOR_TYPE_ACCENT5 = 'ACCENT5';
  /**
   * Represents the sixth accent color
   */
  public const COLOR_TYPE_ACCENT6 = 'ACCENT6';
  /**
   * Represents the color to use for hyperlinks
   */
  public const COLOR_TYPE_LINK = 'LINK';
  protected $colorDataType = '';
  /**
   * The type of the spreadsheet theme color.
   *
   * @var string
   */
  public $colorType;

  /**
   * The concrete color corresponding to the theme color type.
   *
   * @param ColorStyle $color
   */
  public function setColor(ColorStyle $color)
  {
    $this->color = $color;
  }
  /**
   * @return ColorStyle
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The type of the spreadsheet theme color.
   *
   * Accepted values: THEME_COLOR_TYPE_UNSPECIFIED, TEXT, BACKGROUND, ACCENT1,
   * ACCENT2, ACCENT3, ACCENT4, ACCENT5, ACCENT6, LINK
   *
   * @param self::COLOR_TYPE_* $colorType
   */
  public function setColorType($colorType)
  {
    $this->colorType = $colorType;
  }
  /**
   * @return self::COLOR_TYPE_*
   */
  public function getColorType()
  {
    return $this->colorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThemeColorPair::class, 'Google_Service_Sheets_ThemeColorPair');
