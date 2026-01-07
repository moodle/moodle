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

class GoogleCloudApigeeV1QueryTabularStatsRequest extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Required. List of dimension names to group the aggregations by.
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
   * Page size represents the number of rows.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Identifies a sequence of rows.
   *
   * @var string
   */
  public $pageToken;
  protected $timeRangeType = GoogleTypeInterval::class;
  protected $timeRangeDataType = '';

  /**
   * Required. List of dimension names to group the aggregations by.
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
   * Page size represents the number of rows.
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
   * Identifies a sequence of rows.
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
   * Time range for the stats.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1QueryTabularStatsRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1QueryTabularStatsRequest');
