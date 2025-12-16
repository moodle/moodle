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

class GoogleCloudIntegrationsV1alphaIntegrationAlertConfig extends \Google\Model
{
  /**
   * The default value. Metric type should always be set to one of the other
   * non-default values, otherwise it will result in an INVALID_ARGUMENT error.
   */
  public const METRIC_TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * Specifies alerting on the rate of errors for the enclosing integration.
   */
  public const METRIC_TYPE_EVENT_ERROR_RATE = 'EVENT_ERROR_RATE';
  /**
   * Specifies alerting on the rate of warnings for the enclosing integration.
   * Warnings use the same enum values as errors.
   */
  public const METRIC_TYPE_EVENT_WARNING_RATE = 'EVENT_WARNING_RATE';
  /**
   * Specifies alerting on the rate of errors for any task in the enclosing
   * integration.
   */
  public const METRIC_TYPE_TASK_ERROR_RATE = 'TASK_ERROR_RATE';
  /**
   * Specifies alerting on the rate of warnings for any task in the enclosing
   * integration.
   */
  public const METRIC_TYPE_TASK_WARNING_RATE = 'TASK_WARNING_RATE';
  /**
   * Specifies alerting on the rate of executions over all tasks in the
   * enclosing integration.
   */
  public const METRIC_TYPE_TASK_RATE = 'TASK_RATE';
  /**
   * Specifies alerting on the number of events executed in the given
   * aggregation_period.
   */
  public const METRIC_TYPE_EVENT_RATE = 'EVENT_RATE';
  /**
   * Specifies alerting on the average duration of executions for this
   * integration.
   */
  public const METRIC_TYPE_EVENT_AVERAGE_DURATION = 'EVENT_AVERAGE_DURATION';
  /**
   * Specifies alerting on the duration value of a particular percentile of
   * integration executions. E.g. If 10% or more of the integration executions
   * have durations above 5 seconds, alert.
   */
  public const METRIC_TYPE_EVENT_PERCENTILE_DURATION = 'EVENT_PERCENTILE_DURATION';
  /**
   * Specifies alerting on the average duration of any task in the enclosing
   * integration,
   */
  public const METRIC_TYPE_TASK_AVERAGE_DURATION = 'TASK_AVERAGE_DURATION';
  /**
   * Specifies alerting on the duration value of a particular percentile of any
   * task executions within the enclosing integration. E.g. If 10% or more of
   * the task executions in the integration have durations above 5 seconds,
   * alert.
   */
  public const METRIC_TYPE_TASK_PERCENTILE_DURATION = 'TASK_PERCENTILE_DURATION';
  /**
   * Default.
   */
  public const THRESHOLD_TYPE_THRESHOLD_TYPE_UNSPECIFIED = 'THRESHOLD_TYPE_UNSPECIFIED';
  /**
   * Note that this field will only trigger alerts if the integration specifying
   * it runs at least once in 24 hours (which is our in-memory retention period
   * for monarch streams). Also note that `aggregation_period` for this alert
   * configuration must be less than 24 hours. Min value threshold.
   */
  public const THRESHOLD_TYPE_EXPECTED_MIN = 'EXPECTED_MIN';
  /**
   * Max value threshold.
   */
  public const THRESHOLD_TYPE_EXPECTED_MAX = 'EXPECTED_MAX';
  /**
   * The period over which the metric value should be aggregated and evaluated.
   * Format is , where integer should be a positive integer and unit should be
   * one of (s,m,h,d,w) meaning (second, minute, hour, day, week). For an
   * EXPECTED_MIN threshold, this aggregation_period must be lesser than 24
   * hours.
   *
   * @var string
   */
  public $aggregationPeriod;
  /**
   * For how many contiguous aggregation periods should the expected min or max
   * be violated for the alert to be fired.
   *
   * @var int
   */
  public $alertThreshold;
  /**
   * Set to false by default. When set to true, the metrics are not aggregated
   * or pushed to Monarch for this integration alert.
   *
   * @var bool
   */
  public $disableAlert;
  /**
   * Name of the alert. This will be displayed in the alert subject. If set,
   * this name should be unique within the scope of the integration.
   *
   * @var string
   */
  public $displayName;
  /**
   * Should be specified only for *AVERAGE_DURATION and *PERCENTILE_DURATION
   * metrics. This member should be used to specify what duration value the
   * metrics should exceed for the alert to trigger.
   *
   * @var string
   */
  public $durationThreshold;
  /**
   * The type of metric.
   *
   * @var string
   */
  public $metricType;
  /**
   * For either events or tasks, depending on the type of alert, count only
   * final attempts, not retries.
   *
   * @var bool
   */
  public $onlyFinalAttempt;
  /**
   * The threshold type, whether lower(expected_min) or upper(expected_max), for
   * which this alert is being configured. If value falls below expected_min or
   * exceeds expected_max, an alert will be fired.
   *
   * @var string
   */
  public $thresholdType;
  protected $thresholdValueType = GoogleCloudIntegrationsV1alphaIntegrationAlertConfigThresholdValue::class;
  protected $thresholdValueDataType = '';

