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

class AlertPolicy extends \Google\Collection
{
  /**
   * An unspecified combiner.
   */
  public const COMBINER_COMBINE_UNSPECIFIED = 'COMBINE_UNSPECIFIED';
  /**
   * Combine conditions using the logical AND operator. An incident is created
   * only if all the conditions are met simultaneously. This combiner is
   * satisfied if all conditions are met, even if they are met on completely
   * different resources.
   */
  public const COMBINER_AND = 'AND';
  /**
   * Combine conditions using the logical OR operator. An incident is created if
   * any of the listed conditions is met.
   */
  public const COMBINER_OR = 'OR';
  /**
   * Combine conditions using logical AND operator, but unlike the regular AND
   * option, an incident is created only if all conditions are met
   * simultaneously on at least one resource.
   */
  public const COMBINER_AND_WITH_MATCHING_RESOURCE = 'AND_WITH_MATCHING_RESOURCE';
  /**
   * No severity is specified. This is the default value.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * This is the highest severity level. Use this if the problem could cause
   * significant damage or downtime.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * This is the medium severity level. Use this if the problem could cause
   * minor damage or downtime.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * This is the lowest severity level. Use this if the problem is not causing
   * any damage or downtime, but could potentially lead to a problem in the
   * future.
   */
  public const SEVERITY_WARNING = 'WARNING';
  protected $collection_key = 'notificationChannels';
  protected $alertStrategyType = AlertStrategy::class;
  protected $alertStrategyDataType = '';
  /**
   * How to combine the results of multiple conditions to determine if an
   * incident should be opened. If condition_time_series_query_language is
   * present, this must be COMBINE_UNSPECIFIED.
   *
   * @var string
   */
  public $combiner;
  protected $conditionsType = Condition::class;
  protected $conditionsDataType = 'array';
  protected $creationRecordType = MutationRecord::class;
  protected $creationRecordDataType = '';
  /**
   * A short name or phrase used to identify the policy in dashboards,
   * notifications, and incidents. To avoid confusion, don't use the same
   * display name for multiple policies in the same project. The name is limited
   * to 512 Unicode characters.The convention for the display_name of a
   * PrometheusQueryLanguageCondition is "{rule group name}/{alert name}", where
   * the {rule group name} and {alert name} should be taken from the
   * corresponding Prometheus configuration file. This convention is not
   * enforced. In any case the display_name is not a unique key of the
   * AlertPolicy.
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = Documentation::class;
  protected $documentationDataType = '';
  /**
   * Whether or not the policy is enabled. On write, the default interpretation
   * if unset is that the policy is enabled. On read, clients should not make
   * any assumption about the state if it has not been populated. The field
   * should always be populated on List and Get operations, unless a field
   * projection has been specified that strips it out.
   *
   * @var bool
   */
  public $enabled;
  protected $mutationRecordType = MutationRecord::class;
  protected $mutationRecordDataType = '';
  /**
   * Identifier. Required if the policy exists. The resource name for this
   * policy. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[ALERT_POLICY_ID]
   * [ALERT_POLICY_ID] is assigned by Cloud Monitoring when the policy is
   * created. When calling the alertPolicies.create method, do not include the
   * name field in the alerting policy passed as part of the request.
   *
   * @var string
   */
  public $name;
  /**
   * Identifies the notification channels to which notifications should be sent
   * when incidents are opened or closed or when new violations occur on an
   * already opened incident. Each element of this array corresponds to the name
   * field in each of the NotificationChannel objects that are returned from the
   * ListNotificationChannels method. The format of the entries in this field
   * is: projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID]
   *
   * @var string[]
   */
  public $notificationChannels;
  /**
   * Optional. The severity of an alerting policy indicates how important
   * incidents generated by that policy are. The severity level will be
   * displayed on the Incident detail page and in notifications.
   *
   * @var string
   */
  public $severity;
  /**
   * User-supplied key/value data to be used for organizing and identifying the
   * AlertPolicy objects.The field can contain up to 64 entries. Each key and
   * value is limited to 63 Unicode characters or 128 bytes, whichever is
   * smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.Note that Prometheus
   * {alert name} is a valid Prometheus label names
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels),
   * whereas Prometheus {rule group} is an unrestricted UTF-8 string. This means
   * that they cannot be stored as-is in user labels, because they may contain
   * characters that are not allowed in user-label values.
   *
   * @var string[]
   */
  public $userLabels;
  protected $validityType = Status::class;
  protected $validityDataType = '';

