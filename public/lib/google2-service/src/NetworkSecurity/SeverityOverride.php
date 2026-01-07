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

namespace Google\Service\NetworkSecurity;

class SeverityOverride extends \Google\Model
{
  /**
   * Threat action not specified.
   */
  public const ACTION_THREAT_ACTION_UNSPECIFIED = 'THREAT_ACTION_UNSPECIFIED';
  /**
   * The default action (as specified by the vendor) is taken.
   */
  public const ACTION_DEFAULT_ACTION = 'DEFAULT_ACTION';
  /**
   * The packet matching this rule will be allowed to transmit.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * The packet matching this rule will be allowed to transmit, but a threat_log
   * entry will be sent to the consumer project.
   */
  public const ACTION_ALERT = 'ALERT';
  /**
   * The packet matching this rule will be dropped, and a threat_log entry will
   * be sent to the consumer project.
   */
  public const ACTION_DENY = 'DENY';
  /**
   * Severity level not specified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Suspicious events that do not pose an immediate threat, but that are
   * reported to call attention to deeper problems that could possibly exist.
   */
  public const SEVERITY_INFORMATIONAL = 'INFORMATIONAL';
  /**
   * Warning-level threats that have very little impact on an organization's
   * infrastructure. They usually require local or physical system access and
   * may often result in victim privacy issues and information leakage.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Minor threats in which impact is minimized, that do not compromise the
   * target or exploits that require an attacker to reside on the same local
   * network as the victim, affect only non-standard configurations or obscure
   * applications, or provide very limited access.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Threats that have the ability to become critical but have mitigating
   * factors; for example, they may be difficult to exploit, do not result in
   * elevated privileges, or do not have a large victim pool.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Serious threats, such as those that affect default installations of widely
   * deployed software, result in root compromise of servers, and the exploit
   * code is widely available to attackers. The attacker usually does not need
   * any special authentication credentials or knowledge about the individual
   * victims and the target does not need to be manipulated into performing any
   * special functions.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * Required. Threat action override.
   *
   * @var string
   */
  public $action;
  /**
   * Required. Severity level to match.
   *
   * @var string
   */
  public $severity;

  /**
   * Required. Threat action override.
   *
   * Accepted values: THREAT_ACTION_UNSPECIFIED, DEFAULT_ACTION, ALLOW, ALERT,
   * DENY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Required. Severity level to match.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFORMATIONAL, LOW, MEDIUM, HIGH,
   * CRITICAL
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SeverityOverride::class, 'Google_Service_NetworkSecurity_SeverityOverride');
