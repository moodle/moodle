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

namespace Google\Service\AnalyticsData;

class RunRealtimeReportRequest extends \Google\Collection
{
  protected $collection_key = 'orderBys';
  protected $dimensionFilterType = FilterExpression::class;
  protected $dimensionFilterDataType = '';
  protected $dimensionsType = Dimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 250,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API can also return fewer rows than
   * the requested `limit`, if there aren't as many dimension values as the
   * `limit`. For instance, there are fewer than 300 possible values for the
   * dimension `country`, so when reporting on only `country`, you can't get
   * more than 300 rows, even if you set `limit` to a higher value.
   *
   * @var string
   */
  public $limit;
  /**
   * Aggregation of metrics. Aggregated metric values will be shown in rows
   * where the dimension_values are set to "RESERVED_(MetricAggregation)".
   *
   * @var string[]
   */
  public $metricAggregations;
  protected $metricFilterType = FilterExpression::class;
  protected $metricFilterDataType = '';
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  protected $minuteRangesType = MinuteRange::class;
  protected $minuteRangesDataType = 'array';
  protected $orderBysType = OrderBy::class;
  protected $orderBysDataType = 'array';
  /**
   * Toggles whether to return the current state of this Google Analytics
   * property's Realtime quota. Quota is returned in
   * [PropertyQuota](#PropertyQuota).
   *
   * @var bool
   */
  public $returnPropertyQuota;

  /**
   * The filter clause of dimensions. Metrics cannot be used in this filter.
   *
   * @param FilterExpression $dimensionFilter
   */
  public function setDimensionFilter(FilterExpression $dimensionFilter)
  {
    $this->dimensionFilter = $dimensionFilter;
  }
  /**
   * @return FilterExpression
   */
  public function getDimensionFilter()
  {
    return $this->dimensionFilter;
  }
  /**
   * The dimensions requested and displayed.
   *
   * @param Dimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return Dimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 250,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API can also return fewer rows than
   * the requested `limit`, if there aren't as many dimension values as the
   * `limit`. For instance, there are fewer than 300 possible values for the
   * dimension `country`, so when reporting on only `country`, you can't get
   * more than 300 rows, even if you set `limit` to a higher value.
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Aggregation of metrics. Aggregated metric values will be shown in rows
   * where the dimension_values are set to "RESERVED_(MetricAggregation)".
   *
   * @param string[] $metricAggregations
   */
  public function setMetricAggregations($metricAggregations)
  {
    $this->metricAggregations = $metricAggregations;
  }
  /**
   * @return string[]
   */
  public function getMetricAggregations()
  {
    return $this->metricAggregations;
  }
  /**
   * The filter clause of metrics. Applied at post aggregation phase, similar to
   * SQL having-clause. Dimensions cannot be used in this filter.
   *
   * @param FilterExpression $metricFilter
   */
  public function setMetricFilter(FilterExpression $metricFilter)
  {
    $this->metricFilter = $metricFilter;
  }
  /**
   * @return FilterExpression
   */
  public function getMetricFilter()
  {
    return $this->metricFilter;
  }
  /**
   * The metrics requested and displayed.
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The minute ranges of event data to read. If unspecified, one minute range
   * for the last 30 minutes will be used. If multiple minute ranges are
   * requested, each response row will contain a zero based minute range index.
   * If two minute ranges overlap, the event data for the overlapping minutes is
   * included in the response rows for both minute ranges.
   *
   * @param MinuteRange[] $minuteRanges
   */
  public function setMinuteRanges($minuteRanges)
  {
    $this->minuteRanges = $minuteRanges;
  }
  /**
   * @return MinuteRange[]
   */
  public function getMinuteRanges()
  {
    return $this->minuteRanges;
  }
  /**
   * Specifies how rows are ordered in the response.
   *
   * @param OrderBy[] $orderBys
   */
  public function setOrderBys($orderBys)
  {
    $this->orderBys = $orderBys;
  }
  /**
   * @return OrderBy[]
   */
  public function getOrderBys()
  {
    return $this->orderBys;
  }
  /**
   * Toggles whether to return the current state of this Google Analytics
   * property's Realtime quota. Quota is returned in
   * [PropertyQuota](#PropertyQuota).
   *
   * @param bool $returnPropertyQuota
   */
  public function setReturnPropertyQuota($returnPropertyQuota)
  {
    $this->returnPropertyQuota = $returnPropertyQuota;
  }
  /**
   * @return bool
   */
  public function getReturnPropertyQuota()
  {
    return $this->returnPropertyQuota;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunRealtimeReportRequest::class, 'Google_Service_AnalyticsData_RunRealtimeReportRequest');