  /**
   * Control over how this alerting policy's notification channels are notified.
   *
   * @param AlertStrategy $alertStrategy
   */
  public function setAlertStrategy(AlertStrategy $alertStrategy)
  {
    $this->alertStrategy = $alertStrategy;
  }
  /**
   * @return AlertStrategy
   */
  public function getAlertStrategy()
  {
    return $this->alertStrategy;
  }
  /**
   * How to combine the results of multiple conditions to determine if an
   * incident should be opened. If condition_time_series_query_language is
   * present, this must be COMBINE_UNSPECIFIED.
   *
   * Accepted values: COMBINE_UNSPECIFIED, AND, OR, AND_WITH_MATCHING_RESOURCE
   *
   * @param self::COMBINER_* $combiner
   */
  public function setCombiner($combiner)
  {
    $this->combiner = $combiner;
  }
  /**
   * @return self::COMBINER_*
   */
  public function getCombiner()
  {
    return $this->combiner;
  }
  /**
   * A list of conditions for the policy. The conditions are combined by AND or
   * OR according to the combiner field. If the combined conditions evaluate to
   * true, then an incident is created. A policy can have from one to six
   * conditions. If condition_time_series_query_language is present, it must be
   * the only condition. If condition_monitoring_query_language is present, it
   * must be the only condition.
   *
   * @param Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * A read-only record of the creation of the alerting policy. If provided in a
   * call to create or update, this field will be ignored.
   *
   * @param MutationRecord $creationRecord
   */
  public function setCreationRecord(MutationRecord $creationRecord)
  {
    $this->creationRecord = $creationRecord;
  }
  /**
   * @return MutationRecord
   */
  public function getCreationRecord()
  {
    return $this->creationRecord;
  }
  /**
   * A short name or phrase used to identify the policy in dashboards,
   * notifications, and incidents. To avoid confusion, don't use the same
   * display name for multiple policies in the same project. The name is limited
   * to 512 Unicode characters.The convention for the display_name of a
   * PrometheusQueryLanguageCondition is "{rule group name}/{alert name}", where
   * the {rule group name} and {alert name} should be taken from the
   * corresponding Prometheus configuration file. This convention is not
   * enforced. In any case the display_name is not a unique key of the
   * AlertPolicy.
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
   * Documentation that is included with notifications and incidents related to
   * this policy. Best practice is for the documentation to include information
   * to help responders understand, mitigate, escalate, and correct the
   * underlying problems detected by the alerting policy. Notification channels
   * that have limited capacity might not show this documentation.
   *
   * @param Documentation $documentation
   */
  public function setDocumentation(Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Whether or not the policy is enabled. On write, the default interpretation
   * if unset is that the policy is enabled. On read, clients should not make
   * any assumption about the state if it has not been populated. The field
   * should always be populated on List and Get operations, unless a field
   * projection has been specified that strips it out.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * A read-only record of the most recent change to the alerting policy. If
   * provided in a call to create or update, this field will be ignored.
   *
   * @param MutationRecord $mutationRecord
   */
  public function setMutationRecord(MutationRecord $mutationRecord)
  {
    $this->mutationRecord = $mutationRecord;
  }
  /**
   * @return MutationRecord
   */
  public function getMutationRecord()
  {
    return $this->mutationRecord;
  }
  /**
   * Identifier. Required if the policy exists. The resource name for this
   * policy. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/alertPolicies/[ALERT_POLICY_ID]
   * [ALERT_POLICY_ID] is assigned by Cloud Monitoring when the policy is
   * created. When calling the alertPolicies.create method, do not include the
   * name field in the alerting policy passed as part of the request.
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
  /**
   * Identifies the notification channels to which notifications should be sent
   * when incidents are opened or closed or when new violations occur on an
   * already opened incident. Each element of this array corresponds to the name
   * field in each of the NotificationChannel objects that are returned from the
   * ListNotificationChannels method. The format of the entries in this field
   * is: projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID]
   *
   * @param string[] $notificationChannels
   */
  public function setNotificationChannels($notificationChannels)
  {
    $this->notificationChannels = $notificationChannels;
  }
  /**
   * @return string[]
   */
  public function getNotificationChannels()
  {
    return $this->notificationChannels;
  }
  /**
   * Optional. The severity of an alerting policy indicates how important
   * incidents generated by that policy are. The severity level will be
   * displayed on the Incident detail page and in notifications.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, CRITICAL, ERROR, WARNING
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * User-supplied key/value data to be used for organizing and identifying the
   * AlertPolicy objects.The field can contain up to 64 entries. Each key and
   * value is limited to 63 Unicode characters or 128 bytes, whichever is
   * smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.Note that Prometheus
   * {alert name} is a valid Prometheus label names
   * (https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels),
   * whereas Prometheus {rule group} is an unrestricted UTF-8 string. This means
   * that they cannot be stored as-is in user labels, because they may contain
   * characters that are not allowed in user-label values.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
  /**
   * Read-only description of how the alerting policy is invalid. This field is
   * only set when the alerting policy is invalid. An invalid alerting policy
   * will not generate incidents.
   *
   * @param Status $validity
   */
  public function setValidity(Status $validity)
  {
    $this->validity = $validity;
  }
  /**
   * @return Status
   */
  public function getValidity()
  {
    return $this->validity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlertPolicy::class, 'Google_Service_Monitoring_AlertPolicy');
