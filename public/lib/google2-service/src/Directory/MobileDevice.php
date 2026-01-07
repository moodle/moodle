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

namespace Google\Service\Directory;

class MobileDevice extends \Google\Collection
{
  protected $collection_key = 'otherAccountsInfo';
  /**
   * Adb (USB debugging) enabled or disabled on device (Read-only)
   *
   * @var bool
   */
  public $adbStatus;
  protected $applicationsType = MobileDeviceApplications::class;
  protected $applicationsDataType = 'array';
  /**
   * The device's baseband version.
   *
   * @var string
   */
  public $basebandVersion;
  /**
   * Mobile Device Bootloader version (Read-only)
   *
   * @var string
   */
  public $bootloaderVersion;
  /**
   * Mobile Device Brand (Read-only)
   *
   * @var string
   */
  public $brand;
  /**
   * The device's operating system build number.
   *
   * @var string
   */
  public $buildNumber;
  /**
   * The default locale used on the device.
   *
   * @var string
   */
  public $defaultLanguage;
  /**
   * Developer options enabled or disabled on device (Read-only)
   *
   * @var bool
   */
  public $developerOptionsStatus;
  /**
   * The compromised device status.
   *
   * @var string
   */
  public $deviceCompromisedStatus;
  /**
   * The serial number for a Google Sync mobile device. For Android and iOS
   * devices, this is a software generated unique identifier.
   *
   * @var string
   */
  public $deviceId;
  /**
   * DevicePasswordStatus (Read-only)
   *
   * @var string
   */
  public $devicePasswordStatus;
  /**
   * The list of the owner's email addresses. If your application needs the
   * current list of user emails, use the [get](https://developers.google.com/wo
   * rkspace/admin/directory/v1/reference/mobiledevices/get.html) method. For
   * additional information, see the [retrieve a user](https://developers.google
   * .com/workspace/admin/directory/v1/guides/manage-users#get_user) method.
   *
   * @var string[]
   */
  public $email;
  /**
   * Mobile Device Encryption Status (Read-only)
   *
   * @var string
   */
  public $encryptionStatus;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Date and time the device was first synchronized with the policy settings in
   * the G Suite administrator control panel (Read-only)
   *
   * @var string
   */
  public $firstSync;
  /**
   * Mobile Device Hardware (Read-only)
   *
   * @var string
   */
  public $hardware;
  /**
   * The IMEI/MEID unique identifier for Android hardware. It is not applicable
   * to Google Sync devices. When adding an Android mobile device, this is an
   * optional property. When updating one of these devices, this is a read-only
   * property.
   *
   * @var string
   */
  public $hardwareId;
  /**
   * The device's IMEI number.
   *
   * @var string
   */
  public $imei;
  /**
   * The device's kernel version.
   *
   * @var string
   */
  public $kernelVersion;
  /**
   * The type of the API resource. For Mobiledevices resources, the value is
   * `admin#directory#mobiledevice`.
   *
   * @var string
   */
  public $kind;
  /**
   * Date and time the device was last synchronized with the policy settings in
   * the G Suite administrator control panel (Read-only)
   *
   * @var string
   */
  public $lastSync;
  /**
   * Boolean indicating if this account is on owner/primary profile or not.
   *
   * @var bool
   */
  public $managedAccountIsOnOwnerProfile;
  /**
   * Mobile Device manufacturer (Read-only)
   *
   * @var string
   */
  public $manufacturer;
  /**
   * The device's MEID number.
   *
   * @var string
   */
  public $meid;
  /**
   * The mobile device's model name, for example Nexus S. This property can be [
   * updated](https://developers.google.com/workspace/admin/directory/v1/referen
   * ce/mobiledevices/update.html). For more information, see the [Developer's G
   * uide](https://developers.google.com/workspace/admin/directory/v1/guides/man
   * age-mobile=devices#update_mobile_device).
   *
   * @var string
   */
  public $model;
  /**
   * The list of the owner's user names. If your application needs the current
   * list of device owner names, use the [get](https://developers.google.com/wor
   * kspace/admin/directory/v1/reference/mobiledevices/get.html) method. For
   * more information about retrieving mobile device user information, see the
   * [Developer's Guide](https://developers.google.com/workspace/admin/directory
   * /v1/guides/manage-users#get_user).
   *
   * @var string[]
   */
  public $name;
  /**
   * Mobile Device mobile or network operator (if available) (Read-only)
   *
   * @var string
   */
  public $networkOperator;
  /**
   * The mobile device's operating system, for example IOS 4.3 or Android 2.3.5.
   * This property can be [updated](https://developers.google.com/workspace/admi
   * n/directory/v1/reference/mobiledevices/update.html). For more information,
   * see the [Developer's Guide](https://developers.google.com/workspace/admin/d
   * irectory/v1/guides/manage-mobile-devices#update_mobile_device).
   *
   * @var string
   */
  public $os;
  /**
   * The list of accounts added on device (Read-only)
   *
   * @var string[]
   */
  public $otherAccountsInfo;
  /**
   * DMAgentPermission (Read-only)
   *
   * @var string
   */
  public $privilege;
  /**
   * Mobile Device release version version (Read-only)
   *
   * @var string
   */
  public $releaseVersion;
  /**
   * The unique ID the API service uses to identify the mobile device.
   *
   * @var string
   */
  public $resourceId;
  /**
   * Mobile Device Security patch level (Read-only)
   *
   * @var string
   */
  public $securityPatchLevel;
  /**
   * The device's serial number.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * The device's status.
   *
   * @var string
   */
  public $status;
  /**
   * Work profile supported on device (Read-only)
   *
   * @var bool
   */
  public $supportsWorkProfile;
  /**
   * The type of mobile device.
   *
   * @var string
   */
  public $type;
  /**
   * Unknown sources enabled or disabled on device (Read-only)
   *
   * @var bool
   */
  public $unknownSourcesStatus;
  /**
   * Gives information about the device such as `os` version. This property can
   * be [updated](https://developers.google.com/workspace/admin/directory/v1/ref
   * erence/mobiledevices/update.html). For more information, see the
   * [Developer's Guide](https://developers.google.com/workspace/admin/directory
   * /v1/guides/manage-mobile-devices#update_mobile_device).
   *
   * @var string
   */
  public $userAgent;
  /**
   * The device's MAC address on Wi-Fi networks.
   *
   * @var string
   */
  public $wifiMacAddress;

