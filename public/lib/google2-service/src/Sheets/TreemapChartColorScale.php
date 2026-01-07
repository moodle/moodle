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

class TreemapChartColorScale extends \Google\Model
{
  protected $maxValueColorType = Color::class;
  protected $maxValueColorDataType = '';
  protected $maxValueColorStyleType = ColorStyle::class;
  protected $maxValueColorStyleDataType = '';
  protected $midValueColorType = Color::class;
  protected $midValueColorDataType = '';
  protected $midValueColorStyleType = ColorStyle::class;
  protected $midValueColorStyleDataType = '';
  protected $minValueColorType = Color::class;
  protected $minValueColorDataType = '';
  protected $minValueColorStyleType = ColorStyle::class;
  protected $minValueColorStyleDataType = '';
  protected $noDataColorType = Color::class;
  protected $noDataColorDataType = '';
  protected $noDataColorStyleType = ColorStyle::class;
  protected $noDataColorStyleDataType = '';

  /**
   * The background color for cells with a color value greater than or equal to
   * maxValue. Defaults to #109618 if not specified. Deprecated: Use
   * max_value_color_style.
   *
   * @deprecated
   * @param Color $maxValueColor
   */
  public function setMaxValueColor(Color $maxValueColor)
  {
    $this->maxValueColor = $maxValueColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getMaxValueColor()
  {
    return $this->maxValueColor;
  }
  /**
   * The background color for cells with a color value greater than or equal to
   * maxValue. Defaults to #109618 if not specified. If max_value_color is also
   * set, this field takes precedence.
   *
   * @param ColorStyle $maxValueColorStyle
   */
  public function setMaxValueColorStyle(ColorStyle $maxValueColorStyle)
  {
    $this->maxValueColorStyle = $maxValueColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getMaxValueColorStyle()
  {
    return $this->maxValueColorStyle;
  }
  /**
   * The background color for cells with a color value at the midpoint between
   * minValue and maxValue. Defaults to #efe6dc if not specified. Deprecated:
   * Use mid_value_color_style.
   *
   * @deprecated
   * @param Color $midValueColor
   */
  public function setMidValueColor(Color $midValueColor)
  {
    $this->midValueColor = $midValueColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getMidValueColor()
  {
    return $this->midValueColor;
  }
  /**
   * The background color for cells with a color value at the midpoint between
   * minValue and maxValue. Defaults to #efe6dc if not specified. If
   * mid_value_color is also set, this field takes precedence.
   *
   * @param ColorStyle $midValueColorStyle
   */
  public function setMidValueColorStyle(ColorStyle $midValueColorStyle)
  {
    $this->midValueColorStyle = $midValueColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getMidValueColorStyle()
  {
    return $this->midValueColorStyle;
  }
  /**
   * The background color for cells with a color value less than or equal to
   * minValue. Defaults to #dc3912 if not specified. Deprecated: Use
   * min_value_color_style.
   *
   * @deprecated
   * @param Color $minValueColor
   */
  public function setMinValueColor(Color $minValueColor)
  {
    $this->minValueColor = $minValueColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getMinValueColor()
  {
    return $this->minValueColor;
  }
  /**
   * The background color for cells with a color value less than or equal to
   * minValue. Defaults to #dc3912 if not specified. If min_value_color is also
   * set, this field takes precedence.
   *
   * @param ColorStyle $minValueColorStyle
   */
  public function setMinValueColorStyle(ColorStyle $minValueColorStyle)
  {
    $this->minValueColorStyle = $minValueColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getMinValueColorStyle()
  {
    return $this->minValueColorStyle;
  }
  /**
   * The background color for cells that have no color data associated with
   * them. Defaults to #000000 if not specified. Deprecated: Use
   * no_data_color_style.
   *
   * @deprecated
   * @param Color $noDataColor
   */
  public function setNoDataColor(Color $noDataColor)
  {
    $this->noDataColor = $noDataColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getNoDataColor()
  {
    return $this->noDataColor;
  }
  /**
   * The background color for cells that have no color data associated with
   * them. Defaults to #000000 if not specified. If no_data_color is also set,
   * this field takes precedence.
   *
   * @param ColorStyle $noDataColorStyle
   */
  public function setNoDataColorStyle(ColorStyle $noDataColorStyle)
  {
    $this->noDataColorStyle = $noDataColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getNoDataColorStyle()
  {
    return $this->noDataColorStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TreemapChartColorScale::class, 'Google_Service_Sheets_TreemapChartColorScale');
