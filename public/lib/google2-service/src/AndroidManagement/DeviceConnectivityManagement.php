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

class DeviceConnectivityManagement extends \Google\Model
{
  /**
   * Unspecified. Defaults to BLUETOOTH_SHARING_DISALLOWED on work profiles and
   * BLUETOOTH_SHARING_ALLOWED on fully managed devices.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_UNSPECIFIED = 'BLUETOOTH_SHARING_UNSPECIFIED';
  /**
   * Bluetooth sharing is allowed.Supported on Android 8 and above. A
   * NonComplianceDetail with API_LEVEL is reported on work profiles if the
   * Android version is less than 8.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_ALLOWED = 'BLUETOOTH_SHARING_ALLOWED';
  /**
   * Bluetooth sharing is disallowed.Supported on Android 8 and above. A
   * NonComplianceDetail with API_LEVEL is reported on fully managed devices if
   * the Android version is less than 8.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_DISALLOWED = 'BLUETOOTH_SHARING_DISALLOWED';
  /**
   * Unspecified. Defaults to ALLOW_CONFIGURING_WIFI unless wifiConfigDisabled
   * is set to true. If wifiConfigDisabled is set to true, this is equivalent to
   * DISALLOW_CONFIGURING_WIFI.
   */
  public const CONFIGURE_WIFI_CONFIGURE_WIFI_UNSPECIFIED = 'CONFIGURE_WIFI_UNSPECIFIED';
  /**
   * The user is allowed to configure Wi-Fi. wifiConfigDisabled is ignored.
   */
  public const CONFIGURE_WIFI_ALLOW_CONFIGURING_WIFI = 'ALLOW_CONFIGURING_WIFI';
  /**
   * Adding new Wi-Fi configurations is disallowed. The user is only able to
   * switch between already configured networks. Supported on Android 13 and
   * above, on fully managed devices and work profiles on company-owned devices.
   * If the setting is not supported, ALLOW_CONFIGURING_WIFI is set. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13. wifiConfigDisabled is ignored.
   */
  public const CONFIGURE_WIFI_DISALLOW_ADD_WIFI_CONFIG = 'DISALLOW_ADD_WIFI_CONFIG';
  /**
   * Disallows configuring Wi-Fi networks. The setting wifiConfigDisabled is
   * ignored when this value is set. Supported on fully managed devices and work
   * profile on company-owned devices, on all supported API levels. For fully
   * managed devices, setting this removes all configured networks and retains
   * only the networks configured using openNetworkConfiguration policy. For
   * work profiles on company-owned devices, existing configured networks are
   * not affected and the user is not allowed to add, remove, or modify Wi-Fi
   * networks. Note: If a network connection can't be made at boot time and
   * configuring Wi-Fi is disabled then network escape hatch will be shown in
   * order to refresh the device policy (see networkEscapeHatchEnabled).
   */
  public const CONFIGURE_WIFI_DISALLOW_CONFIGURING_WIFI = 'DISALLOW_CONFIGURING_WIFI';
  /**
   * Unspecified. Defaults to ALLOW_ALL_TETHERING unless tetheringConfigDisabled
   * is set to true. If tetheringConfigDisabled is set to true, this is
   * equivalent to DISALLOW_ALL_TETHERING.
   */
  public const TETHERING_SETTINGS_TETHERING_SETTINGS_UNSPECIFIED = 'TETHERING_SETTINGS_UNSPECIFIED';
  /**
   * Allows configuration and use of all forms of tethering.
   * tetheringConfigDisabled is ignored.
   */
  public const TETHERING_SETTINGS_ALLOW_ALL_TETHERING = 'ALLOW_ALL_TETHERING';
  /**
   * Disallows the user from using Wi-Fi tethering. Supported on company owned
   * devices running Android 13 and above. If the setting is not supported,
   * ALLOW_ALL_TETHERING will be set. A NonComplianceDetail with API_LEVEL is
   * reported if the Android version is less than 13. tetheringConfigDisabled is
   * ignored.
   */
  public const TETHERING_SETTINGS_DISALLOW_WIFI_TETHERING = 'DISALLOW_WIFI_TETHERING';
  /**
   * Disallows all forms of tethering. Supported on fully managed devices and
   * work profile on company-owned devices, on all supported android versions.
   * The setting tetheringConfigDisabled is ignored.
   */
  public const TETHERING_SETTINGS_DISALLOW_ALL_TETHERING = 'DISALLOW_ALL_TETHERING';
  /**
   * Unspecified. Defaults to DISALLOW_USB_FILE_TRANSFER.
   */
  public const USB_DATA_ACCESS_USB_DATA_ACCESS_UNSPECIFIED = 'USB_DATA_ACCESS_UNSPECIFIED';
  /**
   * All types of USB data transfers are allowed. usbFileTransferDisabled is
   * ignored.
   */
  public const USB_DATA_ACCESS_ALLOW_USB_DATA_TRANSFER = 'ALLOW_USB_DATA_TRANSFER';
  /**
   * Transferring files over USB is disallowed. Other types of USB data
   * connections, such as mouse and keyboard connection, are allowed.
   * usbFileTransferDisabled is ignored.
   */
  public const USB_DATA_ACCESS_DISALLOW_USB_FILE_TRANSFER = 'DISALLOW_USB_FILE_TRANSFER';
  /**
   * When set, all types of USB data transfers are prohibited. Supported for
   * devices running Android 12 or above with USB HAL 1.3 or above. If the
   * setting is not supported, DISALLOW_USB_FILE_TRANSFER will be set. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 12. A NonComplianceDetail with DEVICE_INCOMPATIBLE is reported if
   * the device does not have USB HAL 1.3 or above. usbFileTransferDisabled is
   * ignored.
   */
  public const USB_DATA_ACCESS_DISALLOW_USB_DATA_TRANSFER = 'DISALLOW_USB_DATA_TRANSFER';
  /**
   * Unspecified. Defaults to ALLOW_WIFI_DIRECT
   */
  public const WIFI_DIRECT_SETTINGS_WIFI_DIRECT_SETTINGS_UNSPECIFIED = 'WIFI_DIRECT_SETTINGS_UNSPECIFIED';
  /**
   * The user is allowed to use Wi-Fi direct.
   */
  public const WIFI_DIRECT_SETTINGS_ALLOW_WIFI_DIRECT = 'ALLOW_WIFI_DIRECT';
  /**
   * The user is not allowed to use Wi-Fi direct. A NonComplianceDetail with
   * API_LEVEL is reported if the Android version is less than 13.
   */
  public const WIFI_DIRECT_SETTINGS_DISALLOW_WIFI_DIRECT = 'DISALLOW_WIFI_DIRECT';
  protected $apnPolicyType = ApnPolicy::class;
  protected $apnPolicyDataType = '';
  /**
   * Optional. Controls whether Bluetooth sharing is allowed.
   *
   * @var string
   */
  public $bluetoothSharing;
  /**
   * Controls Wi-Fi configuring privileges. Based on the option set, user will
   * have either full or limited or no control in configuring Wi-Fi networks.
   *
   * @var string
   */
  public $configureWifi;
  protected $preferentialNetworkServiceSettingsType = PreferentialNetworkServiceSettings::class;
  protected $preferentialNetworkServiceSettingsDataType = '';
  /**
   * Controls tethering settings. Based on the value set, the user is partially
   * or fully disallowed from using different forms of tethering.
   *
   * @var string
   */
  public $tetheringSettings;
  /**
   * Controls what files and/or data can be transferred via USB. Supported only
   * on company-owned devices.
   *
   * @var string
   */
  public $usbDataAccess;
  /**
   * Controls configuring and using Wi-Fi direct settings. Supported on company-
   * owned devices running Android 13 and above.
   *
   * @var string
   */
  public $wifiDirectSettings;
  protected $wifiRoamingPolicyType = WifiRoamingPolicy::class;
  protected $wifiRoamingPolicyDataType = '';
  protected $wifiSsidPolicyType = WifiSsidPolicy::class;
  protected $wifiSsidPolicyDataType = '';

