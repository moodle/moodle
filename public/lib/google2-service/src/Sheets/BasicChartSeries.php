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

class BasicChartSeries extends \Google\Collection
{
  /**
   * Default value, do not use.
   */
  public const TARGET_AXIS_BASIC_CHART_AXIS_POSITION_UNSPECIFIED = 'BASIC_CHART_AXIS_POSITION_UNSPECIFIED';
  /**
   * The axis rendered at the bottom of a chart. For most charts, this is the
   * standard major axis. For bar charts, this is a minor axis.
   */
  public const TARGET_AXIS_BOTTOM_AXIS = 'BOTTOM_AXIS';
  /**
   * The axis rendered at the left of a chart. For most charts, this is a minor
   * axis. For bar charts, this is the standard major axis.
   */
  public const TARGET_AXIS_LEFT_AXIS = 'LEFT_AXIS';
  /**
   * The axis rendered at the right of a chart. For most charts, this is a minor
   * axis. For bar charts, this is an unusual major axis.
   */
  public const TARGET_AXIS_RIGHT_AXIS = 'RIGHT_AXIS';
  /**
   * Default value, do not use.
   */
  public const TYPE_BASIC_CHART_TYPE_UNSPECIFIED = 'BASIC_CHART_TYPE_UNSPECIFIED';
  /**
   * A bar chart.
   */
  public const TYPE_BAR = 'BAR';
  /**
   * A line chart.
   */
  public const TYPE_LINE = 'LINE';
  /**
   * An area chart.
   */
  public const TYPE_AREA = 'AREA';
  /**
   * A column chart.
   */
  public const TYPE_COLUMN = 'COLUMN';
  /**
   * A scatter chart.
   */
  public const TYPE_SCATTER = 'SCATTER';
  /**
   * A combo chart.
   */
  public const TYPE_COMBO = 'COMBO';
  /**
   * A stepped area chart.
   */
  public const TYPE_STEPPED_AREA = 'STEPPED_AREA';
  protected $collection_key = 'styleOverrides';
  protected $colorType = Color::class;
  protected $colorDataType = '';
  protected $colorStyleType = ColorStyle::class;
  protected $colorStyleDataType = '';
  protected $dataLabelType = DataLabel::class;
  protected $dataLabelDataType = '';
  protected $lineStyleType = LineStyle::class;
  protected $lineStyleDataType = '';
  protected $pointStyleType = PointStyle::class;
  protected $pointStyleDataType = '';
  protected $seriesType = ChartData::class;
  protected $seriesDataType = '';
  protected $styleOverridesType = BasicSeriesDataPointStyleOverride::class;
  protected $styleOverridesDataType = 'array';
  /**
   * The minor axis that will specify the range of values for this series. For
   * example, if charting stocks over time, the "Volume" series may want to be
   * pinned to the right with the prices pinned to the left, because the scale
   * of trading volume is different than the scale of prices. It is an error to
   * specify an axis that isn't a valid minor axis for the chart's type.
   *
   * @var string
   */
  public $targetAxis;
  /**
   * The type of this series. Valid only if the chartType is COMBO. Different
   * types will change the way the series is visualized. Only LINE, AREA, and
   * COLUMN are supported.
   *
   * @var string
   */
  public $type;

  /**
   * The color for elements (such as bars, lines, and points) associated with
   * this series. If empty, a default color is used. Deprecated: Use
   * color_style.
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
   * The color for elements (such as bars, lines, and points) associated with
   * this series. If empty, a default color is used. If color is also set, this
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
   * Information about the data labels for this series.
   *
   * @param DataLabel $dataLabel
   */
  public function setDataLabel(DataLabel $dataLabel)
  {
    $this->dataLabel = $dataLabel;
  }
  /**
   * @return DataLabel
   */
  public function getDataLabel()
  {
    return $this->dataLabel;
  }
  /**
   * The line style of this series. Valid only if the chartType is AREA, LINE,
   * or SCATTER. COMBO charts are also supported if the series chart type is
   * AREA or LINE.
   *
   * @param LineStyle $lineStyle
   */
  public function setLineStyle(LineStyle $lineStyle)
  {
    $this->lineStyle = $lineStyle;
  }
  /**
   * @return LineStyle
   */
  public function getLineStyle()
  {
    return $this->lineStyle;
  }
  /**
   * The style for points associated with this series. Valid only if the
   * chartType is AREA, LINE, or SCATTER. COMBO charts are also supported if the
   * series chart type is AREA, LINE, or SCATTER. If empty, a default point
   * style is used.
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
  /**
   * The data being visualized in this chart series.
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
  /**
   * Style override settings for series data points.
   *
   * @param BasicSeriesDataPointStyleOverride[] $styleOverrides
   */
  public function setStyleOverrides($styleOverrides)
  {
    $this->styleOverrides = $styleOverrides;
  }
  /**
   * @return BasicSeriesDataPointStyleOverride[]
   */
  public function getStyleOverrides()
  {
    return $this->styleOverrides;
  }
  /**
   * The minor axis that will specify the range of values for this series. For
   * example, if charting stocks over time, the "Volume" series may want to be
   * pinned to the right with the prices pinned to the left, because the scale
   * of trading volume is different than the scale of prices. It is an error to
   * specify an axis that isn't a valid minor axis for the chart's type.
   *
   * Accepted values: BASIC_CHART_AXIS_POSITION_UNSPECIFIED, BOTTOM_AXIS,
   * LEFT_AXIS, RIGHT_AXIS
   *
   * @param self::TARGET_AXIS_* $targetAxis
   */
  public function setTargetAxis($targetAxis)
  {
    $this->targetAxis = $targetAxis;
  }
  /**
   * @return self::TARGET_AXIS_*
   */
  public function getTargetAxis()
  {
    return $this->targetAxis;
  }
  /**
   * The type of this series. Valid only if the chartType is COMBO. Different
   * types will change the way the series is visualized. Only LINE, AREA, and
   * COLUMN are supported.
   *
   * Accepted values: BASIC_CHART_TYPE_UNSPECIFIED, BAR, LINE, AREA, COLUMN,
   * SCATTER, COMBO, STEPPED_AREA
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicChartSeries::class, 'Google_Service_Sheets_BasicChartSeries');
