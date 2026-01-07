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

class DeviceRadioState extends \Google\Model
{
  /**
   * Unspecified. Defaults to AIRPLANE_MODE_USER_CHOICE.
   */
  public const AIRPLANE_MODE_STATE_AIRPLANE_MODE_STATE_UNSPECIFIED = 'AIRPLANE_MODE_STATE_UNSPECIFIED';
  /**
   * The user is allowed to toggle airplane mode on or off.
   */
  public const AIRPLANE_MODE_STATE_AIRPLANE_MODE_USER_CHOICE = 'AIRPLANE_MODE_USER_CHOICE';
  /**
   * Airplane mode is disabled. The user is not allowed to toggle airplane mode
   * on. A NonComplianceDetail with API_LEVEL is reported if the Android version
   * is less than 9.
   */
  public const AIRPLANE_MODE_STATE_AIRPLANE_MODE_DISABLED = 'AIRPLANE_MODE_DISABLED';
  /**
   * Unspecified. Defaults to CELLULAR_TWO_G_USER_CHOICE.
   */
  public const CELLULAR_TWO_GS_TATE_CELLULAR_TWO_G_STATE_UNSPECIFIED = 'CELLULAR_TWO_G_STATE_UNSPECIFIED';
  /**
   * The user is allowed to toggle cellular 2G on or off.
   */
  public const CELLULAR_TWO_GS_TATE_CELLULAR_TWO_G_USER_CHOICE = 'CELLULAR_TWO_G_USER_CHOICE';
  /**
   * Cellular 2G is disabled. The user is not allowed to toggle cellular 2G on
   * via settings. A NonComplianceDetail with API_LEVEL is reported if the
   * Android version is less than 14.
   */
  public const CELLULAR_TWO_GS_TATE_CELLULAR_TWO_G_DISABLED = 'CELLULAR_TWO_G_DISABLED';
  /**
   * Defaults to OPEN_NETWORK_SECURITY, which means the device will be able to
   * connect to all types of Wi-Fi networks.
   */
  public const MINIMUM_WIFI_SECURITY_LEVEL_MINIMUM_WIFI_SECURITY_LEVEL_UNSPECIFIED = 'MINIMUM_WIFI_SECURITY_LEVEL_UNSPECIFIED';
  /**
   * The device will be able to connect to all types of Wi-Fi networks.
   */
  public const MINIMUM_WIFI_SECURITY_LEVEL_OPEN_NETWORK_SECURITY = 'OPEN_NETWORK_SECURITY';
  /**
   * A personal network such as WEP, WPA2-PSK is the minimum required security.
   * The device will not be able to connect to open wifi networks. This is
   * stricter than OPEN_NETWORK_SECURITY. A NonComplianceDetail with API_LEVEL
   * is reported if the Android version is less than 13.
   */
  public const MINIMUM_WIFI_SECURITY_LEVEL_PERSONAL_NETWORK_SECURITY = 'PERSONAL_NETWORK_SECURITY';
  /**
   * An enterprise EAP network is the minimum required security level. The
   * device will not be able to connect to Wi-Fi network below this security
   * level. This is stricter than PERSONAL_NETWORK_SECURITY. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   */
  public const MINIMUM_WIFI_SECURITY_LEVEL_ENTERPRISE_NETWORK_SECURITY = 'ENTERPRISE_NETWORK_SECURITY';
  /**
   * A 192-bit enterprise network is the minimum required security level. The
   * device will not be able to connect to Wi-Fi network below this security
   * level. This is stricter than ENTERPRISE_NETWORK_SECURITY. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   */
  public const MINIMUM_WIFI_SECURITY_LEVEL_ENTERPRISE_BIT192_NETWORK_SECURITY = 'ENTERPRISE_BIT192_NETWORK_SECURITY';
  /**
   * Unspecified. Defaults to ULTRA_WIDEBAND_USER_CHOICE.
   */
  public const ULTRA_WIDEBAND_STATE_ULTRA_WIDEBAND_STATE_UNSPECIFIED = 'ULTRA_WIDEBAND_STATE_UNSPECIFIED';
  /**
   * The user is allowed to toggle ultra wideband on or off.
   */
  public const ULTRA_WIDEBAND_STATE_ULTRA_WIDEBAND_USER_CHOICE = 'ULTRA_WIDEBAND_USER_CHOICE';
  /**
   * Ultra wideband is disabled. The user is not allowed to toggle ultra
   * wideband on via settings. A NonComplianceDetail with API_LEVEL is reported
   * if the Android version is less than 14.
   */
  public const ULTRA_WIDEBAND_STATE_ULTRA_WIDEBAND_DISABLED = 'ULTRA_WIDEBAND_DISABLED';
  /**
   * Unspecified. Defaults to WIFI_STATE_USER_CHOICE
   */
  public const WIFI_STATE_WIFI_STATE_UNSPECIFIED = 'WIFI_STATE_UNSPECIFIED';
  /**
   * User is allowed to enable/disable Wi-Fi.
   */
  public const WIFI_STATE_WIFI_STATE_USER_CHOICE = 'WIFI_STATE_USER_CHOICE';
  /**
   * Wi-Fi is on and the user is not allowed to turn it off. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   */
  public const WIFI_STATE_WIFI_ENABLED = 'WIFI_ENABLED';
  /**
   * Wi-Fi is off and the user is not allowed to turn it on. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   */
  public const WIFI_STATE_WIFI_DISABLED = 'WIFI_DISABLED';
  /**
   * Controls whether airplane mode can be toggled by the user or not.
   *
   * @var string
   */
  public $airplaneModeState;
  /**
   * Controls whether cellular 2G setting can be toggled by the user or not.
   *
   * @var string
   */
  public $cellularTwoGState;
  /**
   * The minimum required security level of Wi-Fi networks that the device can
   * connect to.
   *
   * @var string
   */
  public $minimumWifiSecurityLevel;
  /**
   * Controls the state of the ultra wideband setting and whether the user can
   * toggle it on or off.
   *
   * @var string
   */
  public $ultraWidebandState;
  /**
   * Controls current state of Wi-Fi and if user can change its state.
   *
   * @var string
   */
  public $wifiState;

