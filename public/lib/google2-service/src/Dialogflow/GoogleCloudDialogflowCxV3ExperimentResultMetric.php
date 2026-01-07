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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ExperimentResultMetric extends \Google\Model
{
  /**
   * Count type unspecified.
   */
  public const COUNT_TYPE_COUNT_TYPE_UNSPECIFIED = 'COUNT_TYPE_UNSPECIFIED';
  /**
   * Total number of occurrences of a 'NO_MATCH'.
   */
  public const COUNT_TYPE_TOTAL_NO_MATCH_COUNT = 'TOTAL_NO_MATCH_COUNT';
  /**
   * Total number of turn counts.
   */
  public const COUNT_TYPE_TOTAL_TURN_COUNT = 'TOTAL_TURN_COUNT';
  /**
   * Average turn count in a session.
   */
  public const COUNT_TYPE_AVERAGE_TURN_COUNT = 'AVERAGE_TURN_COUNT';
  /**
   * Metric unspecified.
   */
  public const TYPE_METRIC_UNSPECIFIED = 'METRIC_UNSPECIFIED';
  /**
   * Percentage of contained sessions without user calling back in 24 hours.
   */
  public const TYPE_CONTAINED_SESSION_NO_CALLBACK_RATE = 'CONTAINED_SESSION_NO_CALLBACK_RATE';
  /**
   * Percentage of sessions that were handed to a human agent.
   */
  public const TYPE_LIVE_AGENT_HANDOFF_RATE = 'LIVE_AGENT_HANDOFF_RATE';
  /**
   * Percentage of sessions with the same user calling back.
   */
  public const TYPE_CALLBACK_SESSION_RATE = 'CALLBACK_SESSION_RATE';
  /**
   * Percentage of sessions where user hung up.
   */
  public const TYPE_ABANDONED_SESSION_RATE = 'ABANDONED_SESSION_RATE';
  /**
   * Percentage of sessions reached Dialogflow 'END_PAGE' or 'END_SESSION'.
   */
  public const TYPE_SESSION_END_RATE = 'SESSION_END_RATE';
  protected $confidenceIntervalType = GoogleCloudDialogflowCxV3ExperimentResultConfidenceInterval::class;
  protected $confidenceIntervalDataType = '';
  /**
   * Count value of a metric.
   *
   * @var 
   */
  public $count;
  /**
   * Count-based metric type. Only one of type or count_type is specified in
   * each Metric.
   *
   * @var string
   */
  public $countType;
  /**
   * Ratio value of a metric.
   *
   * @var 
   */
  public $ratio;
  /**
   * Ratio-based metric type. Only one of type or count_type is specified in
   * each Metric.
   *
   * @var string
   */
  public $type;

  /**
   * The probability that the treatment is better than all other treatments in
   * the experiment
   *
   * @param GoogleCloudDialogflowCxV3ExperimentResultConfidenceInterval $confidenceInterval
   */
  public function setConfidenceInterval(GoogleCloudDialogflowCxV3ExperimentResultConfidenceInterval $confidenceInterval)
  {
    $this->confidenceInterval = $confidenceInterval;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ExperimentResultConfidenceInterval
   */
  public function getConfidenceInterval()
  {
    return $this->confidenceInterval;
  }
  public function setCount($count)
  {
    $this->count = $count;
  }
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Count-based metric type. Only one of type or count_type is specified in
   * each Metric.
   *
   * Accepted values: COUNT_TYPE_UNSPECIFIED, TOTAL_NO_MATCH_COUNT,
   * TOTAL_TURN_COUNT, AVERAGE_TURN_COUNT
   *
   * @param self::COUNT_TYPE_* $countType
   */
  public function setCountType($countType)
  {
    $this->countType = $countType;
  }
  /**
   * @return self::COUNT_TYPE_*
   */
  public function getCountType()
  {
    return $this->countType;
  }
  public function setRatio($ratio)
  {
    $this->ratio = $ratio;
  }
  public function getRatio()
  {
    return $this->ratio;
  }
  /**
   * Ratio-based metric type. Only one of type or count_type is specified in
   * each Metric.
   *
   * Accepted values: METRIC_UNSPECIFIED, CONTAINED_SESSION_NO_CALLBACK_RATE,
   * LIVE_AGENT_HANDOFF_RATE, CALLBACK_SESSION_RATE, ABANDONED_SESSION_RATE,
   * SESSION_END_RATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExperimentResultMetric::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExperimentResultMetric');
