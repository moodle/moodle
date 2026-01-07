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

class TreemapChartSpec extends \Google\Model
{
  protected $colorDataType = ChartData::class;
  protected $colorDataDataType = '';
  protected $colorScaleType = TreemapChartColorScale::class;
  protected $colorScaleDataType = '';
  protected $headerColorType = Color::class;
  protected $headerColorDataType = '';
  protected $headerColorStyleType = ColorStyle::class;
  protected $headerColorStyleDataType = '';
  /**
   * True to hide tooltips.
   *
   * @var bool
   */
  public $hideTooltips;
  /**
   * The number of additional data levels beyond the labeled levels to be shown
   * on the treemap chart. These levels are not interactive and are shown
   * without their labels. Defaults to 0 if not specified.
   *
   * @var int
   */
  public $hintedLevels;
  protected $labelsType = ChartData::class;
  protected $labelsDataType = '';
  /**
   * The number of data levels to show on the treemap chart. These levels are
   * interactive and are shown with their labels. Defaults to 2 if not
   * specified.
   *
   * @var int
   */
  public $levels;
  /**
   * The maximum possible data value. Cells with values greater than this will
   * have the same color as cells with this value. If not specified, defaults to
   * the actual maximum value from color_data, or the maximum value from
   * size_data if color_data is not specified.
   *
   * @var 
   */
  public $maxValue;
  /**
   * The minimum possible data value. Cells with values less than this will have
   * the same color as cells with this value. If not specified, defaults to the
   * actual minimum value from color_data, or the minimum value from size_data
   * if color_data is not specified.
   *
   * @var 
   */
  public $minValue;
  protected $parentLabelsType = ChartData::class;
  protected $parentLabelsDataType = '';
  protected $sizeDataType = ChartData::class;
  protected $sizeDataDataType = '';
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';

  /**
   * The data that determines the background color of each treemap data cell.
   * This field is optional. If not specified, size_data is used to determine
   * background colors. If specified, the data is expected to be numeric.
   * color_scale will determine how the values in this data map to data cell
   * background colors.
   *
   * @param ChartData $colorData
   */
  public function setColorData(ChartData $colorData)
  {
    $this->colorData = $colorData;
  }
  /**
   * @return ChartData
   */
  public function getColorData()
  {
    return $this->colorData;
  }
  /**
   * The color scale for data cells in the treemap chart. Data cells are
   * assigned colors based on their color values. These color values come from
   * color_data, or from size_data if color_data is not specified. Cells with
   * color values less than or equal to min_value will have minValueColor as
   * their background color. Cells with color values greater than or equal to
   * max_value will have maxValueColor as their background color. Cells with
   * color values between min_value and max_value will have background colors on
   * a gradient between minValueColor and maxValueColor, the midpoint of the
   * gradient being midValueColor. Cells with missing or non-numeric color
   * values will have noDataColor as their background color.
   *
   * @param TreemapChartColorScale $colorScale
   */
  public function setColorScale(TreemapChartColorScale $colorScale)
  {
    $this->colorScale = $colorScale;
  }
  /**
   * @return TreemapChartColorScale
   */
  public function getColorScale()
  {
    return $this->colorScale;
  }
  /**
   * The background color for header cells. Deprecated: Use header_color_style.
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
   * The background color for header cells. If header_color is also set, this
   * field takes precedence.
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
   * True to hide tooltips.
   *
   * @param bool $hideTooltips
   */
  public function setHideTooltips($hideTooltips)
  {
    $this->hideTooltips = $hideTooltips;
  }
  /**
   * @return bool
   */
  public function getHideTooltips()
  {
    return $this->hideTooltips;
  }
  /**
   * The number of additional data levels beyond the labeled levels to be shown
   * on the treemap chart. These levels are not interactive and are shown
   * without their labels. Defaults to 0 if not specified.
   *
   * @param int $hintedLevels
   */
  public function setHintedLevels($hintedLevels)
  {
    $this->hintedLevels = $hintedLevels;
  }
  /**
   * @return int
   */
  public function getHintedLevels()
  {
    return $this->hintedLevels;
  }
  /**
   * The data that contains the treemap cell labels.
   *
   * @param ChartData $labels
   */
  public function setLabels(ChartData $labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return ChartData
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The number of data levels to show on the treemap chart. These levels are
   * interactive and are shown with their labels. Defaults to 2 if not
   * specified.
   *
   * @param int $levels
   */
  public function setLevels($levels)
  {
    $this->levels = $levels;
  }
  /**
   * @return int
   */
  public function getLevels()
  {
    return $this->levels;
  }
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * The data the contains the treemap cells' parent labels.
   *
   * @param ChartData $parentLabels
   */
  public function setParentLabels(ChartData $parentLabels)
  {
    $this->parentLabels = $parentLabels;
  }
  /**
   * @return ChartData
   */
  public function getParentLabels()
  {
    return $this->parentLabels;
  }
  /**
   * The data that determines the size of each treemap data cell. This data is
   * expected to be numeric. The cells corresponding to non-numeric or missing
   * data will not be rendered. If color_data is not specified, this data is
   * used to determine data cell background colors as well.
   *
   * @param ChartData $sizeData
   */
  public function setSizeData(ChartData $sizeData)
  {
    $this->sizeData = $sizeData;
  }
  /**
   * @return ChartData
   */
  public function getSizeData()
  {
    return $this->sizeData;
  }
  /**
   * The text format for all labels on the chart. The link field is not
   * supported.
   *
   * @param TextFormat $textFormat
   */
  public function setTextFormat(TextFormat $textFormat)
  {
    $this->textFormat = $textFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTextFormat()
  {
    return $this->textFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TreemapChartSpec::class, 'Google_Service_Sheets_TreemapChartSpec');
