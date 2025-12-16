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

class BasicChartSpec extends \Google\Collection
{
  /**
   * Default value, do not use.
   */
  public const CHART_TYPE_BASIC_CHART_TYPE_UNSPECIFIED = 'BASIC_CHART_TYPE_UNSPECIFIED';
  /**
   * A bar chart.
   */
  public const CHART_TYPE_BAR = 'BAR';
  /**
   * A line chart.
   */
  public const CHART_TYPE_LINE = 'LINE';
  /**
   * An area chart.
   */
  public const CHART_TYPE_AREA = 'AREA';
  /**
   * A column chart.
   */
  public const CHART_TYPE_COLUMN = 'COLUMN';
  /**
   * A scatter chart.
   */
  public const CHART_TYPE_SCATTER = 'SCATTER';
  /**
   * A combo chart.
   */
  public const CHART_TYPE_COMBO = 'COMBO';
  /**
   * A stepped area chart.
   */
  public const CHART_TYPE_STEPPED_AREA = 'STEPPED_AREA';
  /**
   * Default value, do not use.
   */
  public const COMPARE_MODE_BASIC_CHART_COMPARE_MODE_UNSPECIFIED = 'BASIC_CHART_COMPARE_MODE_UNSPECIFIED';
  /**
   * Only the focused data element is highlighted and shown in the tooltip.
   */
  public const COMPARE_MODE_DATUM = 'DATUM';
  /**
   * All data elements with the same category (e.g., domain value) are
   * highlighted and shown in the tooltip.
   */
  public const COMPARE_MODE_CATEGORY = 'CATEGORY';
  /**
   * Default value, do not use.
   */
  public const LEGEND_POSITION_BASIC_CHART_LEGEND_POSITION_UNSPECIFIED = 'BASIC_CHART_LEGEND_POSITION_UNSPECIFIED';
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
   * Default value, do not use.
   */
  public const STACKED_TYPE_BASIC_CHART_STACKED_TYPE_UNSPECIFIED = 'BASIC_CHART_STACKED_TYPE_UNSPECIFIED';
  /**
   * Series are not stacked.
   */
  public const STACKED_TYPE_NOT_STACKED = 'NOT_STACKED';
  /**
   * Series values are stacked, each value is rendered vertically beginning from
   * the top of the value below it.
   */
  public const STACKED_TYPE_STACKED = 'STACKED';
  /**
   * Vertical stacks are stretched to reach the top of the chart, with values
   * laid out as percentages of each other.
   */
  public const STACKED_TYPE_PERCENT_STACKED = 'PERCENT_STACKED';
  protected $collection_key = 'series';
  protected $axisType = BasicChartAxis::class;
  protected $axisDataType = 'array';
  /**
   * The type of the chart.
   *
   * @var string
   */
  public $chartType;
  /**
   * The behavior of tooltips and data highlighting when hovering on data and
   * chart area.
   *
   * @var string
   */
  public $compareMode;
  protected $domainsType = BasicChartDomain::class;
  protected $domainsDataType = 'array';
  /**
   * The number of rows or columns in the data that are "headers". If not set,
   * Google Sheets will guess how many rows are headers based on the data. (Note
   * that BasicChartAxis.title may override the axis title inferred from the
   * header values.)
   *
   * @var int
   */
  public $headerCount;
  /**
   * If some values in a series are missing, gaps may appear in the chart (e.g,
   * segments of lines in a line chart will be missing). To eliminate these gaps
   * set this to true. Applies to Line, Area, and Combo charts.
   *
   * @var bool
   */
  public $interpolateNulls;
  /**
   * The position of the chart legend.
   *
   * @var string
   */
  public $legendPosition;
  /**
   * Gets whether all lines should be rendered smooth or straight by default.
   * Applies to Line charts.
   *
   * @var bool
   */
  public $lineSmoothing;
  protected $seriesType = BasicChartSeries::class;
  protected $seriesDataType = 'array';
  /**
   * The stacked type for charts that support vertical stacking. Applies to
   * Area, Bar, Column, Combo, and Stepped Area charts.
   *
   * @var string
   */
  public $stackedType;
  /**
   * True to make the chart 3D. Applies to Bar and Column charts.
   *
   * @var bool
   */
  public $threeDimensional;
  protected $totalDataLabelType = DataLabel::class;
  protected $totalDataLabelDataType = '';

