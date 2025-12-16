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

class InterpolationPoint extends \Google\Model
{
  /**
   * The default value, do not use.
   */
  public const TYPE_INTERPOLATION_POINT_TYPE_UNSPECIFIED = 'INTERPOLATION_POINT_TYPE_UNSPECIFIED';
  /**
   * The interpolation point uses the minimum value in the cells over the range
   * of the conditional format.
   */
  public const TYPE_MIN = 'MIN';
  /**
   * The interpolation point uses the maximum value in the cells over the range
   * of the conditional format.
   */
  public const TYPE_MAX = 'MAX';
  /**
   * The interpolation point uses exactly the value in InterpolationPoint.value.
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * The interpolation point is the given percentage over all the cells in the
   * range of the conditional format. This is equivalent to `NUMBER` if the
   * value was: `=(MAX(FLATTEN(range)) * (value / 100)) + (MIN(FLATTEN(range)) *
   * (1 - (value / 100)))` (where errors in the range are ignored when
   * flattening).
   */
  public const TYPE_PERCENT = 'PERCENT';
  /**
   * The interpolation point is the given percentile over all the cells in the
   * range of the conditional format. This is equivalent to `NUMBER` if the
   * value was: `=PERCENTILE(FLATTEN(range), value / 100)` (where errors in the
   * range are ignored when flattening).
   */
  public const TYPE_PERCENTILE = 'PERCENTILE';
  protected $colorType = Color::class;
  protected $colorDataType = '';
  protected $colorStyleType = ColorStyle::class;
  protected $colorStyleDataType = '';
  /**
   * How the value should be interpreted.
   *
   * @var string
   */
  public $type;
  /**
   * The value this interpolation point uses. May be a formula. Unused if type
   * is MIN or MAX.
   *
   * @var string
   */
  public $value;

  /**
   * The color this interpolation point should use. Deprecated: Use color_style.
   *
   * @deprecated
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The color this interpolation point should use. If color is also set, this
   * field takes precedence.
   *
   * @param ColorStyle $colorStyle
   */
  public function setColorStyle(ColorStyle $colorStyle)
  {
    $this->colorStyle = $colorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getColorStyle()
  {
    return $this->colorStyle;
  }
  /**
   * How the value should be interpreted.
   *
   * Accepted values: INTERPOLATION_POINT_TYPE_UNSPECIFIED, MIN, MAX, NUMBER,
   * PERCENT, PERCENTILE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The value this interpolation point uses. May be a formula. Unused if type
   * is MIN or MAX.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterpolationPoint::class, 'Google_Service_Sheets_InterpolationPoint');
