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

class HistogramChartSpec extends \Google\Collection
{
  /**
   * Default value, do not use.
   */
  public const LEGEND_POSITION_HISTOGRAM_CHART_LEGEND_POSITION_UNSPECIFIED = 'HISTOGRAM_CHART_LEGEND_POSITION_UNSPECIFIED';
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
  protected $collection_key = 'series';
  /**
   * By default the bucket size (the range of values stacked in a single column)
   * is chosen automatically, but it may be overridden here. E.g., A bucket size
   * of 1.5 results in buckets from 0 - 1.5, 1.5 - 3.0, etc. Cannot be negative.
   * This field is optional.
   *
   * @var 
   */
  public $bucketSize;
  /**
   * The position of the chart legend.
   *
   * @var string
   */
  public $legendPosition;
  /**
   * The outlier percentile is used to ensure that outliers do not adversely
   * affect the calculation of bucket sizes. For example, setting an outlier
   * percentile of 0.05 indicates that the top and bottom 5% of values when
   * calculating buckets. The values are still included in the chart, they will
   * be added to the first or last buckets instead of their own buckets. Must be
   * between 0.0 and 0.5.
   *
   * @var 
   */
  public $outlierPercentile;
  protected $seriesType = HistogramSeries::class;
  protected $seriesDataType = 'array';
  /**
   * Whether horizontal divider lines should be displayed between items in each
   * column.
   *
   * @var bool
   */
  public $showItemDividers;

  public function setBucketSize($bucketSize)
  {
    $this->bucketSize = $bucketSize;
  }
  public function getBucketSize()
  {
    return $this->bucketSize;
  }
  /**
   * The position of the chart legend.
   *
   * Accepted values: HISTOGRAM_CHART_LEGEND_POSITION_UNSPECIFIED,
   * BOTTOM_LEGEND, LEFT_LEGEND, RIGHT_LEGEND, TOP_LEGEND, NO_LEGEND,
   * INSIDE_LEGEND
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
  public function setOutlierPercentile($outlierPercentile)
  {
    $this->outlierPercentile = $outlierPercentile;
  }
  public function getOutlierPercentile()
  {
    return $this->outlierPercentile;
  }
  /**
   * The series for a histogram may be either a single series of values to be
   * bucketed or multiple series, each of the same length, containing the name
   * of the series followed by the values to be bucketed for that series.
   *
   * @param HistogramSeries[] $series
   */
  public function setSeries($series)
  {
    $this->series = $series;
  }
  /**
   * @return HistogramSeries[]
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * Whether horizontal divider lines should be displayed between items in each
   * column.
   *
   * @param bool $showItemDividers
   */
  public function setShowItemDividers($showItemDividers)
  {
    $this->showItemDividers = $showItemDividers;
  }
  /**
   * @return bool
   */
  public function getShowItemDividers()
  {
    return $this->showItemDividers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistogramChartSpec::class, 'Google_Service_Sheets_HistogramChartSpec');
