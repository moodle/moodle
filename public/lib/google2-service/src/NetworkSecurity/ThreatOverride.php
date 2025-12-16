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

class ThreatOverride extends \Google\Model
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
   * Type of threat not specified.
   */
  public const TYPE_THREAT_TYPE_UNSPECIFIED = 'THREAT_TYPE_UNSPECIFIED';
  /**
   * Type of threat is not derivable from threat ID. An override will be created
   * for all types. Firewall will ignore overridden signature ID's that don't
   * exist in the specific type.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Threats related to system flaws that an attacker might otherwise attempt to
   * exploit.
   */
  public const TYPE_VULNERABILITY = 'VULNERABILITY';
  /**
   * Threats related to viruses and malware found in executables and file types.
   */
  public const TYPE_ANTIVIRUS = 'ANTIVIRUS';
  /**
   * Threats related to command-and-control (C2) activity, where spyware on an
   * infected client is collecting data without the user's consent and/or
   * communicating with a remote attacker.
   */
  public const TYPE_SPYWARE = 'SPYWARE';
  /**
   * Threats related to DNS.
   */
  public const TYPE_DNS = 'DNS';
  /**
   * Required. Threat action override. For some threat types, only a subset of
   * actions applies.
   *
   * @var string
   */
  public $action;
  /**
   * Required. Vendor-specific ID of a threat to override.
   *
   * @var string
   */
  public $threatId;
  /**
   * Output only. Type of the threat (read only).
   *
   * @var string
   */
  public $type;

  /**
   * Required. Threat action override. For some threat types, only a subset of
   * actions applies.
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
   * Required. Vendor-specific ID of a threat to override.
   *
   * @param string $threatId
   */
  public function setThreatId($threatId)
  {
    $this->threatId = $threatId;
  }
  /**
   * @return string
   */
  public function getThreatId()
  {
    return $this->threatId;
  }
  /**
   * Output only. Type of the threat (read only).
   *
   * Accepted values: THREAT_TYPE_UNSPECIFIED, UNKNOWN, VULNERABILITY,
   * ANTIVIRUS, SPYWARE, DNS
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
class_alias(ThreatOverride::class, 'Google_Service_NetworkSecurity_ThreatOverride');
