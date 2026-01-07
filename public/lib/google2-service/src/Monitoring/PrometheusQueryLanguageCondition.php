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

class PrometheusQueryLanguageCondition extends \Google\Model
{
  /**
   * Optional. The alerting rule name of this alert in the corresponding
   * Prometheus configuration file.Some external tools may require this field to
   * be populated correctly in order to refer to the original Prometheus
   * configuration file. The rule group name and the alert name are necessary to
   * update the relevant AlertPolicies in case the definition of the rule group
   * changes in the future.This field is optional. If this field is not empty,
   * then it must be a valid Prometheus label name
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels).
   * This field may not exceed 2048 Unicode characters in length.
   *
   * @var string
   */
  public $alertRule;
  /**
   * Optional. Whether to disable metric existence validation for this
   * condition.This allows alerting policies to be defined on metrics that do
   * not yet exist, improving advanced customer workflows such as configuring
   * alerting policies using Terraform.Users with the
   * monitoring.alertPolicyViewer role are able to see the name of the non-
   * existent metric in the alerting policy condition.
   *
   * @var bool
   */
  public $disableMetricValidation;
  /**
   * Optional. Alerts are considered firing once their PromQL expression was
   * evaluated to be "true" for this long. Alerts whose PromQL expression was
   * not evaluated to be "true" for long enough are considered pending. Must be
   * a non-negative duration or missing. This field is optional. Its default
   * value is zero.
   *
   * @var string
   */
  public $duration;
  /**
   * Optional. How often this rule should be evaluated. Must be a positive
   * multiple of 30 seconds or missing. This field is optional. Its default
   * value is 30 seconds. If this PrometheusQueryLanguageCondition was generated
   * from a Prometheus alerting rule, then this value should be taken from the
   * enclosing rule group.
   *
   * @var string
   */
  public $evaluationInterval;
  /**
   * Optional. Labels to add to or overwrite in the PromQL query result. Label
   * names must be valid
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels).
   * Label values can be templatized by using variables
   * (https://cloud.google.com/monitoring/alerts/doc-variables#doc-vars). The
   * only available variable names are the names of the labels in the PromQL
   * result, including "__name__" and "value". "labels" may be empty.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The PromQL expression to evaluate. Every evaluation cycle this
   * expression is evaluated at the current time, and all resultant time series
   * become pending/firing alerts. This field must not be empty.
   *
   * @var string
   */
  public $query;
  /**
   * Optional. The rule group name of this alert in the corresponding Prometheus
   * configuration file.Some external tools may require this field to be
   * populated correctly in order to refer to the original Prometheus
   * configuration file. The rule group name and the alert name are necessary to
   * update the relevant AlertPolicies in case the definition of the rule group
   * changes in the future.This field is optional. If this field is not empty,
   * then it must contain a valid UTF-8 string. This field may not exceed 2048
   * Unicode characters in length.
   *
   * @var string
   */
  public $ruleGroup;

  /**
   * Optional. The alerting rule name of this alert in the corresponding
   * Prometheus configuration file.Some external tools may require this field to
   * be populated correctly in order to refer to the original Prometheus
   * configuration file. The rule group name and the alert name are necessary to
   * update the relevant AlertPolicies in case the definition of the rule group
   * changes in the future.This field is optional. If this field is not empty,
   * then it must be a valid Prometheus label name
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels).
   * This field may not exceed 2048 Unicode characters in length.
   *
   * @param string $alertRule
   */
  public function setAlertRule($alertRule)
  {
    $this->alertRule = $alertRule;
  }
  /**
   * @return string
   */
  public function getAlertRule()
  {
    return $this->alertRule;
  }
  /**
   * Optional. Whether to disable metric existence validation for this
   * condition.This allows alerting policies to be defined on metrics that do
   * not yet exist, improving advanced customer workflows such as configuring
   * alerting policies using Terraform.Users with the
   * monitoring.alertPolicyViewer role are able to see the name of the non-
   * existent metric in the alerting policy condition.
   *
   * @param bool $disableMetricValidation
   */
  public function setDisableMetricValidation($disableMetricValidation)
  {
    $this->disableMetricValidation = $disableMetricValidation;
  }
  /**
   * @return bool
   */
  public function getDisableMetricValidation()
  {
    return $this->disableMetricValidation;
  }
  /**
   * Optional. Alerts are considered firing once their PromQL expression was
   * evaluated to be "true" for this long. Alerts whose PromQL expression was
   * not evaluated to be "true" for long enough are considered pending. Must be
   * a non-negative duration or missing. This field is optional. Its default
   * value is zero.
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
   * Optional. How often this rule should be evaluated. Must be a positive
   * multiple of 30 seconds or missing. This field is optional. Its default
   * value is 30 seconds. If this PrometheusQueryLanguageCondition was generated
   * from a Prometheus alerting rule, then this value should be taken from the
   * enclosing rule group.
   *
   * @param string $evaluationInterval
   */
  public function setEvaluationInterval($evaluationInterval)
  {
    $this->evaluationInterval = $evaluationInterval;
  }
  /**
   * @return string
   */
  public function getEvaluationInterval()
  {
    return $this->evaluationInterval;
  }
  /**
   * Optional. Labels to add to or overwrite in the PromQL query result. Label
   * names must be valid
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels).
   * Label values can be templatized by using variables
   * (https://cloud.google.com/monitoring/alerts/doc-variables#doc-vars). The
   * only available variable names are the names of the labels in the PromQL
   * result, including "__name__" and "value". "labels" may be empty.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The PromQL expression to evaluate. Every evaluation cycle this
   * expression is evaluated at the current time, and all resultant time series
   * become pending/firing alerts. This field must not be empty.
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
   * Optional. The rule group name of this alert in the corresponding Prometheus
   * configuration file.Some external tools may require this field to be
   * populated correctly in order to refer to the original Prometheus
   * configuration file. The rule group name and the alert name are necessary to
   * update the relevant AlertPolicies in case the definition of the rule group
   * changes in the future.This field is optional. If this field is not empty,
   * then it must contain a valid UTF-8 string. This field may not exceed 2048
   * Unicode characters in length.
   *
   * @param string $ruleGroup
   */
  public function setRuleGroup($ruleGroup)
  {
    $this->ruleGroup = $ruleGroup;
  }
  /**
   * @return string
   */
  public function getRuleGroup()
  {
    return $this->ruleGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrometheusQueryLanguageCondition::class, 'Google_Service_Monitoring_PrometheusQueryLanguageCondition');
