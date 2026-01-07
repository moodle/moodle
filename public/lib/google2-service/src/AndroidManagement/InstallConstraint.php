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

namespace Google\Service\AndroidManagement;

class InstallConstraint extends \Google\Model
{
  /**
   * Unspecified. Default to CHARGING_NOT_REQUIRED.
   */
  public const CHARGING_CONSTRAINT_CHARGING_CONSTRAINT_UNSPECIFIED = 'CHARGING_CONSTRAINT_UNSPECIFIED';
  /**
   * Device doesn't have to be charging.
   */
  public const CHARGING_CONSTRAINT_CHARGING_NOT_REQUIRED = 'CHARGING_NOT_REQUIRED';
  /**
   * Device has to be charging.
   */
  public const CHARGING_CONSTRAINT_INSTALL_ONLY_WHEN_CHARGING = 'INSTALL_ONLY_WHEN_CHARGING';
  /**
   * Unspecified. Default to DEVICE_IDLE_NOT_REQUIRED.
   */
  public const DEVICE_IDLE_CONSTRAINT_DEVICE_IDLE_CONSTRAINT_UNSPECIFIED = 'DEVICE_IDLE_CONSTRAINT_UNSPECIFIED';
  /**
   * Device doesn't have to be idle, app can be installed while the user is
   * interacting with the device.
   */
  public const DEVICE_IDLE_CONSTRAINT_DEVICE_IDLE_NOT_REQUIRED = 'DEVICE_IDLE_NOT_REQUIRED';
  /**
   * Device has to be idle.
   */
  public const DEVICE_IDLE_CONSTRAINT_INSTALL_ONLY_WHEN_DEVICE_IDLE = 'INSTALL_ONLY_WHEN_DEVICE_IDLE';
  /**
   * Unspecified. Default to INSTALL_ON_ANY_NETWORK.
   */
  public const NETWORK_TYPE_CONSTRAINT_NETWORK_TYPE_CONSTRAINT_UNSPECIFIED = 'NETWORK_TYPE_CONSTRAINT_UNSPECIFIED';
  /**
   * Any active networks (Wi-Fi, cellular, etc.).
   */
  public const NETWORK_TYPE_CONSTRAINT_INSTALL_ON_ANY_NETWORK = 'INSTALL_ON_ANY_NETWORK';
  /**
   * Any unmetered network (e.g. Wi-FI).
   */
  public const NETWORK_TYPE_CONSTRAINT_INSTALL_ONLY_ON_UNMETERED_NETWORK = 'INSTALL_ONLY_ON_UNMETERED_NETWORK';
  /**
   * Optional. Charging constraint.
   *
   * @var string
   */
  public $chargingConstraint;
  /**
   * Optional. Device idle constraint.
   *
   * @var string
   */
  public $deviceIdleConstraint;
  /**
   * Optional. Network type constraint.
   *
   * @var string
   */
  public $networkTypeConstraint;

  /**
   * Optional. Charging constraint.
   *
   * Accepted values: CHARGING_CONSTRAINT_UNSPECIFIED, CHARGING_NOT_REQUIRED,
   * INSTALL_ONLY_WHEN_CHARGING
   *
   * @param self::CHARGING_CONSTRAINT_* $chargingConstraint
   */
  public function setChargingConstraint($chargingConstraint)
  {
    $this->chargingConstraint = $chargingConstraint;
  }
  /**
   * @return self::CHARGING_CONSTRAINT_*
   */
  public function getChargingConstraint()
  {
    return $this->chargingConstraint;
  }
  /**
   * Optional. Device idle constraint.
   *
   * Accepted values: DEVICE_IDLE_CONSTRAINT_UNSPECIFIED,
   * DEVICE_IDLE_NOT_REQUIRED, INSTALL_ONLY_WHEN_DEVICE_IDLE
   *
   * @param self::DEVICE_IDLE_CONSTRAINT_* $deviceIdleConstraint
   */
  public function setDeviceIdleConstraint($deviceIdleConstraint)
  {
    $this->deviceIdleConstraint = $deviceIdleConstraint;
  }
  /**
   * @return self::DEVICE_IDLE_CONSTRAINT_*
   */
  public function getDeviceIdleConstraint()
  {
    return $this->deviceIdleConstraint;
  }
  /**
   * Optional. Network type constraint.
   *
   * Accepted values: NETWORK_TYPE_CONSTRAINT_UNSPECIFIED,
   * INSTALL_ON_ANY_NETWORK, INSTALL_ONLY_ON_UNMETERED_NETWORK
   *
   * @param self::NETWORK_TYPE_CONSTRAINT_* $networkTypeConstraint
   */
  public function setNetworkTypeConstraint($networkTypeConstraint)
  {
    $this->networkTypeConstraint = $networkTypeConstraint;
  }
  /**
   * @return self::NETWORK_TYPE_CONSTRAINT_*
   */
  public function getNetworkTypeConstraint()
  {
    return $this->networkTypeConstraint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstallConstraint::class, 'Google_Service_AndroidManagement_InstallConstraint');