  /**
   * The axis on the chart.
   *
   * @param BasicChartAxis[] $axis
   */
  public function setAxis($axis)
  {
    $this->axis = $axis;
  }
  /**
   * @return BasicChartAxis[]
   */
  public function getAxis()
  {
    return $this->axis;
  }
  /**
   * The type of the chart.
   *
   * Accepted values: BASIC_CHART_TYPE_UNSPECIFIED, BAR, LINE, AREA, COLUMN,
   * SCATTER, COMBO, STEPPED_AREA
   *
   * @param self::CHART_TYPE_* $chartType
   */
  public function setChartType($chartType)
  {
    $this->chartType = $chartType;
  }
  /**
   * @return self::CHART_TYPE_*
   */
  public function getChartType()
  {
    return $this->chartType;
  }
  /**
   * The behavior of tooltips and data highlighting when hovering on data and
   * chart area.
   *
   * Accepted values: BASIC_CHART_COMPARE_MODE_UNSPECIFIED, DATUM, CATEGORY
   *
   * @param self::COMPARE_MODE_* $compareMode
   */
  public function setCompareMode($compareMode)
  {
    $this->compareMode = $compareMode;
  }
  /**
   * @return self::COMPARE_MODE_*
   */
  public function getCompareMode()
  {
    return $this->compareMode;
  }
  /**
   * The domain of data this is charting. Only a single domain is supported.
   *
   * @param BasicChartDomain[] $domains
   */
  public function setDomains($domains)
  {
    $this->domains = $domains;
  }
  /**
   * @return BasicChartDomain[]
   */
  public function getDomains()
  {
    return $this->domains;
  }
  /**
   * The number of rows or columns in the data that are "headers". If not set,
   * Google Sheets will guess how many rows are headers based on the data. (Note
   * that BasicChartAxis.title may override the axis title inferred from the
   * header values.)
   *
   * @param int $headerCount
   */
  public function setHeaderCount($headerCount)
  {
    $this->headerCount = $headerCount;
  }
  /**
   * @return int
   */
  public function getHeaderCount()
  {
    return $this->headerCount;
  }
  /**
   * If some values in a series are missing, gaps may appear in the chart (e.g,
   * segments of lines in a line chart will be missing). To eliminate these gaps
   * set this to true. Applies to Line, Area, and Combo charts.
   *
   * @param bool $interpolateNulls
   */
  public function setInterpolateNulls($interpolateNulls)
  {
    $this->interpolateNulls = $interpolateNulls;
  }
  /**
   * @return bool
   */
  public function getInterpolateNulls()
  {
    return $this->interpolateNulls;
  }
  /**
   * The position of the chart legend.
   *
   * Accepted values: BASIC_CHART_LEGEND_POSITION_UNSPECIFIED, BOTTOM_LEGEND,
   * LEFT_LEGEND, RIGHT_LEGEND, TOP_LEGEND, NO_LEGEND
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
   * Gets whether all lines should be rendered smooth or straight by default.
   * Applies to Line charts.
   *
   * @param bool $lineSmoothing
   */
  public function setLineSmoothing($lineSmoothing)
  {
    $this->lineSmoothing = $lineSmoothing;
  }
  /**
   * @return bool
   */
  public function getLineSmoothing()
  {
    return $this->lineSmoothing;
  }
  /**
   * The data this chart is visualizing.
   *
   * @param BasicChartSeries[] $series
   */
  public function setSeries($series)
  {
    $this->series = $series;
  }
  /**
   * @return BasicChartSeries[]
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * The stacked type for charts that support vertical stacking. Applies to
   * Area, Bar, Column, Combo, and Stepped Area charts.
   *
   * Accepted values: BASIC_CHART_STACKED_TYPE_UNSPECIFIED, NOT_STACKED,
   * STACKED, PERCENT_STACKED
   *
   * @param self::STACKED_TYPE_* $stackedType
   */
  public function setStackedType($stackedType)
  {
    $this->stackedType = $stackedType;
  }
  /**
   * @return self::STACKED_TYPE_*
   */
  public function getStackedType()
  {
    return $this->stackedType;
  }
  /**
   * True to make the chart 3D. Applies to Bar and Column charts.
   *
   * @param bool $threeDimensional
   */
  public function setThreeDimensional($threeDimensional)
  {
    $this->threeDimensional = $threeDimensional;
  }
  /**
   * @return bool
   */
  public function getThreeDimensional()
  {
    return $this->threeDimensional;
  }
  /**
   * Controls whether to display additional data labels on stacked charts which
   * sum the total value of all stacked values at each value along the domain
   * axis. These data labels can only be set when chart_type is one of AREA,
   * BAR, COLUMN, COMBO or STEPPED_AREA and stacked_type is either STACKED or
   * PERCENT_STACKED. In addition, for COMBO, this will only be supported if
   * there is only one type of stackable series type or one type has more series
   * than the others and each of the other types have no more than one series.
   * For example, if a chart has two stacked bar series and one area series, the
   * total data labels will be supported. If it has three bar series and two
   * area series, total data labels are not allowed. Neither CUSTOM nor
   * placement can be set on the total_data_label.
   *
   * @param DataLabel $totalDataLabel
   */
  public function setTotalDataLabel(DataLabel $totalDataLabel)
  {
    $this->totalDataLabel = $totalDataLabel;
  }
  /**
   * @return DataLabel
   */
  public function getTotalDataLabel()
  {
    return $this->totalDataLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicChartSpec::class, 'Google_Service_Sheets_BasicChartSpec');
