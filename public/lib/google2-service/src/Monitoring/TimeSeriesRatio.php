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

namespace Google\Service\Monitoring;

class TimeSeriesRatio extends \Google\Model
{
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying bad service, either demanded service
   * that was not provided or demanded service that was of inadequate quality.
   * Must have ValueType = DOUBLE or ValueType = INT64 and must have MetricKind
   * = DELTA or MetricKind = CUMULATIVE.
   *
   * @var string
   */
  public $badServiceFilter;
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying good service provided. Must have
   * ValueType = DOUBLE or ValueType = INT64 and must have MetricKind = DELTA or
   * MetricKind = CUMULATIVE.
   *
   * @var string
   */
  public $goodServiceFilter;
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying total demanded service. Must have
   * ValueType = DOUBLE or ValueType = INT64 and must have MetricKind = DELTA or
   * MetricKind = CUMULATIVE.
   *
   * @var string
   */
  public $totalServiceFilter;

  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying bad service, either demanded service
   * that was not provided or demanded service that was of inadequate quality.
   * Must have ValueType = DOUBLE or ValueType = INT64 and must have MetricKind
   * = DELTA or MetricKind = CUMULATIVE.
   *
   * @param string $badServiceFilter
   */
  public function setBadServiceFilter($badServiceFilter)
  {
    $this->badServiceFilter = $badServiceFilter;
  }
  /**
   * @return string
   */
  public function getBadServiceFilter()
  {
    return $this->badServiceFilter;
  }
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying good service provided. Must have
   * ValueType = DOUBLE or ValueType = INT64 and must have MetricKind = DELTA or
   * MetricKind = CUMULATIVE.
   *
   * @param string $goodServiceFilter
   */
  public function setGoodServiceFilter($goodServiceFilter)
  {
    $this->goodServiceFilter = $goodServiceFilter;
  }
  /**
   * @return string
   */
  public function getGoodServiceFilter()
  {
    return $this->goodServiceFilter;
  }
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries quantifying total demanded service. Must have
   * ValueType = DOUBLE or ValueType = INT64 and must have MetricKind = DELTA or
   * MetricKind = CUMULATIVE.
   *
   * @param string $totalServiceFilter
   */
  public function setTotalServiceFilter($totalServiceFilter)
  {
    $this->totalServiceFilter = $totalServiceFilter;
  }
  /**
   * @return string
   */
  public function getTotalServiceFilter()
  {
    return $this->totalServiceFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeSeriesRatio::class, 'Google_Service_Monitoring_TimeSeriesRatio');
