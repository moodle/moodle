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

class EnterpriseCrmEventbusProtoWorkflowAlertConfig extends \Google\Model
{
  /**
   * The default value. Metric type should always be set to one of the other
   * non-default values, otherwise it will result in an INVALID_ARGUMENT error.
   */
  public const METRIC_TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * Specifies alerting on the rate of errors for the enclosing workflow.
   */
  public const METRIC_TYPE_EVENT_ERROR_RATE = 'EVENT_ERROR_RATE';
  /**
   * Specifies alerting on the rate of warnings for the enclosing workflow.
   * Warnings use the same enum values as errors.
   */
  public const METRIC_TYPE_EVENT_WARNING_RATE = 'EVENT_WARNING_RATE';
  /**
   * Specifies alerting on the rate of errors for any task in the enclosing
   * workflow.
   */
  public const METRIC_TYPE_TASK_ERROR_RATE = 'TASK_ERROR_RATE';
  /**
   * Specifies alerting on the rate of warnings for any task in the enclosing
   * workflow.
   */
  public const METRIC_TYPE_TASK_WARNING_RATE = 'TASK_WARNING_RATE';
  /**
   * Specifies alerting on the rate of executions over all tasks in the
   * enclosing workflow.
   */
  public const METRIC_TYPE_TASK_RATE = 'TASK_RATE';
  /**
   * Specifies alerting on the number of events executed in the given
   * aggregation_period.
   */
  public const METRIC_TYPE_EVENT_RATE = 'EVENT_RATE';
  /**
   * Specifies alerting on the average duration of executions for this workflow.
   */
  public const METRIC_TYPE_EVENT_AVERAGE_DURATION = 'EVENT_AVERAGE_DURATION';
  /**
   * Specifies alerting on the duration value of a particular percentile of
   * workflow executions. E.g. If 10% or more of the workflow executions have
   * durations above 5 seconds, alert.
   */
  public const METRIC_TYPE_EVENT_PERCENTILE_DURATION = 'EVENT_PERCENTILE_DURATION';
  /**
   * Specifies alerting on the average duration of any task in the enclosing
   * workflow,
   */
  public const METRIC_TYPE_TASK_AVERAGE_DURATION = 'TASK_AVERAGE_DURATION';
  /**
   * Specifies alerting on the duration value of a particular percentile of any
   * task executions within the enclosing workflow. E.g. If 10% or more of the
   * task executions in the workflow have durations above 5 seconds, alert.
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
   * For an EXPECTED_MIN threshold, this aggregation_period must be lesser than
   * 24 hours.
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
   * If set, this name should be unique within the scope of the workflow.
   *
   * @var string
   */
  public $alertName;
  /**
   * Client associated with this alert configuration.
   *
   * @var string
   */
  public $clientId;
  /**
   * Should be specified only for *AVERAGE_DURATION and *PERCENTILE_DURATION
   * metrics. This member should be used to specify what duration value the
   * metrics should exceed for the alert to trigger.
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
   * For either events or tasks, depending on the type of alert, count only
   * final attempts, not retries.
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
   * The threshold type, whether lower(expected_min) or upper(expected_max), for
   * which this alert is being configured. If value falls below expected_min or
   * exceeds expected_max, an alert will be fired.
   *
   * @var string
   */
  public $thresholdType;
  protected $thresholdValueType = EnterpriseCrmEventbusProtoBaseAlertConfigThresholdValue::class;
  protected $thresholdValueDataType = '';
  protected $warningEnumListType = EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList::class;
  protected $warningEnumListDataType = '';

  /**
   * For an EXPECTED_MIN threshold, this aggregation_period must be lesser than
   * 24 hours.
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
   * If set, this name should be unique within the scope of the workflow.
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
   * Client associated with this alert configuration.
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
   * Should be specified only for *AVERAGE_DURATION and *PERCENTILE_DURATION
   * metrics. This member should be used to specify what duration value the
   * metrics should exceed for the alert to trigger.
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
   * For either events or tasks, depending on the type of alert, count only
   * final attempts, not retries.
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
   * The threshold type, whether lower(expected_min) or upper(expected_max), for
   * which this alert is being configured. If value falls below expected_min or
   * exceeds expected_max, an alert will be fired.
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
class_alias(EnterpriseCrmEventbusProtoWorkflowAlertConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoWorkflowAlertConfig');