  /**
   * Adb (USB debugging) enabled or disabled on device (Read-only)
   *
   * @param bool $adbStatus
   */
  public function setAdbStatus($adbStatus)
  {
    $this->adbStatus = $adbStatus;
  }
  /**
   * @return bool
   */
  public function getAdbStatus()
  {
    return $this->adbStatus;
  }
  /**
   * The list of applications installed on an Android mobile device. It is not
   * applicable to Google Sync and iOS devices. The list includes any Android
   * applications that access Google Workspace data. When updating an
   * applications list, it is important to note that updates replace the
   * existing list. If the Android device has two existing applications and the
   * API updates the list with five applications, the is now the updated list of
   * five applications.
   *
   * @param MobileDeviceApplications[] $applications
   */
  public function setApplications($applications)
  {
    $this->applications = $applications;
  }
  /**
   * @return MobileDeviceApplications[]
   */
  public function getApplications()
  {
    return $this->applications;
  }
  /**
   * The device's baseband version.
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
   * Mobile Device Bootloader version (Read-only)
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
   * Mobile Device Brand (Read-only)
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
   * The device's operating system build number.
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
   * The default locale used on the device.
   *
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * Developer options enabled or disabled on device (Read-only)
   *
   * @param bool $developerOptionsStatus
   */
  public function setDeveloperOptionsStatus($developerOptionsStatus)
  {
    $this->developerOptionsStatus = $developerOptionsStatus;
  }
  /**
   * @return bool
   */
  public function getDeveloperOptionsStatus()
  {
    return $this->developerOptionsStatus;
  }
  /**
   * The compromised device status.
   *
   * @param string $deviceCompromisedStatus
   */
  public function setDeviceCompromisedStatus($deviceCompromisedStatus)
  {
    $this->deviceCompromisedStatus = $deviceCompromisedStatus;
  }
  /**
   * @return string
   */
  public function getDeviceCompromisedStatus()
  {
    return $this->deviceCompromisedStatus;
  }
  /**
   * The serial number for a Google Sync mobile device. For Android and iOS
   * devices, this is a software generated unique identifier.
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
   * DevicePasswordStatus (Read-only)
   *
   * @param string $devicePasswordStatus
   */
  public function setDevicePasswordStatus($devicePasswordStatus)
  {
    $this->devicePasswordStatus = $devicePasswordStatus;
  }
  /**
   * @return string
   */
  public function getDevicePasswordStatus()
  {
    return $this->devicePasswordStatus;
  }
  /**
   * The list of the owner's email addresses. If your application needs the
   * current list of user emails, use the [get](https://developers.google.com/wo
   * rkspace/admin/directory/v1/reference/mobiledevices/get.html) method. For
   * additional information, see the [retrieve a user](https://developers.google
   * .com/workspace/admin/directory/v1/guides/manage-users#get_user) method.
   *
   * @param string[] $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string[]
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Mobile Device Encryption Status (Read-only)
   *
   * @param string $encryptionStatus
   */
  public function setEncryptionStatus($encryptionStatus)
  {
    $this->encryptionStatus = $encryptionStatus;
  }
  /**
   * @return string
   */
  public function getEncryptionStatus()
  {
    return $this->encryptionStatus;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Date and time the device was first synchronized with the policy settings in
   * the G Suite administrator control panel (Read-only)
   *
   * @param string $firstSync
   */
  public function setFirstSync($firstSync)
  {
    $this->firstSync = $firstSync;
  }
  /**
   * @return string
   */
  public function getFirstSync()
  {
    return $this->firstSync;
  }
  /**
   * Mobile Device Hardware (Read-only)
   *
   * @param string $hardware
   */
  public function setHardware($hardware)
  {
    $this->hardware = $hardware;
  }
  /**
   * @return string
   */
  public function getHardware()
  {
    return $this->hardware;
  }
  /**
   * The IMEI/MEID unique identifier for Android hardware. It is not applicable
   * to Google Sync devices. When adding an Android mobile device, this is an
   * optional property. When updating one of these devices, this is a read-only
   * property.
   *
   * @param string $hardwareId
   */
  public function setHardwareId($hardwareId)
  {
    $this->hardwareId = $hardwareId;
  }
  /**
   * @return string
   */
  public function getHardwareId()
  {
    return $this->hardwareId;
  }
  /**
   * The device's IMEI number.
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
   * The device's kernel version.
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
   * The type of the API resource. For Mobiledevices resources, the value is
   * `admin#directory#mobiledevice`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Date and time the device was last synchronized with the policy settings in
   * the G Suite administrator control panel (Read-only)
   *
   * @param string $lastSync
   */
  public function setLastSync($lastSync)
  {
    $this->lastSync = $lastSync;
  }
  /**
   * @return string
   */
  public function getLastSync()
  {
    return $this->lastSync;
  }
  /**
   * Boolean indicating if this account is on owner/primary profile or not.
   *
   * @param bool $managedAccountIsOnOwnerProfile
   */
  public function setManagedAccountIsOnOwnerProfile($managedAccountIsOnOwnerProfile)
  {
    $this->managedAccountIsOnOwnerProfile = $managedAccountIsOnOwnerProfile;
  }
  /**
   * @return bool
   */
  public function getManagedAccountIsOnOwnerProfile()
  {
    return $this->managedAccountIsOnOwnerProfile;
  }
  /**
   * Mobile Device manufacturer (Read-only)
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
   * The device's MEID number.
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
   * The mobile device's model name, for example Nexus S. This property can be [
   * updated](https://developers.google.com/workspace/admin/directory/v1/referen
   * ce/mobiledevices/update.html). For more information, see the [Developer's G
   * uide](https://developers.google.com/workspace/admin/directory/v1/guides/man
   * age-mobile=devices#update_mobile_device).
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
   * The list of the owner's user names. If your application needs the current
   * list of device owner names, use the [get](https://developers.google.com/wor
   * kspace/admin/directory/v1/reference/mobiledevices/get.html) method. For
   * more information about retrieving mobile device user information, see the
   * [Developer's Guide](https://developers.google.com/workspace/admin/directory
   * /v1/guides/manage-users#get_user).
   *
   * @param string[] $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string[]
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Mobile Device mobile or network operator (if available) (Read-only)
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
   * The mobile device's operating system, for example IOS 4.3 or Android 2.3.5.
   * This property can be [updated](https://developers.google.com/workspace/admi
   * n/directory/v1/reference/mobiledevices/update.html). For more information,
   * see the [Developer's Guide](https://developers.google.com/workspace/admin/d
   * irectory/v1/guides/manage-mobile-devices#update_mobile_device).
   *
   * @param string $os
   */
  public function setOs($os)
  {
    $this->os = $os;
  }
  /**
   * @return string
   */
  public function getOs()
  {
    return $this->os;
  }
  /**
   * The list of accounts added on device (Read-only)
   *
   * @param string[] $otherAccountsInfo
   */
  public function setOtherAccountsInfo($otherAccountsInfo)
  {
    $this->otherAccountsInfo = $otherAccountsInfo;
  }
  /**
   * @return string[]
   */
  public function getOtherAccountsInfo()
  {
    return $this->otherAccountsInfo;
  }
  /**
   * DMAgentPermission (Read-only)
   *
   * @param string $privilege
   */
  public function setPrivilege($privilege)
  {
    $this->privilege = $privilege;
  }
  /**
   * @return string
   */
  public function getPrivilege()
  {
    return $this->privilege;
  }
  /**
   * Mobile Device release version version (Read-only)
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
   * The unique ID the API service uses to identify the mobile device.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * Mobile Device Security patch level (Read-only)
   *
   * @param string $securityPatchLevel
   */
  public function setSecurityPatchLevel($securityPatchLevel)
  {
    $this->securityPatchLevel = $securityPatchLevel;
  }
  /**
   * @return string
   */
  public function getSecurityPatchLevel()
  {
    return $this->securityPatchLevel;
  }
  /**
   * The device's serial number.
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
   * The device's status.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Work profile supported on device (Read-only)
   *
   * @param bool $supportsWorkProfile
   */
  public function setSupportsWorkProfile($supportsWorkProfile)
  {
    $this->supportsWorkProfile = $supportsWorkProfile;
  }
  /**
   * @return bool
   */
  public function getSupportsWorkProfile()
  {
    return $this->supportsWorkProfile;
  }
  /**
   * The type of mobile device.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Unknown sources enabled or disabled on device (Read-only)
   *
   * @param bool $unknownSourcesStatus
   */
  public function setUnknownSourcesStatus($unknownSourcesStatus)
  {
    $this->unknownSourcesStatus = $unknownSourcesStatus;
  }
  /**
   * @return bool
   */
  public function getUnknownSourcesStatus()
  {
    return $this->unknownSourcesStatus;
  }
  /**
   * Gives information about the device such as `os` version. This property can
   * be [updated](https://developers.google.com/workspace/admin/directory/v1/ref
   * erence/mobiledevices/update.html). For more information, see the
   * [Developer's Guide](https://developers.google.com/workspace/admin/directory
   * /v1/guides/manage-mobile-devices#update_mobile_device).
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * The device's MAC address on Wi-Fi networks.
   *
   * @param string $wifiMacAddress
   */
  public function setWifiMacAddress($wifiMacAddress)
  {
    $this->wifiMacAddress = $wifiMacAddress;
  }
  /**
   * @return string
   */
  public function getWifiMacAddress()
  {
    return $this->wifiMacAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileDevice::class, 'Google_Service_Directory_MobileDevice');
