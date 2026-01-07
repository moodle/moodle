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

class MonitoringQueryLanguageCondition extends \Google\Model
{
  /**
   * An unspecified evaluation missing data option. Equivalent to
   * EVALUATION_MISSING_DATA_NO_OP.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_UNSPECIFIED = 'EVALUATION_MISSING_DATA_UNSPECIFIED';
  /**
   * If there is no data to evaluate the condition, then evaluate the condition
   * as false.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_INACTIVE = 'EVALUATION_MISSING_DATA_INACTIVE';
  /**
   * If there is no data to evaluate the condition, then evaluate the condition
   * as true.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_ACTIVE = 'EVALUATION_MISSING_DATA_ACTIVE';
  /**
   * Do not evaluate the condition to any value if there is no data.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_NO_OP = 'EVALUATION_MISSING_DATA_NO_OP';
  /**
   * Optional. The amount of time that a time series must violate the threshold
   * to be considered failing. Currently, only values that are a multiple of a
   * minute--e.g., 0, 60, 120, or 300 seconds--are supported. If an invalid
   * value is given, an error will be returned. When choosing a duration, it is
   * useful to keep in mind the frequency of the underlying time series data
   * (which may also be affected by any alignments specified in the aggregations
   * field); a good duration is long enough so that a single outlier does not
   * generate spurious alerts, but short enough that unhealthy states are
   * detected and alerted on quickly. The default value is zero.
   *
   * @var string
   */
  public $duration;
  /**
   * A condition control that determines how metric-threshold conditions are
   * evaluated when data stops arriving.
   *
   * @var string
   */
  public $evaluationMissingData;
  /**
   * Monitoring Query Language (https://cloud.google.com/monitoring/mql) query
   * that outputs a boolean stream.
   *
   * @var string
   */
  public $query;
  protected $triggerType = Trigger::class;
  protected $triggerDataType = '';

  /**
   * Optional. The amount of time that a time series must violate the threshold
   * to be considered failing. Currently, only values that are a multiple of a
   * minute--e.g., 0, 60, 120, or 300 seconds--are supported. If an invalid
   * value is given, an error will be returned. When choosing a duration, it is
   * useful to keep in mind the frequency of the underlying time series data
   * (which may also be affected by any alignments specified in the aggregations
   * field); a good duration is long enough so that a single outlier does not
   * generate spurious alerts, but short enough that unhealthy states are
   * detected and alerted on quickly. The default value is zero.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * A condition control that determines how metric-threshold conditions are
   * evaluated when data stops arriving.
   *
   * Accepted values: EVALUATION_MISSING_DATA_UNSPECIFIED,
   * EVALUATION_MISSING_DATA_INACTIVE, EVALUATION_MISSING_DATA_ACTIVE,
   * EVALUATION_MISSING_DATA_NO_OP
   *
   * @param self::EVALUATION_MISSING_DATA_* $evaluationMissingData
   */
  public function setEvaluationMissingData($evaluationMissingData)
  {
    $this->evaluationMissingData = $evaluationMissingData;
  }
  /**
   * @return self::EVALUATION_MISSING_DATA_*
   */
  public function getEvaluationMissingData()
  {
    return $this->evaluationMissingData;
  }
  /**
   * Monitoring Query Language (https://cloud.google.com/monitoring/mql) query
   * that outputs a boolean stream.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The number/percent of time series for which the comparison must hold in
   * order for the condition to trigger. If unspecified, then the condition will
   * trigger if the comparison is true for any of the time series that have been
   * identified by filter and aggregations, or by the ratio, if
   * denominator_filter and denominator_aggregations are specified.
   *
   * @param Trigger $trigger
   */
  public function setTrigger(Trigger $trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return Trigger
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitoringQueryLanguageCondition::class, 'Google_Service_Monitoring_MonitoringQueryLanguageCondition');
