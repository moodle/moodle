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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1QueryMetricsRequest extends \Google\Collection
{
  /**
   * The time granularity is unspecified and will default to NONE.
   */
  public const TIME_GRANULARITY_TIME_GRANULARITY_UNSPECIFIED = 'TIME_GRANULARITY_UNSPECIFIED';
  /**
   * No time granularity. The response won't contain a time series. This is the
   * default value if no time granularity is specified.
   */
  public const TIME_GRANULARITY_NONE = 'NONE';
  /**
   * Data points in the time series will aggregate at a daily granularity. 1 day
   * means [midnight to midnight).
   */
  public const TIME_GRANULARITY_DAILY = 'DAILY';
  /**
   * Data points in the time series will aggregate at a daily granularity. 1
   * HOUR means [01:00 to 02:00).
   */
  public const TIME_GRANULARITY_HOURLY = 'HOURLY';
  /**
   * Data points in the time series will aggregate at a daily granularity.
   * PER_MINUTE means [01:00 to 01:01).
   */
  public const TIME_GRANULARITY_PER_MINUTE = 'PER_MINUTE';
  /**
   * Data points in the time series will aggregate at a 1 minute granularity.
   * PER_5_MINUTES means [01:00 to 01:05).
   */
  public const TIME_GRANULARITY_PER_5_MINUTES = 'PER_5_MINUTES';
  /**
   * Data points in the time series will aggregate at a monthly granularity. 1
   * MONTH means [01st of the month to 1st of the next month).
   */
  public const TIME_GRANULARITY_MONTHLY = 'MONTHLY';
  protected $collection_key = 'dimensions';
  protected $dimensionsType = GoogleCloudContactcenterinsightsV1Dimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * Required. Filter to select a subset of conversations to compute the
   * metrics. Must specify a window of the conversation create time to compute
   * the metrics. The returned metrics will be from the range [DATE(starting
   * create time), DATE(ending create time)).
   *
   * @var string
   */
  public $filter;
  /**
   * Measures to return. Defaults to all measures if this field is unspecified.
   * A valid mask should traverse from the `measure` field from the response.
   * For example, a path from a measure mask to get the conversation count is
   * "conversation_measure.count".
   *
   * @var string
   */
  public $measureMask;
  /**
   * The time granularity of each data point in the time series. Defaults to
   * NONE if this field is unspecified.
   *
   * @var string
   */
  public $timeGranularity;

  /**
   * The dimensions that determine the grouping key for the query. Defaults to
   * no dimension if this field is unspecified. If a dimension is specified, its
   * key must also be specified. Each dimension's key must be unique. If a time
   * granularity is also specified, metric values in the dimension will be
   * bucketed by this granularity. Up to one dimension is supported for now.
   *
   * @param GoogleCloudContactcenterinsightsV1Dimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1Dimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Required. Filter to select a subset of conversations to compute the
   * metrics. Must specify a window of the conversation create time to compute
   * the metrics. The returned metrics will be from the range [DATE(starting
   * create time), DATE(ending create time)).
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
   * Measures to return. Defaults to all measures if this field is unspecified.
   * A valid mask should traverse from the `measure` field from the response.
   * For example, a path from a measure mask to get the conversation count is
   * "conversation_measure.count".
   *
   * @param string $measureMask
   */
  public function setMeasureMask($measureMask)
  {
    $this->measureMask = $measureMask;
  }
  /**
   * @return string
   */
  public function getMeasureMask()
  {
    return $this->measureMask;
  }
  /**
   * The time granularity of each data point in the time series. Defaults to
   * NONE if this field is unspecified.
   *
   * Accepted values: TIME_GRANULARITY_UNSPECIFIED, NONE, DAILY, HOURLY,
   * PER_MINUTE, PER_5_MINUTES, MONTHLY
   *
   * @param self::TIME_GRANULARITY_* $timeGranularity
   */
  public function setTimeGranularity($timeGranularity)
  {
    $this->timeGranularity = $timeGranularity;
  }
  /**
   * @return self::TIME_GRANULARITY_*
   */
  public function getTimeGranularity()
  {
    return $this->timeGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QueryMetricsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QueryMetricsRequest');
