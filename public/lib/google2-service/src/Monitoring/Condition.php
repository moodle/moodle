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

class Condition extends \Google\Model
{
  protected $conditionAbsentType = MetricAbsence::class;
  protected $conditionAbsentDataType = '';
  protected $conditionMatchedLogType = LogMatch::class;
  protected $conditionMatchedLogDataType = '';
  protected $conditionMonitoringQueryLanguageType = MonitoringQueryLanguageCondition::class;
  protected $conditionMonitoringQueryLanguageDataType = '';
  protected $conditionPrometheusQueryLanguageType = PrometheusQueryLanguageCondition::class;
  protected $conditionPrometheusQueryLanguageDataType = '';
  protected $conditionSqlType = SqlCondition::class;
  protected $conditionSqlDataType = '';
  protected $conditionThresholdType = MetricThreshold::class;
  protected $conditionThresholdDataType = '';
  /**
   * A short name or phrase used to identify the condition in dashboards,
   * notifications, and incidents. To avoid confusion, don't use the same
   * display name for multiple conditions in the same policy.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required if the condition exists. The unique resource name for this
   * condition. Its format is: projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[PO
   * LICY_ID]/conditions/[CONDITION_ID] [CONDITION_ID] is assigned by Cloud
   * Monitoring when the condition is created as part of a new or updated
   * alerting policy.When calling the alertPolicies.create method, do not
   * include the name field in the conditions of the requested alerting policy.
   * Cloud Monitoring creates the condition identifiers and includes them in the
   * new policy.When calling the alertPolicies.update method to update a policy,
   * including a condition name causes the existing condition to be updated.
   * Conditions without names are added to the updated policy. Existing
   * conditions are deleted if they are not updated.Best practice is to preserve
   * [CONDITION_ID] if you make only small changes, such as those to condition
   * thresholds, durations, or trigger values. Otherwise, treat the change as a
   * new condition and let the existing condition be deleted.
   *
   * @var string
   */
  public $name;

  /**
   * A condition that checks that a time series continues to receive new data
   * points.
   *
   * @param MetricAbsence $conditionAbsent
   */
  public function setConditionAbsent(MetricAbsence $conditionAbsent)
  {
    $this->conditionAbsent = $conditionAbsent;
  }
  /**
   * @return MetricAbsence
   */
  public function getConditionAbsent()
  {
    return $this->conditionAbsent;
  }
  /**
   * A condition that checks for log messages matching given constraints. If
   * set, no other conditions can be present.
   *
   * @param LogMatch $conditionMatchedLog
   */
  public function setConditionMatchedLog(LogMatch $conditionMatchedLog)
  {
    $this->conditionMatchedLog = $conditionMatchedLog;
  }
  /**
   * @return LogMatch
   */
  public function getConditionMatchedLog()
  {
    return $this->conditionMatchedLog;
  }
  /**
   * A condition that uses the Monitoring Query Language to define alerts.
   *
   * @param MonitoringQueryLanguageCondition $conditionMonitoringQueryLanguage
   */
  public function setConditionMonitoringQueryLanguage(MonitoringQueryLanguageCondition $conditionMonitoringQueryLanguage)
  {
    $this->conditionMonitoringQueryLanguage = $conditionMonitoringQueryLanguage;
  }
  /**
   * @return MonitoringQueryLanguageCondition
   */
  public function getConditionMonitoringQueryLanguage()
  {
    return $this->conditionMonitoringQueryLanguage;
  }
  /**
   * A condition that uses the Prometheus query language to define alerts.
   *
   * @param PrometheusQueryLanguageCondition $conditionPrometheusQueryLanguage
   */
  public function setConditionPrometheusQueryLanguage(PrometheusQueryLanguageCondition $conditionPrometheusQueryLanguage)
  {
    $this->conditionPrometheusQueryLanguage = $conditionPrometheusQueryLanguage;
  }
  /**
   * @return PrometheusQueryLanguageCondition
   */
  public function getConditionPrometheusQueryLanguage()
  {
    return $this->conditionPrometheusQueryLanguage;
  }
  /**
   * A condition that periodically evaluates a SQL query result.
   *
   * @param SqlCondition $conditionSql
   */
  public function setConditionSql(SqlCondition $conditionSql)
  {
    $this->conditionSql = $conditionSql;
  }
  /**
   * @return SqlCondition
   */
  public function getConditionSql()
  {
    return $this->conditionSql;
  }
  /**
   * A condition that compares a time series against a threshold.
   *
   * @param MetricThreshold $conditionThreshold
   */
  public function setConditionThreshold(MetricThreshold $conditionThreshold)
  {
    $this->conditionThreshold = $conditionThreshold;
  }
  /**
   * @return MetricThreshold
   */
  public function getConditionThreshold()
  {
    return $this->conditionThreshold;
  }
  /**
   * A short name or phrase used to identify the condition in dashboards,
   * notifications, and incidents. To avoid confusion, don't use the same
   * display name for multiple conditions in the same policy.
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
   * Required if the condition exists. The unique resource name for this
   * condition. Its format is: projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[PO
   * LICY_ID]/conditions/[CONDITION_ID] [CONDITION_ID] is assigned by Cloud
   * Monitoring when the condition is created as part of a new or updated
   * alerting policy.When calling the alertPolicies.create method, do not
   * include the name field in the conditions of the requested alerting policy.
   * Cloud Monitoring creates the condition identifiers and includes them in the
   * new policy.When calling the alertPolicies.update method to update a policy,
   * including a condition name causes the existing condition to be updated.
   * Conditions without names are added to the updated policy. Existing
   * conditions are deleted if they are not updated.Best practice is to preserve
   * [CONDITION_ID] if you make only small changes, such as those to condition
   * thresholds, durations, or trigger values. Otherwise, treat the change as a
   * new condition and let the existing condition be deleted.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Condition::class, 'Google_Service_Monitoring_Condition');
