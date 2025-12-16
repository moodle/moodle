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

class PolicySnapshot extends \Google\Model
{
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
  /**
   * The display name of the alert policy.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the alert policy resource. In the form of
   * "projects/PROJECT_ID_OR_NUMBER/alertPolicies/ALERT_POLICY_ID".
   *
   * @var string
   */
  public $name;
  /**
   * The severity of the alert policy.
   *
   * @var string
   */
  public $severity;
  /**
   * The user labels for the alert policy.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * The display name of the alert policy.
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
   * The name of the alert policy resource. In the form of
   * "projects/PROJECT_ID_OR_NUMBER/alertPolicies/ALERT_POLICY_ID".
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
   * The severity of the alert policy.
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
   * The user labels for the alert policy.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicySnapshot::class, 'Google_Service_Monitoring_PolicySnapshot');
