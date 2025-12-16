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

class WifiSsidPolicy extends \Google\Collection
{
  /**
   * Defaults to WIFI_SSID_DENYLIST. wifiSsids must not be set. There are no
   * restrictions on which SSID the device can connect to.
   */
  public const WIFI_SSID_POLICY_TYPE_WIFI_SSID_POLICY_TYPE_UNSPECIFIED = 'WIFI_SSID_POLICY_TYPE_UNSPECIFIED';
  /**
   * The device cannot connect to any Wi-Fi network whose SSID is in wifiSsids,
   * but can connect to other networks.
   */
  public const WIFI_SSID_POLICY_TYPE_WIFI_SSID_DENYLIST = 'WIFI_SSID_DENYLIST';
  /**
   * The device can make Wi-Fi connections only to the SSIDs in wifiSsids.
   * wifiSsids must not be empty. The device will not be able to connect to any
   * other Wi-Fi network.
   */
  public const WIFI_SSID_POLICY_TYPE_WIFI_SSID_ALLOWLIST = 'WIFI_SSID_ALLOWLIST';
  protected $collection_key = 'wifiSsids';
  /**
   * Type of the Wi-Fi SSID policy to be applied.
   *
   * @var string
   */
  public $wifiSsidPolicyType;
  protected $wifiSsidsType = WifiSsid::class;
  protected $wifiSsidsDataType = 'array';

  /**
   * Type of the Wi-Fi SSID policy to be applied.
   *
   * Accepted values: WIFI_SSID_POLICY_TYPE_UNSPECIFIED, WIFI_SSID_DENYLIST,
   * WIFI_SSID_ALLOWLIST
   *
   * @param self::WIFI_SSID_POLICY_TYPE_* $wifiSsidPolicyType
   */
  public function setWifiSsidPolicyType($wifiSsidPolicyType)
  {
    $this->wifiSsidPolicyType = $wifiSsidPolicyType;
  }
  /**
   * @return self::WIFI_SSID_POLICY_TYPE_*
   */
  public function getWifiSsidPolicyType()
  {
    return $this->wifiSsidPolicyType;
  }
  /**
   * Optional. List of Wi-Fi SSIDs that should be applied in the policy. This
   * field must be non-empty when WifiSsidPolicyType is set to
   * WIFI_SSID_ALLOWLIST. If this is set to a non-empty list, then a
   * NonComplianceDetail detail with API_LEVEL is reported if the Android
   * version is less than 13 and a NonComplianceDetail with MANAGEMENT_MODE is
   * reported for non-company-owned devices.
   *
   * @param WifiSsid[] $wifiSsids
   */
  public function setWifiSsids($wifiSsids)
  {
    $this->wifiSsids = $wifiSsids;
  }
  /**
   * @return WifiSsid[]
   */
  public function getWifiSsids()
  {
    return $this->wifiSsids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WifiSsidPolicy::class, 'Google_Service_AndroidManagement_WifiSsidPolicy');