  /**
   * The period over which the metric value should be aggregated and evaluated.
   * Format is , where integer should be a positive integer and unit should be
   * one of (s,m,h,d,w) meaning (second, minute, hour, day, week). For an
   * EXPECTED_MIN threshold, this aggregation_period must be lesser than 24
   * hours.
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
   * For how many contiguous aggregation periods should the expected min or max
   * be violated for the alert to be fired.
   *
   * @param int $alertThreshold
   */
  public function setAlertThreshold($alertThreshold)
  {
    $this->alertThreshold = $alertThreshold;
  }
  /**
   * @return int
   */
  public function getAlertThreshold()
  {
    return $this->alertThreshold;
  }
  /**
   * Set to false by default. When set to true, the metrics are not aggregated
   * or pushed to Monarch for this integration alert.
   *
   * @param bool $disableAlert
   */
  public function setDisableAlert($disableAlert)
  {
    $this->disableAlert = $disableAlert;
  }
  /**
   * @return bool
   */
  public function getDisableAlert()
  {
    return $this->disableAlert;
  }
  /**
   * Name of the alert. This will be displayed in the alert subject. If set,
   * this name should be unique within the scope of the integration.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Should be specified only for *AVERAGE_DURATION and *PERCENTILE_DURATION
   * metrics. This member should be used to specify what duration value the
   * metrics should exceed for the alert to trigger.
   *
   * @param string $durationThreshold
   */
  public function setDurationThreshold($durationThreshold)
  {
    $this->durationThreshold = $durationThreshold;
  }
  /**
   * @return string
   */
  public function getDurationThreshold()
  {
    return $this->durationThreshold;
  }
  /**
   * The type of metric.
   *
   * Accepted values: METRIC_TYPE_UNSPECIFIED, EVENT_ERROR_RATE,
   * EVENT_WARNING_RATE, TASK_ERROR_RATE, TASK_WARNING_RATE, TASK_RATE,
   * EVENT_RATE, EVENT_AVERAGE_DURATION, EVENT_PERCENTILE_DURATION,
   * TASK_AVERAGE_DURATION, TASK_PERCENTILE_DURATION
   *
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
   * The threshold type, whether lower(expected_min) or upper(expected_max), for
   * which this alert is being configured. If value falls below expected_min or
   * exceeds expected_max, an alert will be fired.
   *
   * Accepted values: THRESHOLD_TYPE_UNSPECIFIED, EXPECTED_MIN, EXPECTED_MAX
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
   * @param GoogleCloudIntegrationsV1alphaIntegrationAlertConfigThresholdValue $thresholdValue
   */
  public function setThresholdValue(GoogleCloudIntegrationsV1alphaIntegrationAlertConfigThresholdValue $thresholdValue)
  {
    $this->thresholdValue = $thresholdValue;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationAlertConfigThresholdValue
   */
  public function getThresholdValue()
  {
    return $this->thresholdValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaIntegrationAlertConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaIntegrationAlertConfig');
