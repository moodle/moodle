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

class BasicSeriesDataPointStyleOverride extends \Google\Model
{
  protected $colorType = Color::class;
  protected $colorDataType = '';
  protected $colorStyleType = ColorStyle::class;
  protected $colorStyleDataType = '';
  /**
   * The zero-based index of the series data point.
   *
   * @var int
   */
  public $index;
  protected $pointStyleType = PointStyle::class;
  protected $pointStyleDataType = '';

  /**
   * Color of the series data point. If empty, the series default is used.
   * Deprecated: Use color_style.
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
   * Color of the series data point. If empty, the series default is used. If
   * color is also set, this field takes precedence.
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
   * The zero-based index of the series data point.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Point style of the series data point. Valid only if the chartType is AREA,
   * LINE, or SCATTER. COMBO charts are also supported if the series chart type
   * is AREA, LINE, or SCATTER. If empty, the series default is used.
   *
   * @param PointStyle $pointStyle
   */
  public function setPointStyle(PointStyle $pointStyle)
  {
    $this->pointStyle = $pointStyle;
  }
  /**
   * @return PointStyle
   */
  public function getPointStyle()
  {
    return $this->pointStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicSeriesDataPointStyleOverride::class, 'Google_Service_Sheets_BasicSeriesDataPointStyleOverride');