  /**
   * Optional. Access Point Name (APN) policy. Configuration for Access Point
   * Names (APNs) which may override any other APNs on the device. See
   * OVERRIDE_APNS_ENABLED and overrideApns for details.
   *
   * @param ApnPolicy $apnPolicy
   */
  public function setApnPolicy(ApnPolicy $apnPolicy)
  {
    $this->apnPolicy = $apnPolicy;
  }
  /**
   * @return ApnPolicy
   */
  public function getApnPolicy()
  {
    return $this->apnPolicy;
  }
  /**
   * Optional. Controls whether Bluetooth sharing is allowed.
   *
   * Accepted values: BLUETOOTH_SHARING_UNSPECIFIED, BLUETOOTH_SHARING_ALLOWED,
   * BLUETOOTH_SHARING_DISALLOWED
   *
   * @param self::BLUETOOTH_SHARING_* $bluetoothSharing
   */
  public function setBluetoothSharing($bluetoothSharing)
  {
    $this->bluetoothSharing = $bluetoothSharing;
  }
  /**
   * @return self::BLUETOOTH_SHARING_*
   */
  public function getBluetoothSharing()
  {
    return $this->bluetoothSharing;
  }
  /**
   * Controls Wi-Fi configuring privileges. Based on the option set, user will
   * have either full or limited or no control in configuring Wi-Fi networks.
   *
   * Accepted values: CONFIGURE_WIFI_UNSPECIFIED, ALLOW_CONFIGURING_WIFI,
   * DISALLOW_ADD_WIFI_CONFIG, DISALLOW_CONFIGURING_WIFI
   *
   * @param self::CONFIGURE_WIFI_* $configureWifi
   */
  public function setConfigureWifi($configureWifi)
  {
    $this->configureWifi = $configureWifi;
  }
  /**
   * @return self::CONFIGURE_WIFI_*
   */
  public function getConfigureWifi()
  {
    return $this->configureWifi;
  }
  /**
   * Optional. Preferential network service configuration. Setting this field
   * will override preferentialNetworkService. This can be set on both work
   * profiles and fully managed devices on Android 13 and above. See 5G network
   * slicing (https://developers.google.com/android/management/5g-network-
   * slicing) guide for more details.
   *
   * @param PreferentialNetworkServiceSettings $preferentialNetworkServiceSettings
   */
  public function setPreferentialNetworkServiceSettings(PreferentialNetworkServiceSettings $preferentialNetworkServiceSettings)
  {
    $this->preferentialNetworkServiceSettings = $preferentialNetworkServiceSettings;
  }
  /**
   * @return PreferentialNetworkServiceSettings
   */
  public function getPreferentialNetworkServiceSettings()
  {
    return $this->preferentialNetworkServiceSettings;
  }
  /**
   * Controls tethering settings. Based on the value set, the user is partially
   * or fully disallowed from using different forms of tethering.
   *
   * Accepted values: TETHERING_SETTINGS_UNSPECIFIED, ALLOW_ALL_TETHERING,
   * DISALLOW_WIFI_TETHERING, DISALLOW_ALL_TETHERING
   *
   * @param self::TETHERING_SETTINGS_* $tetheringSettings
   */
  public function setTetheringSettings($tetheringSettings)
  {
    $this->tetheringSettings = $tetheringSettings;
  }
  /**
   * @return self::TETHERING_SETTINGS_*
   */
  public function getTetheringSettings()
  {
    return $this->tetheringSettings;
  }
  /**
   * Controls what files and/or data can be transferred via USB. Supported only
   * on company-owned devices.
   *
   * Accepted values: USB_DATA_ACCESS_UNSPECIFIED, ALLOW_USB_DATA_TRANSFER,
   * DISALLOW_USB_FILE_TRANSFER, DISALLOW_USB_DATA_TRANSFER
   *
   * @param self::USB_DATA_ACCESS_* $usbDataAccess
   */
  public function setUsbDataAccess($usbDataAccess)
  {
    $this->usbDataAccess = $usbDataAccess;
  }
  /**
   * @return self::USB_DATA_ACCESS_*
   */
  public function getUsbDataAccess()
  {
    return $this->usbDataAccess;
  }
  /**
   * Controls configuring and using Wi-Fi direct settings. Supported on company-
   * owned devices running Android 13 and above.
   *
   * Accepted values: WIFI_DIRECT_SETTINGS_UNSPECIFIED, ALLOW_WIFI_DIRECT,
   * DISALLOW_WIFI_DIRECT
   *
   * @param self::WIFI_DIRECT_SETTINGS_* $wifiDirectSettings
   */
  public function setWifiDirectSettings($wifiDirectSettings)
  {
    $this->wifiDirectSettings = $wifiDirectSettings;
  }
  /**
   * @return self::WIFI_DIRECT_SETTINGS_*
   */
  public function getWifiDirectSettings()
  {
    return $this->wifiDirectSettings;
  }
  /**
   * Optional. Wi-Fi roaming policy.
   *
   * @param WifiRoamingPolicy $wifiRoamingPolicy
   */
  public function setWifiRoamingPolicy(WifiRoamingPolicy $wifiRoamingPolicy)
  {
    $this->wifiRoamingPolicy = $wifiRoamingPolicy;
  }
  /**
   * @return WifiRoamingPolicy
   */
  public function getWifiRoamingPolicy()
  {
    return $this->wifiRoamingPolicy;
  }
  /**
   * Restrictions on which Wi-Fi SSIDs the device can connect to. Note that this
   * does not affect which networks can be configured on the device. Supported
   * on company-owned devices running Android 13 and above.
   *
   * @param WifiSsidPolicy $wifiSsidPolicy
   */
  public function setWifiSsidPolicy(WifiSsidPolicy $wifiSsidPolicy)
  {
    $this->wifiSsidPolicy = $wifiSsidPolicy;
  }
  /**
   * @return WifiSsidPolicy
   */
  public function getWifiSsidPolicy()
  {
    return $this->wifiSsidPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceConnectivityManagement::class, 'Google_Service_AndroidManagement_DeviceConnectivityManagement');
