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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1Device extends \Google\Collection
{
  /**
   * Default value.
   */
  public const COMPROMISED_STATE_COMPROMISED_STATE_UNSPECIFIED = 'COMPROMISED_STATE_UNSPECIFIED';
  /**
   * The device is compromised (currently, this means Android device is rooted).
   */
  public const COMPROMISED_STATE_COMPROMISED = 'COMPROMISED';
  /**
   * The device is safe (currently, this means Android device is unrooted).
   */
  public const COMPROMISED_STATE_UNCOMPROMISED = 'UNCOMPROMISED';
  /**
   * Unknown device type
   */
  public const DEVICE_TYPE_DEVICE_TYPE_UNSPECIFIED = 'DEVICE_TYPE_UNSPECIFIED';
  /**
   * Device is an Android device
   */
  public const DEVICE_TYPE_ANDROID = 'ANDROID';
  /**
   * Device is an iOS device
   */
  public const DEVICE_TYPE_IOS = 'IOS';
  /**
   * Device is a Google Sync device.
   */
  public const DEVICE_TYPE_GOOGLE_SYNC = 'GOOGLE_SYNC';
  /**
   * Device is a Windows device.
   */
  public const DEVICE_TYPE_WINDOWS = 'WINDOWS';
  /**
   * Device is a MacOS device.
   */
  public const DEVICE_TYPE_MAC_OS = 'MAC_OS';
  /**
   * Device is a Linux device.
   */
  public const DEVICE_TYPE_LINUX = 'LINUX';
  /**
   * Device is a ChromeOS device.
   */
  public const DEVICE_TYPE_CHROME_OS = 'CHROME_OS';
  /**
   * Encryption Status is not set.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_UNSPECIFIED = 'ENCRYPTION_STATE_UNSPECIFIED';
  /**
   * Device doesn't support encryption.
   */
  public const ENCRYPTION_STATE_UNSUPPORTED_BY_DEVICE = 'UNSUPPORTED_BY_DEVICE';
  /**
   * Device is encrypted.
   */
  public const ENCRYPTION_STATE_ENCRYPTED = 'ENCRYPTED';
  /**
   * Device is not encrypted.
   */
  public const ENCRYPTION_STATE_NOT_ENCRYPTED = 'NOT_ENCRYPTED';
  /**
   * Default value. This value is unused.
   */
  public const MANAGEMENT_STATE_MANAGEMENT_STATE_UNSPECIFIED = 'MANAGEMENT_STATE_UNSPECIFIED';
  /**
   * Device is approved.
   */
  public const MANAGEMENT_STATE_APPROVED = 'APPROVED';
  /**
   * Device is blocked.
   */
  public const MANAGEMENT_STATE_BLOCKED = 'BLOCKED';
  /**
   * Device is pending approval.
   */
  public const MANAGEMENT_STATE_PENDING = 'PENDING';
  /**
   * The device is not provisioned. Device will start from this state until some
   * action is taken (i.e. a user starts using the device).
   */
  public const MANAGEMENT_STATE_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * Data and settings on the device are being removed.
   */
  public const MANAGEMENT_STATE_WIPING = 'WIPING';
  /**
   * All data and settings on the device are removed.
   */
  public const MANAGEMENT_STATE_WIPED = 'WIPED';
  /**
   * Default value. The value is unused.
   */
  public const OWNER_TYPE_DEVICE_OWNERSHIP_UNSPECIFIED = 'DEVICE_OWNERSHIP_UNSPECIFIED';
  /**
   * Company owns the device.
   */
  public const OWNER_TYPE_COMPANY = 'COMPANY';
  /**
   * Bring Your Own Device (i.e. individual owns the device)
   */
  public const OWNER_TYPE_BYOD = 'BYOD';
  protected $collection_key = 'wifiMacAddresses';
  protected $androidSpecificAttributesType = GoogleAppsCloudidentityDevicesV1AndroidAttributes::class;
  protected $androidSpecificAttributesDataType = '';
  /**
   * Asset tag of the device.
   *
   * @var string
   */
  public $assetTag;
  /**
   * Output only. Baseband version of the device.
   *
   * @var string
   */
  public $basebandVersion;
  /**
   * Output only. Device bootloader version. Example: 0.6.7.
   *
   * @var string
   */
  public $bootloaderVersion;
  /**
   * Output only. Device brand. Example: Samsung.
   *
   * @var string
   */
  public $brand;
  /**
   * Output only. Build number of the device.
   *
   * @var string
   */
  public $buildNumber;
  /**
   * Output only. Represents whether the Device is compromised.
   *
   * @var string
   */
  public $compromisedState;
  /**
   * Output only. When the Company-Owned device was imported. This field is
   * empty for BYOD devices.
   *
   * @var string
   */
  public $createTime;
  /**
   * Unique identifier for the device.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Output only. Type of device.
   *
   * @var string
   */
  public $deviceType;
  /**
   * Output only. Whether developer options is enabled on device.
   *
   * @var bool
   */
  public $enabledDeveloperOptions;
  /**
   * Output only. Whether USB debugging is enabled on device.
   *
   * @var bool
   */
  public $enabledUsbDebugging;
  /**
   * Output only. Device encryption state.
   *
   * @var string
   */
  public $encryptionState;
  protected $endpointVerificationSpecificAttributesType = GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes::class;
  protected $endpointVerificationSpecificAttributesDataType = '';
  /**
   * Host name of the device.
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. IMEI number of device if GSM device; empty otherwise.
   *
   * @var string
   */
  public $imei;
  /**
   * Output only. Kernel version of the device.
   *
   * @var string
   */
  public $kernelVersion;
  /**
   * Most recent time when device synced with this service.
   *
   * @var string
   */
  public $lastSyncTime;
  /**
   * Output only. Management state of the device
   *
   * @var string
   */
  public $managementState;
  /**
   * Output only. Device manufacturer. Example: Motorola.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Output only. MEID number of device if CDMA device; empty otherwise.
   *
   * @var string
   */
  public $meid;
  /**
   * Output only. Model name of device. Example: Pixel 3.
   *
   * @var string
   */
  public $model;
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Device in
   * format: `devices/{device}`, where device is the unique id assigned to the
   * Device. Important: Device API scopes require that you use domain-wide
   * delegation to access the API. For more information, see [Set up the Devices
   * API](https://cloud.google.com/identity/docs/how-to/setup-devices).
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Mobile or network operator of device, if available.
   *
   * @var string
   */
  public $networkOperator;
  /**
   * Output only. OS version of the device. Example: Android 8.1.0.
   *
   * @var string
   */
  public $osVersion;
  /**
   * Output only. Domain name for Google accounts on device. Type for other
   * accounts on device. On Android, will only be populated if
   * |ownership_privilege| is |PROFILE_OWNER| or |DEVICE_OWNER|. Does not
   * include the account signed in to the device policy app if that account's
   * domain has only one account. Examples: "com.example", "xyz.com".
   *
   * @var string[]
   */
  public $otherAccounts;
  /**
   * Output only. Whether the device is owned by the company or an individual
   *
   * @var string
   */
  public $ownerType;
  /**
   * Output only. OS release version. Example: 6.0.
   *
   * @var string
   */
  public $releaseVersion;
  /**
   * Output only. OS security patch update time on device.
   *
   * @var string
   */
  public $securityPatchTime;
  /**
   * Serial Number of device. Example: HT82V1A01076.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. Unified device id of the device.
   *
   * @var string
   */
  public $unifiedDeviceId;
  /**
   * WiFi MAC addresses of device.
   *
   * @var string[]
   */
  public $wifiMacAddresses;

  /**
   * Output only. Attributes specific to Android devices.
   *
   * @param GoogleAppsCloudidentityDevicesV1AndroidAttributes $androidSpecificAttributes
   */
  public function setAndroidSpecificAttributes(GoogleAppsCloudidentityDevicesV1AndroidAttributes $androidSpecificAttributes)
  {
    $this->androidSpecificAttributes = $androidSpecificAttributes;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1AndroidAttributes
   */
  public function getAndroidSpecificAttributes()
  {
    return $this->androidSpecificAttributes;
  }
  /**
   * Asset tag of the device.
   *
   * @param string $assetTag
   */
  public function setAssetTag($assetTag)
  {
    $this->assetTag = $assetTag;
  }
  /**
   * @return string
   */
  public function getAssetTag()
  {
    return $this->assetTag;
  }
  /**
   * Output only. Baseband version of the device.
   *
   * @param string $basebandVersion
   */
  public function setBasebandVersion($basebandVersion)
  {
    $this->basebandVersion = $basebandVersion;
  }
  /**
   * @return string
   */
  public function getBasebandVersion()
  {
    return $this->basebandVersion;
  }
  /**
   * Output only. Device bootloader version. Example: 0.6.7.
   *
   * @param string $bootloaderVersion
   */
  public function setBootloaderVersion($bootloaderVersion)
  {
    $this->bootloaderVersion = $bootloaderVersion;
  }
  /**
   * @return string
   */
  public function getBootloaderVersion()
  {
    return $this->bootloaderVersion;
  }
  /**
   * Output only. Device brand. Example: Samsung.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * Output only. Build number of the device.
   *
   * @param string $buildNumber
   */
  public function setBuildNumber($buildNumber)
  {
    $this->buildNumber = $buildNumber;
  }
  /**
   * @return string
   */
  public function getBuildNumber()
  {
    return $this->buildNumber;
  }
  /**
   * Output only. Represents whether the Device is compromised.
   *
   * Accepted values: COMPROMISED_STATE_UNSPECIFIED, COMPROMISED, UNCOMPROMISED
   *
   * @param self::COMPROMISED_STATE_* $compromisedState
   */
  public function setCompromisedState($compromisedState)
  {
    $this->compromisedState = $compromisedState;
  }
  /**
   * @return self::COMPROMISED_STATE_*
   */
  public function getCompromisedState()
  {
    return $this->compromisedState;
  }
  /**
   * Output only. When the Company-Owned device was imported. This field is
   * empty for BYOD devices.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Unique identifier for the device.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Output only. Type of device.
   *
   * Accepted values: DEVICE_TYPE_UNSPECIFIED, ANDROID, IOS, GOOGLE_SYNC,
   * WINDOWS, MAC_OS, LINUX, CHROME_OS
   *
   * @param self::DEVICE_TYPE_* $deviceType
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return self::DEVICE_TYPE_*
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * Output only. Whether developer options is enabled on device.
   *
   * @param bool $enabledDeveloperOptions
   */
  public function setEnabledDeveloperOptions($enabledDeveloperOptions)
  {
    $this->enabledDeveloperOptions = $enabledDeveloperOptions;
  }
  /**
   * @return bool
   */
  public function getEnabledDeveloperOptions()
  {
    return $this->enabledDeveloperOptions;
  }
  /**
   * Output only. Whether USB debugging is enabled on device.
   *
   * @param bool $enabledUsbDebugging
   */
  public function setEnabledUsbDebugging($enabledUsbDebugging)
  {
    $this->enabledUsbDebugging = $enabledUsbDebugging;
  }
  /**
   * @return bool
   */
  public function getEnabledUsbDebugging()
  {
    return $this->enabledUsbDebugging;
  }
  /**
   * Output only. Device encryption state.
   *
   * Accepted values: ENCRYPTION_STATE_UNSPECIFIED, UNSUPPORTED_BY_DEVICE,
   * ENCRYPTED, NOT_ENCRYPTED
   *
   * @param self::ENCRYPTION_STATE_* $encryptionState
   */
  public function setEncryptionState($encryptionState)
  {
    $this->encryptionState = $encryptionState;
  }
  /**
   * @return self::ENCRYPTION_STATE_*
   */
  public function getEncryptionState()
  {
    return $this->encryptionState;
  }
  /**
   * Output only. Attributes specific to [Endpoint
   * Verification](https://cloud.google.com/endpoint-verification/docs/overview)
   * devices.
   *
   * @param GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes $endpointVerificationSpecificAttributes
   */
  public function setEndpointVerificationSpecificAttributes(GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes $endpointVerificationSpecificAttributes)
  {
    $this->endpointVerificationSpecificAttributes = $endpointVerificationSpecificAttributes;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1EndpointVerificationSpecificAttributes
   */
  public function getEndpointVerificationSpecificAttributes()
  {
    return $this->endpointVerificationSpecificAttributes;
  }
  /**
   * Host name of the device.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. IMEI number of device if GSM device; empty otherwise.
   *
   * @param string $imei
   */
  public function setImei($imei)
  {
    $this->imei = $imei;
  }
  /**
   * @return string
   */
  public function getImei()
  {
    return $this->imei;
  }
  /**
   * Output only. Kernel version of the device.
   *
   * @param string $kernelVersion
   */
  public function setKernelVersion($kernelVersion)
  {
    $this->kernelVersion = $kernelVersion;
  }
  /**
   * @return string
   */
  public function getKernelVersion()
  {
    return $this->kernelVersion;
  }
  /**
   * Most recent time when device synced with this service.
   *
   * @param string $lastSyncTime
   */
  public function setLastSyncTime($lastSyncTime)
  {
    $this->lastSyncTime = $lastSyncTime;
  }
  /**
   * @return string
   */
  public function getLastSyncTime()
  {
    return $this->lastSyncTime;
  }
  /**
   * Output only. Management state of the device
   *
   * Accepted values: MANAGEMENT_STATE_UNSPECIFIED, APPROVED, BLOCKED, PENDING,
   * UNPROVISIONED, WIPING, WIPED
   *
   * @param self::MANAGEMENT_STATE_* $managementState
   */
  public function setManagementState($managementState)
  {
    $this->managementState = $managementState;
  }
  /**
   * @return self::MANAGEMENT_STATE_*
   */
  public function getManagementState()
  {
    return $this->managementState;
  }
  /**
   * Output only. Device manufacturer. Example: Motorola.
   *
   * @param string $manufacturer
   */
  public function setManufacturer($manufacturer)
  {
    $this->manufacturer = $manufacturer;
  }
  /**
   * @return string
   */
  public function getManufacturer()
  {
    return $this->manufacturer;
  }
  /**
   * Output only. MEID number of device if CDMA device; empty otherwise.
   *
   * @param string $meid
   */
  public function setMeid($meid)
  {
    $this->meid = $meid;
  }
  /**
   * @return string
   */
  public function getMeid()
  {
    return $this->meid;
  }
  /**
   * Output only. Model name of device. Example: Pixel 3.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Device in
   * format: `devices/{device}`, where device is the unique id assigned to the
   * Device. Important: Device API scopes require that you use domain-wide
   * delegation to access the API. For more information, see [Set up the Devices
   * API](https://cloud.google.com/identity/docs/how-to/setup-devices).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Mobile or network operator of device, if available.
   *
   * @param string $networkOperator
   */
  public function setNetworkOperator($networkOperator)
  {
    $this->networkOperator = $networkOperator;
  }
  /**
   * @return string
   */
  public function getNetworkOperator()
  {
    return $this->networkOperator;
  }
  /**
   * Output only. OS version of the device. Example: Android 8.1.0.
   *
   * @param string $osVersion
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * Output only. Domain name for Google accounts on device. Type for other
   * accounts on device. On Android, will only be populated if
   * |ownership_privilege| is |PROFILE_OWNER| or |DEVICE_OWNER|. Does not
   * include the account signed in to the device policy app if that account's
   * domain has only one account. Examples: "com.example", "xyz.com".
   *
   * @param string[] $otherAccounts
   */
  public function setOtherAccounts($otherAccounts)
  {
    $this->otherAccounts = $otherAccounts;
  }
  /**
   * @return string[]
   */
  public function getOtherAccounts()
  {
    return $this->otherAccounts;
  }
  /**
   * Output only. Whether the device is owned by the company or an individual
   *
   * Accepted values: DEVICE_OWNERSHIP_UNSPECIFIED, COMPANY, BYOD
   *
   * @param self::OWNER_TYPE_* $ownerType
   */
  public function setOwnerType($ownerType)
  {
    $this->ownerType = $ownerType;
  }
  /**
   * @return self::OWNER_TYPE_*
   */
  public function getOwnerType()
  {
    return $this->ownerType;
  }
  /**
   * Output only. OS release version. Example: 6.0.
   *
   * @param string $releaseVersion
   */
  public function setReleaseVersion($releaseVersion)
  {
    $this->releaseVersion = $releaseVersion;
  }
  /**
   * @return string
   */
  public function getReleaseVersion()
  {
    return $this->releaseVersion;
  }
  /**
   * Output only. OS security patch update time on device.
   *
   * @param string $securityPatchTime
   */
  public function setSecurityPatchTime($securityPatchTime)
  {
    $this->securityPatchTime = $securityPatchTime;
  }
  /**
   * @return string
   */
  public function getSecurityPatchTime()
  {
    return $this->securityPatchTime;
  }
  /**
   * Serial Number of device. Example: HT82V1A01076.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Output only. Unified device id of the device.
   *
   * @param string $unifiedDeviceId
   */
  public function setUnifiedDeviceId($unifiedDeviceId)
  {
    $this->unifiedDeviceId = $unifiedDeviceId;
  }
  /**
   * @return string
   */
  public function getUnifiedDeviceId()
  {
    return $this->unifiedDeviceId;
  }
  /**
   * WiFi MAC addresses of device.
   *
   * @param string[] $wifiMacAddresses
   */
  public function setWifiMacAddresses($wifiMacAddresses)
  {
    $this->wifiMacAddresses = $wifiMacAddresses;
  }
  /**
   * @return string[]
   */
  public function getWifiMacAddresses()
  {
    return $this->wifiMacAddresses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1Device::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1Device');
