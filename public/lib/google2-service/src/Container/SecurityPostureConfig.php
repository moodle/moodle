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

namespace Google\Service\Container;

class SecurityPostureConfig extends \Google\Model
{
  /**
   * Default value not specified.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Disables Security Posture features on the cluster.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * Applies Security Posture features on the cluster.
   */
  public const MODE_BASIC = 'BASIC';
  /**
   * Applies the Security Posture off cluster Enterprise level features.
   */
  public const MODE_ENTERPRISE = 'ENTERPRISE';
  /**
   * Default value not specified.
   */
  public const VULNERABILITY_MODE_VULNERABILITY_MODE_UNSPECIFIED = 'VULNERABILITY_MODE_UNSPECIFIED';
  /**
   * Disables vulnerability scanning on the cluster.
   */
  public const VULNERABILITY_MODE_VULNERABILITY_DISABLED = 'VULNERABILITY_DISABLED';
  /**
   * Applies basic vulnerability scanning on the cluster.
   */
  public const VULNERABILITY_MODE_VULNERABILITY_BASIC = 'VULNERABILITY_BASIC';
  /**
   * Applies the Security Posture's vulnerability on cluster Enterprise level
   * features.
   */
  public const VULNERABILITY_MODE_VULNERABILITY_ENTERPRISE = 'VULNERABILITY_ENTERPRISE';
  /**
   * Sets which mode to use for Security Posture features.
   *
   * @var string
   */
  public $mode;
  /**
   * Sets which mode to use for vulnerability scanning.
   *
   * @var string
   */
  public $vulnerabilityMode;

  /**
   * Sets which mode to use for Security Posture features.
   *
   * Accepted values: MODE_UNSPECIFIED, DISABLED, BASIC, ENTERPRISE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Sets which mode to use for vulnerability scanning.
   *
   * Accepted values: VULNERABILITY_MODE_UNSPECIFIED, VULNERABILITY_DISABLED,
   * VULNERABILITY_BASIC, VULNERABILITY_ENTERPRISE
   *
   * @param self::VULNERABILITY_MODE_* $vulnerabilityMode
   */
  public function setVulnerabilityMode($vulnerabilityMode)
  {
    $this->vulnerabilityMode = $vulnerabilityMode;
  }
  /**
   * @return self::VULNERABILITY_MODE_*
   */
  public function getVulnerabilityMode()
  {
    return $this->vulnerabilityMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPostureConfig::class, 'Google_Service_Container_SecurityPostureConfig');
