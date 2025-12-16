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

class GooglePlayDeveloperReportingV1beta1MetricsRow extends \Google\Collection
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
  protected $collection_key = 'metrics';
  /**
   * Granularity of the aggregation period of the row.
   *
   * @var string
   */
  public $aggregationPeriod;
  protected $dimensionsType = GooglePlayDeveloperReportingV1beta1DimensionValue::class;
  protected $dimensionsDataType = 'array';
  protected $metricsType = GooglePlayDeveloperReportingV1beta1MetricValue::class;
  protected $metricsDataType = 'array';
  protected $startTimeType = GoogleTypeDateTime::class;
  protected $startTimeDataType = '';

  /**
   * Granularity of the aggregation period of the row.
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
   * Dimension columns in the row.
   *
   * @param GooglePlayDeveloperReportingV1beta1DimensionValue[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1DimensionValue[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Metric columns in the row.
   *
   * @param GooglePlayDeveloperReportingV1beta1MetricValue[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1MetricValue[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Starting date (and time for hourly aggregation) of the period covered by
   * this row.
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
class_alias(GooglePlayDeveloperReportingV1beta1MetricsRow::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1MetricsRow');
