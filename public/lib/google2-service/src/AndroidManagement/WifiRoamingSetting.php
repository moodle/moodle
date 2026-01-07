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

class WifiRoamingSetting extends \Google\Model
{
  /**
   * Unspecified. Defaults to WIFI_ROAMING_DEFAULT.
   */
  public const WIFI_ROAMING_MODE_WIFI_ROAMING_MODE_UNSPECIFIED = 'WIFI_ROAMING_MODE_UNSPECIFIED';
  /**
   * Wi-Fi roaming is disabled. Supported on Android 15 and above on fully
   * managed devices and work profiles on company-owned devices. A
   * NonComplianceDetail with MANAGEMENT_MODE is reported for other management
   * modes. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 15.
   */
  public const WIFI_ROAMING_MODE_WIFI_ROAMING_DISABLED = 'WIFI_ROAMING_DISABLED';
  /**
   * Default Wi-Fi roaming mode of the device.
   */
  public const WIFI_ROAMING_MODE_WIFI_ROAMING_DEFAULT = 'WIFI_ROAMING_DEFAULT';
  /**
   * Aggressive roaming mode which allows quicker Wi-Fi roaming. Supported on
   * Android 15 and above on fully managed devices and work profiles on company-
   * owned devices. A NonComplianceDetail with MANAGEMENT_MODE is reported for
   * other management modes. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 15. A NonComplianceDetail with
   * DEVICE_INCOMPATIBLE is reported if the device does not support aggressive
   * roaming mode.
   */
  public const WIFI_ROAMING_MODE_WIFI_ROAMING_AGGRESSIVE = 'WIFI_ROAMING_AGGRESSIVE';
  /**
   * Required. Wi-Fi roaming mode for the specified SSID.
   *
   * @var string
   */
  public $wifiRoamingMode;
  /**
   * Required. SSID of the Wi-Fi network.
   *
   * @var string
   */
  public $wifiSsid;

  /**
   * Required. Wi-Fi roaming mode for the specified SSID.
   *
   * Accepted values: WIFI_ROAMING_MODE_UNSPECIFIED, WIFI_ROAMING_DISABLED,
   * WIFI_ROAMING_DEFAULT, WIFI_ROAMING_AGGRESSIVE
   *
   * @param self::WIFI_ROAMING_MODE_* $wifiRoamingMode
   */
  public function setWifiRoamingMode($wifiRoamingMode)
  {
    $this->wifiRoamingMode = $wifiRoamingMode;
  }
  /**
   * @return self::WIFI_ROAMING_MODE_*
   */
  public function getWifiRoamingMode()
  {
    return $this->wifiRoamingMode;
  }
  /**
   * Required. SSID of the Wi-Fi network.
   *
   * @param string $wifiSsid
   */
  public function setWifiSsid($wifiSsid)
  {
    $this->wifiSsid = $wifiSsid;
  }
  /**
   * @return string
   */
  public function getWifiSsid()
  {
    return $this->wifiSsid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WifiRoamingSetting::class, 'Google_Service_AndroidManagement_WifiRoamingSetting');
