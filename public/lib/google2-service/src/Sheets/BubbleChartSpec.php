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

class BubbleChartSpec extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const LEGEND_POSITION_BUBBLE_CHART_LEGEND_POSITION_UNSPECIFIED = 'BUBBLE_CHART_LEGEND_POSITION_UNSPECIFIED';
  /**
   * The legend is rendered on the bottom of the chart.
   */
  public const LEGEND_POSITION_BOTTOM_LEGEND = 'BOTTOM_LEGEND';
  /**
   * The legend is rendered on the left of the chart.
   */
  public const LEGEND_POSITION_LEFT_LEGEND = 'LEFT_LEGEND';
  /**
   * The legend is rendered on the right of the chart.
   */
  public const LEGEND_POSITION_RIGHT_LEGEND = 'RIGHT_LEGEND';
  /**
   * The legend is rendered on the top of the chart.
   */
  public const LEGEND_POSITION_TOP_LEGEND = 'TOP_LEGEND';
  /**
   * No legend is rendered.
   */
  public const LEGEND_POSITION_NO_LEGEND = 'NO_LEGEND';
  /**
   * The legend is rendered inside the chart area.
   */
  public const LEGEND_POSITION_INSIDE_LEGEND = 'INSIDE_LEGEND';
  protected $bubbleBorderColorType = Color::class;
  protected $bubbleBorderColorDataType = '';
  protected $bubbleBorderColorStyleType = ColorStyle::class;
  protected $bubbleBorderColorStyleDataType = '';
  protected $bubbleLabelsType = ChartData::class;
  protected $bubbleLabelsDataType = '';
  /**
   * The max radius size of the bubbles, in pixels. If specified, the field must
   * be a positive value.
   *
   * @var int
   */
  public $bubbleMaxRadiusSize;
  /**
   * The minimum radius size of the bubbles, in pixels. If specific, the field
   * must be a positive value.
   *
   * @var int
   */
  public $bubbleMinRadiusSize;
  /**
   * The opacity of the bubbles between 0 and 1.0. 0 is fully transparent and 1
   * is fully opaque.
   *
   * @var float
   */
  public $bubbleOpacity;
  protected $bubbleSizesType = ChartData::class;
  protected $bubbleSizesDataType = '';
  protected $bubbleTextStyleType = TextFormat::class;
  protected $bubbleTextStyleDataType = '';
  protected $domainType = ChartData::class;
  protected $domainDataType = '';
  protected $groupIdsType = ChartData::class;
  protected $groupIdsDataType = '';
  /**
   * Where the legend of the chart should be drawn.
   *
   * @var string
   */
  public $legendPosition;
  protected $seriesType = ChartData::class;
  protected $seriesDataType = '';

  /**
   * The bubble border color. Deprecated: Use bubble_border_color_style.
   *
   * @deprecated
   * @param Color $bubbleBorderColor
   */
  public function setBubbleBorderColor(Color $bubbleBorderColor)
  {
    $this->bubbleBorderColor = $bubbleBorderColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getBubbleBorderColor()
  {
    return $this->bubbleBorderColor;
  }
  /**
   * The bubble border color. If bubble_border_color is also set, this field
   * takes precedence.
   *
   * @param ColorStyle $bubbleBorderColorStyle
   */
  public function setBubbleBorderColorStyle(ColorStyle $bubbleBorderColorStyle)
  {
    $this->bubbleBorderColorStyle = $bubbleBorderColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getBubbleBorderColorStyle()
  {
    return $this->bubbleBorderColorStyle;
  }
  /**
   * The data containing the bubble labels. These do not need to be unique.
   *
   * @param ChartData $bubbleLabels
   */
  public function setBubbleLabels(ChartData $bubbleLabels)
  {
    $this->bubbleLabels = $bubbleLabels;
  }
  /**
   * @return ChartData
   */
  public function getBubbleLabels()
  {
    return $this->bubbleLabels;
  }
  /**
   * The max radius size of the bubbles, in pixels. If specified, the field must
   * be a positive value.
   *
   * @param int $bubbleMaxRadiusSize
   */
  public function setBubbleMaxRadiusSize($bubbleMaxRadiusSize)
  {
    $this->bubbleMaxRadiusSize = $bubbleMaxRadiusSize;
  }
  /**
   * @return int
   */
  public function getBubbleMaxRadiusSize()
  {
    return $this->bubbleMaxRadiusSize;
  }
  /**
   * The minimum radius size of the bubbles, in pixels. If specific, the field
   * must be a positive value.
   *
   * @param int $bubbleMinRadiusSize
   */
  public function setBubbleMinRadiusSize($bubbleMinRadiusSize)
  {
    $this->bubbleMinRadiusSize = $bubbleMinRadiusSize;
  }
  /**
   * @return int
   */
  public function getBubbleMinRadiusSize()
  {
    return $this->bubbleMinRadiusSize;
  }
  /**
   * The opacity of the bubbles between 0 and 1.0. 0 is fully transparent and 1
   * is fully opaque.
   *
   * @param float $bubbleOpacity
   */
  public function setBubbleOpacity($bubbleOpacity)
  {
    $this->bubbleOpacity = $bubbleOpacity;
  }
  /**
   * @return float
   */
  public function getBubbleOpacity()
  {
    return $this->bubbleOpacity;
  }
  /**
   * The data containing the bubble sizes. Bubble sizes are used to draw the
   * bubbles at different sizes relative to each other. If specified, group_ids
   * must also be specified. This field is optional.
   *
   * @param ChartData $bubbleSizes
   */
  public function setBubbleSizes(ChartData $bubbleSizes)
  {
    $this->bubbleSizes = $bubbleSizes;
  }
  /**
   * @return ChartData
   */
  public function getBubbleSizes()
  {
    return $this->bubbleSizes;
  }
  /**
   * The format of the text inside the bubbles. Strikethrough, underline, and
   * link are not supported.
   *
   * @param TextFormat $bubbleTextStyle
   */
  public function setBubbleTextStyle(TextFormat $bubbleTextStyle)
  {
    $this->bubbleTextStyle = $bubbleTextStyle;
  }
  /**
   * @return TextFormat
   */
  public function getBubbleTextStyle()
  {
    return $this->bubbleTextStyle;
  }
  /**
   * The data containing the bubble x-values. These values locate the bubbles in
   * the chart horizontally.
   *
   * @param ChartData $domain
   */
  public function setDomain(ChartData $domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return ChartData
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The data containing the bubble group IDs. All bubbles with the same group
   * ID are drawn in the same color. If bubble_sizes is specified then this
   * field must also be specified but may contain blank values. This field is
   * optional.
   *
   * @param ChartData $groupIds
   */
  public function setGroupIds(ChartData $groupIds)
  {
    $this->groupIds = $groupIds;
  }
  /**
   * @return ChartData
   */
  public function getGroupIds()
  {
    return $this->groupIds;
  }
  /**
   * Where the legend of the chart should be drawn.
   *
   * Accepted values: BUBBLE_CHART_LEGEND_POSITION_UNSPECIFIED, BOTTOM_LEGEND,
   * LEFT_LEGEND, RIGHT_LEGEND, TOP_LEGEND, NO_LEGEND, INSIDE_LEGEND
   *
   * @param self::LEGEND_POSITION_* $legendPosition
   */
  public function setLegendPosition($legendPosition)
  {
    $this->legendPosition = $legendPosition;
  }
  /**
   * @return self::LEGEND_POSITION_*
   */
  public function getLegendPosition()
  {
    return $this->legendPosition;
  }
  /**
   * The data containing the bubble y-values. These values locate the bubbles in
   * the chart vertically.
   *
   * @param ChartData $series
   */
  public function setSeries(ChartData $series)
  {
    $this->series = $series;
  }
  /**
   * @return ChartData
   */
  public function getSeries()
  {
    return $this->series;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BubbleChartSpec::class, 'Google_Service_Sheets_BubbleChartSpec');
