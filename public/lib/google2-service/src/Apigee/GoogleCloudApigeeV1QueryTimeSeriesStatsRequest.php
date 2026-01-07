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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1QueryTimeSeriesStatsRequest extends \Google\Collection
{
  /**
   * Unspecified order. Default is Descending.
   */
  public const TIMESTAMP_ORDER_ORDER_UNSPECIFIED = 'ORDER_UNSPECIFIED';
  /**
   * Ascending sort order.
   */
  public const TIMESTAMP_ORDER_ASCENDING = 'ASCENDING';
  /**
   * Descending sort order.
   */
  public const TIMESTAMP_ORDER_DESCENDING = 'DESCENDING';
  /**
   * Unspecified window size. Default is 1 hour.
   */
  public const WINDOW_SIZE_WINDOW_SIZE_UNSPECIFIED = 'WINDOW_SIZE_UNSPECIFIED';
  /**
   * 1 Minute window
   */
  public const WINDOW_SIZE_MINUTE = 'MINUTE';
  /**
   * 1 Hour window
   */
  public const WINDOW_SIZE_HOUR = 'HOUR';
  /**
   * 1 Day window
   */
  public const WINDOW_SIZE_DAY = 'DAY';
  /**
   * 1 Month window
   */
  public const WINDOW_SIZE_MONTH = 'MONTH';
  protected $collection_key = 'metrics';
  /**
   * List of dimension names to group the aggregations by. If no dimensions are
   * passed, a single trend line representing the requested metric aggregations
   * grouped by environment is returned.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * Filter further on specific dimension values. Follows the same grammar as
   * custom report's filter expressions. Example, apiproxy eq 'foobar'.
   * https://cloud.google.com/apigee/docs/api-platform/analytics/analytics-
   * reference#filters
   *
   * @var string
   */
  public $filter;
  protected $metricsType = GoogleCloudApigeeV1MetricAggregation::class;
  protected $metricsDataType = 'array';
  /**
   * Page size represents the number of time series sequences, one per unique
   * set of dimensions and their values.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Page token stands for a specific collection of time series sequences.
   *
   * @var string
   */
  public $pageToken;
  protected $timeRangeType = GoogleTypeInterval::class;
  protected $timeRangeDataType = '';
  /**
   * Order the sequences in increasing or decreasing order of timestamps.
   * Default is descending order of timestamps (latest first).
   *
   * @var string
   */
  public $timestampOrder;
  /**
   * Time buckets to group the stats by.
   *
   * @var string
   */
  public $windowSize;

  /**
   * List of dimension names to group the aggregations by. If no dimensions are
   * passed, a single trend line representing the requested metric aggregations
   * grouped by environment is returned.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Filter further on specific dimension values. Follows the same grammar as
   * custom report's filter expressions. Example, apiproxy eq 'foobar'.
   * https://cloud.google.com/apigee/docs/api-platform/analytics/analytics-
   * reference#filters
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. List of metrics and their aggregations.
   *
   * @param GoogleCloudApigeeV1MetricAggregation[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudApigeeV1MetricAggregation[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Page size represents the number of time series sequences, one per unique
   * set of dimensions and their values.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Page token stands for a specific collection of time series sequences.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. Time range for the stats.
   *
   * @param GoogleTypeInterval $timeRange
   */
  public function setTimeRange(GoogleTypeInterval $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return GoogleTypeInterval
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
  /**
   * Order the sequences in increasing or decreasing order of timestamps.
   * Default is descending order of timestamps (latest first).
   *
   * Accepted values: ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::TIMESTAMP_ORDER_* $timestampOrder
   */
  public function setTimestampOrder($timestampOrder)
  {
    $this->timestampOrder = $timestampOrder;
  }
  /**
   * @return self::TIMESTAMP_ORDER_*
   */
  public function getTimestampOrder()
  {
    return $this->timestampOrder;
  }
  /**
   * Time buckets to group the stats by.
   *
   * Accepted values: WINDOW_SIZE_UNSPECIFIED, MINUTE, HOUR, DAY, MONTH
   *
   * @param self::WINDOW_SIZE_* $windowSize
   */
  public function setWindowSize($windowSize)
  {
    $this->windowSize = $windowSize;
  }
  /**
   * @return self::WINDOW_SIZE_*
   */
  public function getWindowSize()
  {
    return $this->windowSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1QueryTimeSeriesStatsRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1QueryTimeSeriesStatsRequest');
