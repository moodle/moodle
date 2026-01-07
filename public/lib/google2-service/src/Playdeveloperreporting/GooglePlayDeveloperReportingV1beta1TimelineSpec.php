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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1TimelineSpec extends \Google\Model
{
  /**
   * Unspecified granularity.
   */
  public const AGGREGATION_PERIOD_AGGREGATION_PERIOD_UNSPECIFIED = 'AGGREGATION_PERIOD_UNSPECIFIED';
  /**
   * Data is aggregated in hourly intervals.
   */
  public const AGGREGATION_PERIOD_HOURLY = 'HOURLY';
  /**
   * Data is aggregated in daily intervals.
   */
  public const AGGREGATION_PERIOD_DAILY = 'DAILY';
  /**
   * Data is aggregated over the full timeline range. Effectively this produces
   * a single value rather than a timeline.
   */
  public const AGGREGATION_PERIOD_FULL_RANGE = 'FULL_RANGE';
  /**
   * Type of the aggregation period of the datapoints in the timeline. Intervals
   * are identified by the date and time at the start of the interval.
   *
   * @var string
   */
  public $aggregationPeriod;
  protected $endTimeType = GoogleTypeDateTime::class;
  protected $endTimeDataType = '';
  protected $startTimeType = GoogleTypeDateTime::class;
  protected $startTimeDataType = '';

  /**
   * Type of the aggregation period of the datapoints in the timeline. Intervals
   * are identified by the date and time at the start of the interval.
   *
   * Accepted values: AGGREGATION_PERIOD_UNSPECIFIED, HOURLY, DAILY, FULL_RANGE
   *
   * @param self::AGGREGATION_PERIOD_* $aggregationPeriod
   */
  public function setAggregationPeriod($aggregationPeriod)
  {
    $this->aggregationPeriod = $aggregationPeriod;
  }
  /**
   * @return self::AGGREGATION_PERIOD_*
   */
  public function getAggregationPeriod()
  {
    return $this->aggregationPeriod;
  }
  /**
   * Ending datapoint of the timeline (exclusive). See start_time for
   * restrictions. The timezone of the end point must match the timezone of the
   * start point.
   *
   * @param GoogleTypeDateTime $endTime
   */
  public function setEndTime(GoogleTypeDateTime $endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Starting datapoint of the timeline (inclusive). Must be aligned to the
   * aggregation period as follows: * HOURLY: the 'minutes', 'seconds' and
   * 'nanos' fields must be unset. The time_zone can be left unset (defaults to
   * UTC) or set explicitly to "UTC". Setting any other utc_offset or timezone
   * id will result in a validation error. * DAILY: the 'hours', 'minutes',
   * 'seconds' and 'nanos' fields must be unset. Different metric sets support
   * different timezones. It can be left unset to use the default timezone
   * specified by the metric set. The timezone of the end point must match the
   * timezone of the start point.
   *
   * @param GoogleTypeDateTime $startTime
   */
  public function setStartTime(GoogleTypeDateTime $startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1TimelineSpec::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1TimelineSpec');
