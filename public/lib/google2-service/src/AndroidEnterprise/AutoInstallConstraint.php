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

namespace Google\Service\AndroidEnterprise;

class AutoInstallConstraint extends \Google\Model
{
  public const CHARGING_STATE_CONSTRAINT_chargingStateConstraintUnspecified = 'chargingStateConstraintUnspecified';
  /**
   * Device doesn't have to be charging.
   */
  public const CHARGING_STATE_CONSTRAINT_chargingNotRequired = 'chargingNotRequired';
  /**
   * Device has to be charging.
   */
  public const CHARGING_STATE_CONSTRAINT_chargingRequired = 'chargingRequired';
  public const DEVICE_IDLE_STATE_CONSTRAINT_deviceIdleStateConstraintUnspecified = 'deviceIdleStateConstraintUnspecified';
  /**
   * Device doesn't have to be idle, app can be installed while the user is
   * interacting with the device.
   */
  public const DEVICE_IDLE_STATE_CONSTRAINT_deviceIdleNotRequired = 'deviceIdleNotRequired';
  /**
   * Device has to be idle.
   */
  public const DEVICE_IDLE_STATE_CONSTRAINT_deviceIdleRequired = 'deviceIdleRequired';
  public const NETWORK_TYPE_CONSTRAINT_networkTypeConstraintUnspecified = 'networkTypeConstraintUnspecified';
  /**
   * Any active networks (Wi-Fi, cellular, etc.).
   */
  public const NETWORK_TYPE_CONSTRAINT_anyNetwork = 'anyNetwork';
  /**
   * Any unmetered network (e.g. Wi-FI).
   */
  public const NETWORK_TYPE_CONSTRAINT_unmeteredNetwork = 'unmeteredNetwork';
  /**
   * Charging state constraint.
   *
   * @var string
   */
  public $chargingStateConstraint;
  /**
   * Device idle state constraint.
   *
   * @var string
   */
  public $deviceIdleStateConstraint;
  /**
   * Network type constraint.
   *
   * @var string
   */
  public $networkTypeConstraint;

  /**
   * Charging state constraint.
   *
   * Accepted values: chargingStateConstraintUnspecified, chargingNotRequired,
   * chargingRequired
   *
   * @param self::CHARGING_STATE_CONSTRAINT_* $chargingStateConstraint
   */
  public function setChargingStateConstraint($chargingStateConstraint)
  {
    $this->chargingStateConstraint = $chargingStateConstraint;
  }
  /**
   * @return self::CHARGING_STATE_CONSTRAINT_*
   */
  public function getChargingStateConstraint()
  {
    return $this->chargingStateConstraint;
  }
  /**
   * Device idle state constraint.
   *
   * Accepted values: deviceIdleStateConstraintUnspecified,
   * deviceIdleNotRequired, deviceIdleRequired
   *
   * @param self::DEVICE_IDLE_STATE_CONSTRAINT_* $deviceIdleStateConstraint
   */
  public function setDeviceIdleStateConstraint($deviceIdleStateConstraint)
  {
    $this->deviceIdleStateConstraint = $deviceIdleStateConstraint;
  }
  /**
   * @return self::DEVICE_IDLE_STATE_CONSTRAINT_*
   */
  public function getDeviceIdleStateConstraint()
  {
    return $this->deviceIdleStateConstraint;
  }
  /**
   * Network type constraint.
   *
   * Accepted values: networkTypeConstraintUnspecified, anyNetwork,
   * unmeteredNetwork
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
class_alias(AutoInstallConstraint::class, 'Google_Service_AndroidEnterprise_AutoInstallConstraint');
