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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoTaskAlertConfig extends \Google\Model
{
  /**
   * The default value. Metric type should always be set to one of the other
   * non-default values, otherwise it will result in an INVALID_ARGUMENT error.
   */
  public const METRIC_TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * Specifies alerting on the rate of errors (potentially for a specific set of
   * enum values) for the enclosing TaskConfig.
   */
  public const METRIC_TYPE_TASK_ERROR_RATE = 'TASK_ERROR_RATE';
  /**
   * Specifies alerting on the rate of warnings (potentially for a specific set
   * of enum values) for the enclosing TaskConfig. Warnings use the same enum
   * values as errors.
   */
  public const METRIC_TYPE_TASK_WARNING_RATE = 'TASK_WARNING_RATE';
  /**
   * Specifies alerting on the number of instances for the enclosing TaskConfig
   * executed in the given aggregation_period.
   */
  public const METRIC_TYPE_TASK_RATE = 'TASK_RATE';
  /**
   * Specifies alerting on the average duration of execution for the enclosing
   * task.
   */
  public const METRIC_TYPE_TASK_AVERAGE_DURATION = 'TASK_AVERAGE_DURATION';
  /**
   * Specifies alerting on the duration of a particular percentile of task
   * executions. E.g. If 10% or more of the task executions have durations above
   * 5 seconds, alert.
   */
  public const METRIC_TYPE_TASK_PERCENTILE_DURATION = 'TASK_PERCENTILE_DURATION';
  public const THRESHOLD_TYPE_UNSPECIFIED_THRESHOLD_TYPE = 'UNSPECIFIED_THRESHOLD_TYPE';
  /**
   * Note that this field will only trigger alerts if the workflow specifying it
   * runs at least once in 24 hours (which is our in-memory retention period for
   * monarch streams). Also note that `aggregation_period` for this alert
   * configuration must be less than 24 hours.
   */
  public const THRESHOLD_TYPE_EXPECTED_MIN = 'EXPECTED_MIN';
  public const THRESHOLD_TYPE_EXPECTED_MAX = 'EXPECTED_MAX';
  /**
   * The period over which the metric value should be aggregated and evaluated.
   * Format is , where integer should be a positive integer and unit should be
   * one of (s,m,h,d,w) meaning (second, minute, hour, day, week).
   *
   * @var string
   */
  public $aggregationPeriod;
  /**
   * Set to false by default. When set to true, the metrics are not aggregated
   * or pushed to Monarch for this workflow alert.
   *
   * @var bool
   */
  public $alertDisabled;
  /**
   * A name to identify this alert. This will be displayed in the alert subject.
   * If set, this name should be unique in within the scope of the containing
   * workflow.
   *
   * @var string
   */
  public $alertName;
  /**
   * Client associated with this alert configuration. Must be a client enabled
   * in one of the containing workflow's triggers.
   *
   * @var string
   */
  public $clientId;
  /**
   * Should be specified only for TASK_AVERAGE_DURATION and
   * TASK_PERCENTILE_DURATION metrics. This member should be used to specify
   * what duration value the metrics should exceed for the alert to trigger.
   *
   * @var string
   */
  public $durationThresholdMs;
  protected $errorEnumListType = EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList::class;
  protected $errorEnumListDataType = '';
  /**
   * @var string
   */
  public $metricType;
  /**
   * For how many contiguous aggregation periods should the expected min or max
   * be violated for the alert to be fired.
   *
   * @var int
   */
  public $numAggregationPeriods;
  /**
   * Only count final task attempts, not retries.
   *
   * @var bool
   */
  public $onlyFinalAttempt;
  /**
   * Link to a playbook for resolving the issue that triggered this alert.
   *
   * @var string
   */
  public $playbookUrl;
  /**
   * The threshold type for which this alert is being configured. If value falls
   * below expected_min or exceeds expected_max, an alert will be fired.
   *
   * @var string
   */
  public $thresholdType;
  protected $thresholdValueType = EnterpriseCrmEventbusProtoBaseAlertConfigThresholdValue::class;
  protected $thresholdValueDataType = '';
  protected $warningEnumListType = EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList::class;
  protected $warningEnumListDataType = '';

  /**
   * The period over which the metric value should be aggregated and evaluated.
   * Format is , where integer should be a positive integer and unit should be
   * one of (s,m,h,d,w) meaning (second, minute, hour, day, week).
   *
   * @param string $aggregationPeriod
   */
  public function setAggregationPeriod($aggregationPeriod)
  {
    $this->aggregationPeriod = $aggregationPeriod;
  }
  /**
   * @return string
   */
  public function getAggregationPeriod()
  {
    return $this->aggregationPeriod;
  }
  /**
   * Set to false by default. When set to true, the metrics are not aggregated
   * or pushed to Monarch for this workflow alert.
   *
   * @param bool $alertDisabled
   */
  public function setAlertDisabled($alertDisabled)
  {
    $this->alertDisabled = $alertDisabled;
  }
  /**
   * @return bool
   */
  public function getAlertDisabled()
  {
    return $this->alertDisabled;
  }
  /**
   * A name to identify this alert. This will be displayed in the alert subject.
   * If set, this name should be unique in within the scope of the containing
   * workflow.
   *
   * @param string $alertName
   */
  public function setAlertName($alertName)
  {
    $this->alertName = $alertName;
  }
  /**
   * @return string
   */
  public function getAlertName()
  {
    return $this->alertName;
  }
  /**
   * Client associated with this alert configuration. Must be a client enabled
   * in one of the containing workflow's triggers.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Should be specified only for TASK_AVERAGE_DURATION and
   * TASK_PERCENTILE_DURATION metrics. This member should be used to specify
   * what duration value the metrics should exceed for the alert to trigger.
   *
   * @param string $durationThresholdMs
   */
  public function setDurationThresholdMs($durationThresholdMs)
  {
    $this->durationThresholdMs = $durationThresholdMs;
  }
  /**
   * @return string
   */
  public function getDurationThresholdMs()
  {
    return $this->durationThresholdMs;
  }
  /**
   * @param EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList $errorEnumList
   */
  public function setErrorEnumList(EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList $errorEnumList)
  {
    $this->errorEnumList = $errorEnumList;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList
   */
  public function getErrorEnumList()
  {
    return $this->errorEnumList;
  }
  /**
   * @param self::METRIC_TYPE_* $metricType
   */
  public function setMetricType($metricType)
  {
    $this->metricType = $metricType;
  }
  /**
   * @return self::METRIC_TYPE_*
   */
  public function getMetricType()
  {
    return $this->metricType;
  }
  /**
   * For how many contiguous aggregation periods should the expected min or max
   * be violated for the alert to be fired.
   *
   * @param int $numAggregationPeriods
   */
  public function setNumAggregationPeriods($numAggregationPeriods)
  {
    $this->numAggregationPeriods = $numAggregationPeriods;
  }
  /**
   * @return int
   */
  public function getNumAggregationPeriods()
  {
    return $this->numAggregationPeriods;
  }
  /**
   * Only count final task attempts, not retries.
   *
   * @param bool $onlyFinalAttempt
   */
  public function setOnlyFinalAttempt($onlyFinalAttempt)
  {
    $this->onlyFinalAttempt = $onlyFinalAttempt;
  }
  /**
   * @return bool
   */
  public function getOnlyFinalAttempt()
  {
    return $this->onlyFinalAttempt;
  }
  /**
   * Link to a playbook for resolving the issue that triggered this alert.
   *
   * @param string $playbookUrl
   */
  public function setPlaybookUrl($playbookUrl)
  {
    $this->playbookUrl = $playbookUrl;
  }
  /**
   * @return string
   */
  public function getPlaybookUrl()
  {
    return $this->playbookUrl;
  }
  /**
   * The threshold type for which this alert is being configured. If value falls
   * below expected_min or exceeds expected_max, an alert will be fired.
   *
   * Accepted values: UNSPECIFIED_THRESHOLD_TYPE, EXPECTED_MIN, EXPECTED_MAX
   *
   * @param self::THRESHOLD_TYPE_* $thresholdType
   */
  public function setThresholdType($thresholdType)
  {
    $this->thresholdType = $thresholdType;
  }
  /**
   * @return self::THRESHOLD_TYPE_*
   */
  public function getThresholdType()
  {
    return $this->thresholdType;
  }
  /**
   * The metric value, above or below which the alert should be triggered.
   *
   * @param EnterpriseCrmEventbusProtoBaseAlertConfigThresholdValue $thresholdValue
   */
  public function setThresholdValue(EnterpriseCrmEventbusProtoBaseAlertConfigThresholdValue $thresholdValue)
  {
    $this->thresholdValue = $thresholdValue;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBaseAlertConfigThresholdValue
   */
  public function getThresholdValue()
  {
    return $this->thresholdValue;
  }
  /**
   * @param EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList $warningEnumList
   */
  public function setWarningEnumList(EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList $warningEnumList)
  {
    $this->warningEnumList = $warningEnumList;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList
   */
  public function getWarningEnumList()
  {
    return $this->warningEnumList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoTaskAlertConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoTaskAlertConfig');
