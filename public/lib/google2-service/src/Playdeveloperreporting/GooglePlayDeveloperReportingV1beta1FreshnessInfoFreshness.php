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

class GooglePlayDeveloperReportingV1beta1FreshnessInfoFreshness extends \Google\Model
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
   * Aggregation period for which data is available.
   *
   * @var string
   */
  public $aggregationPeriod;
  protected $latestEndTimeType = GoogleTypeDateTime::class;
  protected $latestEndTimeDataType = '';

  /**
   * Aggregation period for which data is available.
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
   * Latest end time for which data is available, for the aggregation period.
   * The time is specified in the metric set's default timezone. *Note:* time
   * ranges in TimelineSpec are represented as `start_time, end_time)`. For
   * example, if the latest available timeline data point for a `DAILY`
   * aggregation period is `2021-06-23 00:00:00 America/Los_Angeles`, the value
   * of this field would be `2021-06-24 00:00:00 America/Los_Angeles` so it can
   * be easily reused in [TimelineSpec.end_time.
   *
   * @param GoogleTypeDateTime $latestEndTime
   */
  public function setLatestEndTime(GoogleTypeDateTime $latestEndTime)
  {
    $this->latestEndTime = $latestEndTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getLatestEndTime()
  {
    return $this->latestEndTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1FreshnessInfoFreshness::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1FreshnessInfoFreshness');
