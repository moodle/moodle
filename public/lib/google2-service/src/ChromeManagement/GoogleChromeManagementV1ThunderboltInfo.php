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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1ThunderboltInfo extends \Google\Model
{
  /**
   * Thunderbolt security level is not set.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_LEVEL_UNSPECIFIED = 'THUNDERBOLT_SECURITY_LEVEL_UNSPECIFIED';
  /**
   * All devices are automatically connected by the firmware. No user approval
   * is needed.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_NONE_LEVEL = 'THUNDERBOLT_SECURITY_NONE_LEVEL';
  /**
   * User is asked whether the device is allowed to be connected.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_USER_LEVEL = 'THUNDERBOLT_SECURITY_USER_LEVEL';
  /**
   * User is asked whether the device is allowed to be connected. In addition
   * the device is sent a challenge that should match the expected one based on
   * a random key written to the key sysfs attribute
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_SECURE_LEVEL = 'THUNDERBOLT_SECURITY_SECURE_LEVEL';
  /**
   * The firmware automatically creates tunnels for Thunderbolt.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_DP_ONLY_LEVEL = 'THUNDERBOLT_SECURITY_DP_ONLY_LEVEL';
  /**
   * The firmware automatically creates tunnels for the USB controller and
   * Display Port in a dock. All PCIe links downstream of the dock are removed.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_USB_ONLY_LEVEL = 'THUNDERBOLT_SECURITY_USB_ONLY_LEVEL';
  /**
   * PCIE tunneling is disabled.
   */
  public const SECURITY_LEVEL_THUNDERBOLT_SECURITY_NO_PCIE_LEVEL = 'THUNDERBOLT_SECURITY_NO_PCIE_LEVEL';
  /**
   * Security level of the Thunderbolt bus.
   *
   * @var string
   */
  public $securityLevel;

  /**
   * Security level of the Thunderbolt bus.
   *
   * Accepted values: THUNDERBOLT_SECURITY_LEVEL_UNSPECIFIED,
   * THUNDERBOLT_SECURITY_NONE_LEVEL, THUNDERBOLT_SECURITY_USER_LEVEL,
   * THUNDERBOLT_SECURITY_SECURE_LEVEL, THUNDERBOLT_SECURITY_DP_ONLY_LEVEL,
   * THUNDERBOLT_SECURITY_USB_ONLY_LEVEL, THUNDERBOLT_SECURITY_NO_PCIE_LEVEL
   *
   * @param self::SECURITY_LEVEL_* $securityLevel
   */
  public function setSecurityLevel($securityLevel)
  {
    $this->securityLevel = $securityLevel;
  }
  /**
   * @return self::SECURITY_LEVEL_*
   */
  public function getSecurityLevel()
  {
    return $this->securityLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1ThunderboltInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1ThunderboltInfo');