  /**
   * Controls whether airplane mode can be toggled by the user or not.
   *
   * Accepted values: AIRPLANE_MODE_STATE_UNSPECIFIED,
   * AIRPLANE_MODE_USER_CHOICE, AIRPLANE_MODE_DISABLED
   *
   * @param self::AIRPLANE_MODE_STATE_* $airplaneModeState
   */
  public function setAirplaneModeState($airplaneModeState)
  {
    $this->airplaneModeState = $airplaneModeState;
  }
  /**
   * @return self::AIRPLANE_MODE_STATE_*
   */
  public function getAirplaneModeState()
  {
    return $this->airplaneModeState;
  }
  /**
   * Controls whether cellular 2G setting can be toggled by the user or not.
   *
   * Accepted values: CELLULAR_TWO_G_STATE_UNSPECIFIED,
   * CELLULAR_TWO_G_USER_CHOICE, CELLULAR_TWO_G_DISABLED
   *
   * @param self::CELLULAR_TWO_GS_TATE_* $cellularTwoGState
   */
  public function setCellularTwoGState($cellularTwoGState)
  {
    $this->cellularTwoGState = $cellularTwoGState;
  }
  /**
   * @return self::CELLULAR_TWO_GS_TATE_*
   */
  public function getCellularTwoGState()
  {
    return $this->cellularTwoGState;
  }
  /**
   * The minimum required security level of Wi-Fi networks that the device can
   * connect to.
   *
   * Accepted values: MINIMUM_WIFI_SECURITY_LEVEL_UNSPECIFIED,
   * OPEN_NETWORK_SECURITY, PERSONAL_NETWORK_SECURITY,
   * ENTERPRISE_NETWORK_SECURITY, ENTERPRISE_BIT192_NETWORK_SECURITY
   *
   * @param self::MINIMUM_WIFI_SECURITY_LEVEL_* $minimumWifiSecurityLevel
   */
  public function setMinimumWifiSecurityLevel($minimumWifiSecurityLevel)
  {
    $this->minimumWifiSecurityLevel = $minimumWifiSecurityLevel;
  }
  /**
   * @return self::MINIMUM_WIFI_SECURITY_LEVEL_*
   */
  public function getMinimumWifiSecurityLevel()
  {
    return $this->minimumWifiSecurityLevel;
  }
  /**
   * Controls the state of the ultra wideband setting and whether the user can
   * toggle it on or off.
   *
   * Accepted values: ULTRA_WIDEBAND_STATE_UNSPECIFIED,
   * ULTRA_WIDEBAND_USER_CHOICE, ULTRA_WIDEBAND_DISABLED
   *
   * @param self::ULTRA_WIDEBAND_STATE_* $ultraWidebandState
   */
  public function setUltraWidebandState($ultraWidebandState)
  {
    $this->ultraWidebandState = $ultraWidebandState;
  }
  /**
   * @return self::ULTRA_WIDEBAND_STATE_*
   */
  public function getUltraWidebandState()
  {
    return $this->ultraWidebandState;
  }
  /**
   * Controls current state of Wi-Fi and if user can change its state.
   *
   * Accepted values: WIFI_STATE_UNSPECIFIED, WIFI_STATE_USER_CHOICE,
   * WIFI_ENABLED, WIFI_DISABLED
   *
   * @param self::WIFI_STATE_* $wifiState
   */
  public function setWifiState($wifiState)
  {
    $this->wifiState = $wifiState;
  }
  /**
   * @return self::WIFI_STATE_*
   */
  public function getWifiState()
  {
    return $this->wifiState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceRadioState::class, 'Google_Service_AndroidManagement_DeviceRadioState');
