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

class ChromeOsDevice extends \Google\Collection
{
  /**
   * Chrome OS Type unspecified.
   */
  public const CHROME_OS_TYPE_chromeOsTypeUnspecified = 'chromeOsTypeUnspecified';
  /**
   * Chrome OS Type Chrome OS Flex.
   */
  public const CHROME_OS_TYPE_chromeOsFlex = 'chromeOsFlex';
  /**
   * Chrome OS Type Chrome OS.
   */
  public const CHROME_OS_TYPE_chromeOs = 'chromeOs';
  /**
   * The deprovision reason is unknown.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UNSPECIFIED = 'DEPROVISION_REASON_UNSPECIFIED';
  /**
   * Same model replacement. You have return materials authorization (RMA) or
   * you are replacing a malfunctioning device under warranty with the same
   * device model.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_SAME_MODEL_REPLACEMENT = 'DEPROVISION_REASON_SAME_MODEL_REPLACEMENT';
  /**
   * The device was upgraded.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UPGRADE = 'DEPROVISION_REASON_UPGRADE';
  /**
   * The device's domain was changed.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_DOMAIN_MOVE = 'DEPROVISION_REASON_DOMAIN_MOVE';
  /**
   * Service expired for the device.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_SERVICE_EXPIRATION = 'DEPROVISION_REASON_SERVICE_EXPIRATION';
  /**
   * The device was deprovisioned for a legacy reason that is no longer
   * supported.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_OTHER = 'DEPROVISION_REASON_OTHER';
  /**
   * Different model replacement. You are replacing this device with an upgraded
   * or newer device model.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT = 'DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT';
  /**
   * Retiring from fleet. You are donating, discarding, or otherwise removing
   * the device from use.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_RETIRING_DEVICE = 'DEPROVISION_REASON_RETIRING_DEVICE';
  /**
   * ChromeOS Flex upgrade transfer. This is a ChromeOS Flex device that you are
   * replacing with a Chromebook within a year.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UPGRADE_TRANSFER = 'DEPROVISION_REASON_UPGRADE_TRANSFER';
  /**
   * A reason was not required. For example, the licenses were returned to the
   * customer's license pool.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_NOT_REQUIRED = 'DEPROVISION_REASON_NOT_REQUIRED';
  /**
   * The device was deprovisioned by the Repair Service Center. Can only be set
   * by Repair Service Center during RMA.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_REPAIR_CENTER = 'DEPROVISION_REASON_REPAIR_CENTER';
  /**
   * The license type is unknown.
   */
  public const DEVICE_LICENSE_TYPE_deviceLicenseTypeUnspecified = 'deviceLicenseTypeUnspecified';
  /**
   * The device is bundled with a perpetual Chrome Enterprise Upgrade.
   */
  public const DEVICE_LICENSE_TYPE_enterprise = 'enterprise';
  /**
   * The device has an annual standalone Chrome Enterprise Upgrade.
   *
   * @deprecated
   */
  public const DEVICE_LICENSE_TYPE_enterpriseUpgrade = 'enterpriseUpgrade';
  /**
   * The device has a perpetual standalone Chrome Education Upgrade.
   *
   * @deprecated
   */
  public const DEVICE_LICENSE_TYPE_educationUpgrade = 'educationUpgrade';
  /**
   * The device is bundled with a perpetual Chrome Education Upgrade.
   */
  public const DEVICE_LICENSE_TYPE_education = 'education';
  /**
   * The device has an annual Kiosk Upgrade.
   */
  public const DEVICE_LICENSE_TYPE_kioskUpgrade = 'kioskUpgrade';
  /**
   * Indicates that the device is consuming a standalone, perpetual Chrome
   * Enterprise Upgrade, a Chrome Enterprise license.
   */
  public const DEVICE_LICENSE_TYPE_enterpriseUpgradePerpetual = 'enterpriseUpgradePerpetual';
  /**
   * Indicates that the device is consuming a standalone, fixed-term Chrome
   * Enterprise Upgrade, a Chrome Enterprise license.
   */
  public const DEVICE_LICENSE_TYPE_enterpriseUpgradeFixedTerm = 'enterpriseUpgradeFixedTerm';
  /**
   * Indicates that the device is consuming a standalone, perpetual Chrome
   * Education Upgrade(AKA Chrome EDU perpetual license).
   */
  public const DEVICE_LICENSE_TYPE_educationUpgradePerpetual = 'educationUpgradePerpetual';
  /**
   * Indicates that the device is consuming a standalone, fixed-term Chrome
   * Education Upgrade(AKA Chrome EDU fixed-term license).
   */
  public const DEVICE_LICENSE_TYPE_educationUpgradeFixedTerm = 'educationUpgradeFixedTerm';
  /**
   * Compliance status unspecified.
   */
  public const OS_VERSION_COMPLIANCE_complianceUnspecified = 'complianceUnspecified';
  /**
   * Compliance status compliant.
   */
  public const OS_VERSION_COMPLIANCE_compliant = 'compliant';
  /**
   * Compliance status pending.
   */
  public const OS_VERSION_COMPLIANCE_pending = 'pending';
  /**
   * Compliance status not compliant.
   */
  public const OS_VERSION_COMPLIANCE_notCompliant = 'notCompliant';
  protected $collection_key = 'systemRamFreeReports';
  protected $activeTimeRangesType = ChromeOsDeviceActiveTimeRanges::class;
  protected $activeTimeRangesDataType = 'array';
  /**
   * The asset identifier as noted by an administrator or specified during
   * enrollment.
   *
   * @var string
   */
  public $annotatedAssetId;
  /**
   * The address or location of the device as noted by the administrator.
   * Maximum length is `200` characters. Empty values are allowed.
   *
   * @var string
   */
  public $annotatedLocation;
  /**
   * The user of the device as noted by the administrator. Maximum length is 100
   * characters. Empty values are allowed.
   *
   * @var string
   */
  public $annotatedUser;
  /**
   * (Read-only) The timestamp after which the device will stop receiving Chrome
   * updates or support. Please use "autoUpdateThrough" instead.
   *
   * @deprecated
   * @var string
   */
  public $autoUpdateExpiration;
  /**
   * Output only. The timestamp after which the device will stop receiving
   * Chrome updates or support.
   *
   * @var string
   */
  public $autoUpdateThrough;
  protected $backlightInfoType = BacklightInfo::class;
  protected $backlightInfoDataType = 'array';
  protected $bluetoothAdapterInfoType = BluetoothAdapterInfo::class;
  protected $bluetoothAdapterInfoDataType = 'array';
  /**
   * The boot mode for the device. The possible values are: * `Verified`: The
   * device is running a valid version of the Chrome OS. * `Dev`: The devices's
   * developer hardware switch is enabled. When booted, the device has a command
   * line shell. For an example of a developer switch, see the [Chromebook
   * developer information](https://www.chromium.org/chromium-os/developer-
   * information-for-chrome-os-devices/samsung-series-5-chromebook#TOC-
   * Developer-switch).
   *
   * @var string
   */
  public $bootMode;
  /**
   * Output only. Chrome OS type of the device.
   *
   * @var string
   */
  public $chromeOsType;
  protected $cpuInfoType = ChromeOsDeviceCpuInfo::class;
  protected $cpuInfoDataType = 'array';
  protected $cpuStatusReportsType = ChromeOsDeviceCpuStatusReports::class;
  protected $cpuStatusReportsDataType = 'array';
  /**
   * (Read-only) Deprovision reason.
   *
   * @var string
   */
  public $deprovisionReason;
  protected $deviceFilesType = ChromeOsDeviceDeviceFiles::class;
  protected $deviceFilesDataType = 'array';
  /**
   * The unique ID of the Chrome device.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Output only. Device license type.
   *
   * @var string
   */
  public $deviceLicenseType;
  protected $diskSpaceUsageType = ByteUsage::class;
  protected $diskSpaceUsageDataType = '';
  protected $diskVolumeReportsType = ChromeOsDeviceDiskVolumeReports::class;
  protected $diskVolumeReportsDataType = 'array';
  /**
   * (Read-only) Built-in MAC address for the docking station that the device
   * connected to. Factory sets Media access control address (MAC address)
   * assigned for use by a dock. It is reserved specifically for MAC pass
   * through device policy. The format is twelve (12) hexadecimal digits without
   * any delimiter (uppercase letters). This is only relevant for some devices.
   *
   * @var string
   */
  public $dockMacAddress;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The device's MAC address on the ethernet network interface.
   *
   * @var string
   */
  public $ethernetMacAddress;
  /**
   * (Read-only) MAC address used by the Chromebook’s internal ethernet port,
   * and for onboard network (ethernet) interface. The format is twelve (12)
   * hexadecimal digits without any delimiter (uppercase letters). This is only
   * relevant for some devices.
   *
   * @var string
   */
  public $ethernetMacAddress0;
  /**
   * Output only. Whether or not the device requires the extended support opt
   * in.
   *
   * @var bool
   */
  public $extendedSupportEligible;
  /**
   * Output only. Whether extended support policy is enabled on the device.
   *
   * @var bool
   */
  public $extendedSupportEnabled;
  /**
   * Output only. Date of the device when extended support policy for automatic
   * updates starts.
   *
   * @var string
   */
  public $extendedSupportStart;
  protected $fanInfoType = FanInfo::class;
  protected $fanInfoDataType = 'array';
  /**
   * The Chrome device's firmware version.
   *
   * @var string
   */
  public $firmwareVersion;
  /**
   * Date and time for the first time the device was enrolled.
   *
   * @var string
   */
  public $firstEnrollmentTime;
  /**
   * The type of resource. For the Chromeosdevices resource, the value is
   * `admin#directory#chromeosdevice`.
   *
   * @var string
   */
  public $kind;
  /**
   * (Read-only) Date and time for the last deprovision of the device.
   *
   * @var string
   */
  public $lastDeprovisionTimestamp;
  /**
   * Date and time the device was last enrolled (Read-only)
   *
   * @var string
   */
  public $lastEnrollmentTime;
  protected $lastKnownNetworkType = ChromeOsDeviceLastKnownNetwork::class;
  protected $lastKnownNetworkDataType = 'array';
  /**
   * Date and time the device was last synchronized with the policy settings in
   * the G Suite administrator control panel (Read-only)
   *
   * @var string
   */
  public $lastSync;
  /**
   * The device's wireless MAC address. If the device does not have this
   * information, it is not included in the response.
   *
   * @var string
   */
  public $macAddress;
  /**
   * (Read-only) The date the device was manufactured in yyyy-mm-dd format.
   *
   * @var string
   */
  public $manufactureDate;
  /**
   * The Mobile Equipment Identifier (MEID) or the International Mobile
   * Equipment Identity (IMEI) for the 3G mobile card in a mobile device. A
   * MEID/IMEI is typically used when adding a device to a wireless carrier's
   * post-pay service plan. If the device does not have this information, this
   * property is not included in the response. For more information on how to
   * export a MEID/IMEI list, see the [Developer's Guide](https://developers.goo
   * gle.com/workspace/admin/directory/v1/guides/manage-chrome-
   * devices.html#export_meid).
   *
   * @var string
   */
  public $meid;
  /**
   * The device's model information. If the device does not have this
   * information, this property is not included in the response.
   *
   * @var string
   */
  public $model;
  /**
   * Notes about this device added by the administrator. This property can be
   * [searched](https://support.google.com/chrome/a/answer/1698333) with the [li
   * st](https://developers.google.com/workspace/admin/directory/v1/reference/ch
   * romeosdevices/list) method's `query` parameter. Maximum length is 500
   * characters. Empty values are allowed.
   *
   * @var string
   */
  public $notes;
  /**
   * The device's order number. Only devices directly purchased from Google have
   * an order number.
   *
   * @var string
   */
  public $orderNumber;
  /**
   * The unique ID of the organizational unit. orgUnitPath is the human readable
   * version of orgUnitId. While orgUnitPath may change by renaming an
   * organizational unit within the path, orgUnitId is unchangeable for one
   * organizational unit. This property can be [updated](https://developers.goog
   * le.com/workspace/admin/directory/v1/guides/manage-chrome-
   * devices#move_chrome_devices_to_ou) using the API. For more information
   * about how to create an organizational structure for your device, see the
   * [administration help center](https://support.google.com/a/answer/182433).
   *
   * @var string
   */
  public $orgUnitId;
  /**
   * The full parent path with the organizational unit's name associated with
   * the device. Path names are case insensitive. If the parent organizational
   * unit is the top-level organization, it is represented as a forward slash,
   * `/`. This property can be [updated](https://developers.google.com/workspace
   * /admin/directory/v1/guides/manage-chrome-devices#move_chrome_devices_to_ou)
   * using the API. For more information about how to create an organizational
   * structure for your device, see the [administration help
   * center](https://support.google.com/a/answer/182433).
   *
   * @var string
   */
  public $orgUnitPath;
  protected $osUpdateStatusType = OsUpdateStatus::class;
  protected $osUpdateStatusDataType = '';
  /**
   * The Chrome device's operating system version.
   *
   * @var string
   */
  public $osVersion;
  /**
   * Output only. Device policy compliance status of the OS version.
   *
   * @var string
   */
  public $osVersionCompliance;
  /**
   * The Chrome device's platform version.
   *
   * @var string
   */
  public $platformVersion;
  protected $recentUsersType = ChromeOsDeviceRecentUsers::class;
  protected $recentUsersDataType = 'array';
  protected $screenshotFilesType = ChromeOsDeviceScreenshotFiles::class;
  protected $screenshotFilesDataType = 'array';
  /**
   * The Chrome device serial number entered when the device was enabled. This
   * value is the same as the Admin console's *Serial Number* in the *Chrome OS
   * Devices* tab.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * The status of the device.
   *
   * @var string
   */
  public $status;
  /**
   * Final date the device will be supported (Read-only)
   *
   * @var string
   */
  public $supportEndDate;
  protected $systemRamFreeReportsType = ChromeOsDeviceSystemRamFreeReports::class;
  protected $systemRamFreeReportsDataType = 'array';
  /**
   * Total RAM on the device [in bytes] (Read-only)
   *
   * @var string
   */
  public $systemRamTotal;
  protected $tpmVersionInfoType = ChromeOsDeviceTpmVersionInfo::class;
  protected $tpmVersionInfoDataType = '';
  /**
   * Determines if the device will auto renew its support after the support end
   * date. This is a read-only property.
   *
   * @var bool
   */
  public $willAutoRenew;

  /**
   * A list of active time ranges (Read-only).
   *
   * @param ChromeOsDeviceActiveTimeRanges[] $activeTimeRanges
   */
  public function setActiveTimeRanges($activeTimeRanges)
  {
    $this->activeTimeRanges = $activeTimeRanges;
  }
  /**
   * @return ChromeOsDeviceActiveTimeRanges[]
   */
  public function getActiveTimeRanges()
  {
    return $this->activeTimeRanges;
  }
  /**
   * The asset identifier as noted by an administrator or specified during
   * enrollment.
   *
   * @param string $annotatedAssetId
   */
  public function setAnnotatedAssetId($annotatedAssetId)
  {
    $this->annotatedAssetId = $annotatedAssetId;
  }
  /**
   * @return string
   */
  public function getAnnotatedAssetId()
  {
    return $this->annotatedAssetId;
  }
  /**
   * The address or location of the device as noted by the administrator.
   * Maximum length is `200` characters. Empty values are allowed.
   *
   * @param string $annotatedLocation
   */
  public function setAnnotatedLocation($annotatedLocation)
  {
    $this->annotatedLocation = $annotatedLocation;
  }
  /**
   * @return string
   */
  public function getAnnotatedLocation()
  {
    return $this->annotatedLocation;
  }
  /**
   * The user of the device as noted by the administrator. Maximum length is 100
   * characters. Empty values are allowed.
   *
   * @param string $annotatedUser
   */
  public function setAnnotatedUser($annotatedUser)
  {
    $this->annotatedUser = $annotatedUser;
  }
  /**
   * @return string
   */
  public function getAnnotatedUser()
  {
    return $this->annotatedUser;
  }
  /**
   * (Read-only) The timestamp after which the device will stop receiving Chrome
   * updates or support. Please use "autoUpdateThrough" instead.
   *
   * @deprecated
   * @param string $autoUpdateExpiration
   */
  public function setAutoUpdateExpiration($autoUpdateExpiration)
  {
    $this->autoUpdateExpiration = $autoUpdateExpiration;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getAutoUpdateExpiration()
  {
    return $this->autoUpdateExpiration;
  }
  /**
   * Output only. The timestamp after which the device will stop receiving
   * Chrome updates or support.
   *
   * @param string $autoUpdateThrough
   */
  public function setAutoUpdateThrough($autoUpdateThrough)
  {
    $this->autoUpdateThrough = $autoUpdateThrough;
  }
  /**
   * @return string
   */
  public function getAutoUpdateThrough()
  {
    return $this->autoUpdateThrough;
  }
  /**
   * Output only. Contains backlight information for the device.
   *
   * @param BacklightInfo[] $backlightInfo
   */
  public function setBacklightInfo($backlightInfo)
  {
    $this->backlightInfo = $backlightInfo;
  }
  /**
   * @return BacklightInfo[]
   */
  public function getBacklightInfo()
  {
    return $this->backlightInfo;
  }
  /**
   * Output only. Information about Bluetooth adapters of the device.
   *
   * @param BluetoothAdapterInfo[] $bluetoothAdapterInfo
   */
  public function setBluetoothAdapterInfo($bluetoothAdapterInfo)
  {
    $this->bluetoothAdapterInfo = $bluetoothAdapterInfo;
  }
  /**
   * @return BluetoothAdapterInfo[]
   */
  public function getBluetoothAdapterInfo()
  {
    return $this->bluetoothAdapterInfo;
  }
  /**
   * The boot mode for the device. The possible values are: * `Verified`: The
   * device is running a valid version of the Chrome OS. * `Dev`: The devices's
   * developer hardware switch is enabled. When booted, the device has a command
   * line shell. For an example of a developer switch, see the [Chromebook
   * developer information](https://www.chromium.org/chromium-os/developer-
   * information-for-chrome-os-devices/samsung-series-5-chromebook#TOC-
   * Developer-switch).
   *
   * @param string $bootMode
   */
  public function setBootMode($bootMode)
  {
    $this->bootMode = $bootMode;
  }
  /**
   * @return string
   */
  public function getBootMode()
  {
    return $this->bootMode;
  }
  /**
   * Output only. Chrome OS type of the device.
   *
   * Accepted values: chromeOsTypeUnspecified, chromeOsFlex, chromeOs
   *
   * @param self::CHROME_OS_TYPE_* $chromeOsType
   */
  public function setChromeOsType($chromeOsType)
  {
    $this->chromeOsType = $chromeOsType;
  }
  /**
   * @return self::CHROME_OS_TYPE_*
   */
  public function getChromeOsType()
  {
    return $this->chromeOsType;
  }
  /**
   * Information regarding CPU specs in the device.
   *
   * @param ChromeOsDeviceCpuInfo[] $cpuInfo
   */
  public function setCpuInfo($cpuInfo)
  {
    $this->cpuInfo = $cpuInfo;
  }
  /**
   * @return ChromeOsDeviceCpuInfo[]
   */
  public function getCpuInfo()
  {
    return $this->cpuInfo;
  }
  /**
   * Reports of CPU utilization and temperature (Read-only)
   *
   * @param ChromeOsDeviceCpuStatusReports[] $cpuStatusReports
   */
  public function setCpuStatusReports($cpuStatusReports)
  {
    $this->cpuStatusReports = $cpuStatusReports;
  }
  /**
   * @return ChromeOsDeviceCpuStatusReports[]
   */
  public function getCpuStatusReports()
  {
    return $this->cpuStatusReports;
  }
  /**
   * (Read-only) Deprovision reason.
   *
   * Accepted values: DEPROVISION_REASON_UNSPECIFIED,
   * DEPROVISION_REASON_SAME_MODEL_REPLACEMENT, DEPROVISION_REASON_UPGRADE,
   * DEPROVISION_REASON_DOMAIN_MOVE, DEPROVISION_REASON_SERVICE_EXPIRATION,
   * DEPROVISION_REASON_OTHER, DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT,
   * DEPROVISION_REASON_RETIRING_DEVICE, DEPROVISION_REASON_UPGRADE_TRANSFER,
   * DEPROVISION_REASON_NOT_REQUIRED, DEPROVISION_REASON_REPAIR_CENTER
   *
   * @param self::DEPROVISION_REASON_* $deprovisionReason
   */
  public function setDeprovisionReason($deprovisionReason)
  {
    $this->deprovisionReason = $deprovisionReason;
  }
  /**
   * @return self::DEPROVISION_REASON_*
   */
  public function getDeprovisionReason()
  {
    return $this->deprovisionReason;
  }
  /**
   * A list of device files to download (Read-only)
   *
   * @param ChromeOsDeviceDeviceFiles[] $deviceFiles
   */
  public function setDeviceFiles($deviceFiles)
  {
    $this->deviceFiles = $deviceFiles;
  }
  /**
   * @return ChromeOsDeviceDeviceFiles[]
   */
  public function getDeviceFiles()
  {
    return $this->deviceFiles;
  }
  /**
   * The unique ID of the Chrome device.
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
   * Output only. Device license type.
   *
   * Accepted values: deviceLicenseTypeUnspecified, enterprise,
   * enterpriseUpgrade, educationUpgrade, education, kioskUpgrade,
   * enterpriseUpgradePerpetual, enterpriseUpgradeFixedTerm,
   * educationUpgradePerpetual, educationUpgradeFixedTerm
   *
   * @param self::DEVICE_LICENSE_TYPE_* $deviceLicenseType
   */
  public function setDeviceLicenseType($deviceLicenseType)
  {
    $this->deviceLicenseType = $deviceLicenseType;
  }
  /**
   * @return self::DEVICE_LICENSE_TYPE_*
   */
  public function getDeviceLicenseType()
  {
    return $this->deviceLicenseType;
  }
  /**
   * Output only. How much disk space the device has available and is currently
   * using.
   *
   * @param ByteUsage $diskSpaceUsage
   */
  public function setDiskSpaceUsage(ByteUsage $diskSpaceUsage)
  {
    $this->diskSpaceUsage = $diskSpaceUsage;
  }
  /**
   * @return ByteUsage
   */
  public function getDiskSpaceUsage()
  {
    return $this->diskSpaceUsage;
  }
  /**
   * Reports of disk space and other info about mounted/connected volumes.
   *
   * @param ChromeOsDeviceDiskVolumeReports[] $diskVolumeReports
   */
  public function setDiskVolumeReports($diskVolumeReports)
  {
    $this->diskVolumeReports = $diskVolumeReports;
  }
  /**
   * @return ChromeOsDeviceDiskVolumeReports[]
   */
  public function getDiskVolumeReports()
  {
    return $this->diskVolumeReports;
  }
  /**
   * (Read-only) Built-in MAC address for the docking station that the device
   * connected to. Factory sets Media access control address (MAC address)
   * assigned for use by a dock. It is reserved specifically for MAC pass
   * through device policy. The format is twelve (12) hexadecimal digits without
   * any delimiter (uppercase letters). This is only relevant for some devices.
   *
   * @param string $dockMacAddress
   */
  public function setDockMacAddress($dockMacAddress)
  {
    $this->dockMacAddress = $dockMacAddress;
  }
  /**
   * @return string
   */
  public function getDockMacAddress()
  {
    return $this->dockMacAddress;
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
   * The device's MAC address on the ethernet network interface.
   *
   * @param string $ethernetMacAddress
   */
  public function setEthernetMacAddress($ethernetMacAddress)
  {
    $this->ethernetMacAddress = $ethernetMacAddress;
  }
  /**
   * @return string
   */
  public function getEthernetMacAddress()
  {
    return $this->ethernetMacAddress;
  }
  /**
   * (Read-only) MAC address used by the Chromebook’s internal ethernet port,
   * and for onboard network (ethernet) interface. The format is twelve (12)
   * hexadecimal digits without any delimiter (uppercase letters). This is only
   * relevant for some devices.
   *
   * @param string $ethernetMacAddress0
   */
  public function setEthernetMacAddress0($ethernetMacAddress0)
  {
    $this->ethernetMacAddress0 = $ethernetMacAddress0;
  }
  /**
   * @return string
   */
  public function getEthernetMacAddress0()
  {
    return $this->ethernetMacAddress0;
  }
  /**
   * Output only. Whether or not the device requires the extended support opt
   * in.
   *
   * @param bool $extendedSupportEligible
   */
  public function setExtendedSupportEligible($extendedSupportEligible)
  {
    $this->extendedSupportEligible = $extendedSupportEligible;
  }
  /**
   * @return bool
   */
  public function getExtendedSupportEligible()
  {
    return $this->extendedSupportEligible;
  }
  /**
   * Output only. Whether extended support policy is enabled on the device.
   *
   * @param bool $extendedSupportEnabled
   */
  public function setExtendedSupportEnabled($extendedSupportEnabled)
  {
    $this->extendedSupportEnabled = $extendedSupportEnabled;
  }
  /**
   * @return bool
   */
  public function getExtendedSupportEnabled()
  {
    return $this->extendedSupportEnabled;
  }
  /**
   * Output only. Date of the device when extended support policy for automatic
   * updates starts.
   *
   * @param string $extendedSupportStart
   */
  public function setExtendedSupportStart($extendedSupportStart)
  {
    $this->extendedSupportStart = $extendedSupportStart;
  }
  /**
   * @return string
   */
  public function getExtendedSupportStart()
  {
    return $this->extendedSupportStart;
  }
  /**
   * Output only. Fan information for the device.
   *
   * @param FanInfo[] $fanInfo
   */
  public function setFanInfo($fanInfo)
  {
    $this->fanInfo = $fanInfo;
  }
  /**
   * @return FanInfo[]
   */
  public function getFanInfo()
  {
    return $this->fanInfo;
  }
  /**
   * The Chrome device's firmware version.
   *
   * @param string $firmwareVersion
   */
  public function setFirmwareVersion($firmwareVersion)
  {
    $this->firmwareVersion = $firmwareVersion;
  }
  /**
   * @return string
   */
  public function getFirmwareVersion()
  {
    return $this->firmwareVersion;
  }
  /**
   * Date and time for the first time the device was enrolled.
   *
   * @param string $firstEnrollmentTime
   */
  public function setFirstEnrollmentTime($firstEnrollmentTime)
  {
    $this->firstEnrollmentTime = $firstEnrollmentTime;
  }
  /**
   * @return string
   */
  public function getFirstEnrollmentTime()
  {
    return $this->firstEnrollmentTime;
  }
  /**
   * The type of resource. For the Chromeosdevices resource, the value is
   * `admin#directory#chromeosdevice`.
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
   * (Read-only) Date and time for the last deprovision of the device.
   *
   * @param string $lastDeprovisionTimestamp
   */
  public function setLastDeprovisionTimestamp($lastDeprovisionTimestamp)
  {
    $this->lastDeprovisionTimestamp = $lastDeprovisionTimestamp;
  }
  /**
   * @return string
   */
  public function getLastDeprovisionTimestamp()
  {
    return $this->lastDeprovisionTimestamp;
  }
  /**
   * Date and time the device was last enrolled (Read-only)
   *
   * @param string $lastEnrollmentTime
   */
  public function setLastEnrollmentTime($lastEnrollmentTime)
  {
    $this->lastEnrollmentTime = $lastEnrollmentTime;
  }
  /**
   * @return string
   */
  public function getLastEnrollmentTime()
  {
    return $this->lastEnrollmentTime;
  }
  /**
   * Contains last known network (Read-only)
   *
   * @param ChromeOsDeviceLastKnownNetwork[] $lastKnownNetwork
   */
  public function setLastKnownNetwork($lastKnownNetwork)
  {
    $this->lastKnownNetwork = $lastKnownNetwork;
  }
  /**
   * @return ChromeOsDeviceLastKnownNetwork[]
   */
  public function getLastKnownNetwork()
  {
    return $this->lastKnownNetwork;
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
   * The device's wireless MAC address. If the device does not have this
   * information, it is not included in the response.
   *
   * @param string $macAddress
   */
  public function setMacAddress($macAddress)
  {
    $this->macAddress = $macAddress;
  }
  /**
   * @return string
   */
  public function getMacAddress()
  {
    return $this->macAddress;
  }
  /**
   * (Read-only) The date the device was manufactured in yyyy-mm-dd format.
   *
   * @param string $manufactureDate
   */
  public function setManufactureDate($manufactureDate)
  {
    $this->manufactureDate = $manufactureDate;
  }
  /**
   * @return string
   */
  public function getManufactureDate()
  {
    return $this->manufactureDate;
  }
  /**
   * The Mobile Equipment Identifier (MEID) or the International Mobile
   * Equipment Identity (IMEI) for the 3G mobile card in a mobile device. A
   * MEID/IMEI is typically used when adding a device to a wireless carrier's
   * post-pay service plan. If the device does not have this information, this
   * property is not included in the response. For more information on how to
   * export a MEID/IMEI list, see the [Developer's Guide](https://developers.goo
   * gle.com/workspace/admin/directory/v1/guides/manage-chrome-
   * devices.html#export_meid).
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
   * The device's model information. If the device does not have this
   * information, this property is not included in the response.
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
   * Notes about this device added by the administrator. This property can be
   * [searched](https://support.google.com/chrome/a/answer/1698333) with the [li
   * st](https://developers.google.com/workspace/admin/directory/v1/reference/ch
   * romeosdevices/list) method's `query` parameter. Maximum length is 500
   * characters. Empty values are allowed.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * The device's order number. Only devices directly purchased from Google have
   * an order number.
   *
   * @param string $orderNumber
   */
  public function setOrderNumber($orderNumber)
  {
    $this->orderNumber = $orderNumber;
  }
  /**
   * @return string
   */
  public function getOrderNumber()
  {
    return $this->orderNumber;
  }
  /**
   * The unique ID of the organizational unit. orgUnitPath is the human readable
   * version of orgUnitId. While orgUnitPath may change by renaming an
   * organizational unit within the path, orgUnitId is unchangeable for one
   * organizational unit. This property can be [updated](https://developers.goog
   * le.com/workspace/admin/directory/v1/guides/manage-chrome-
   * devices#move_chrome_devices_to_ou) using the API. For more information
   * about how to create an organizational structure for your device, see the
   * [administration help center](https://support.google.com/a/answer/182433).
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * The full parent path with the organizational unit's name associated with
   * the device. Path names are case insensitive. If the parent organizational
   * unit is the top-level organization, it is represented as a forward slash,
   * `/`. This property can be [updated](https://developers.google.com/workspace
   * /admin/directory/v1/guides/manage-chrome-devices#move_chrome_devices_to_ou)
   * using the API. For more information about how to create an organizational
   * structure for your device, see the [administration help
   * center](https://support.google.com/a/answer/182433).
   *
   * @param string $orgUnitPath
   */
  public function setOrgUnitPath($orgUnitPath)
  {
    $this->orgUnitPath = $orgUnitPath;
  }
  /**
   * @return string
   */
  public function getOrgUnitPath()
  {
    return $this->orgUnitPath;
  }
  /**
   * The status of the OS updates for the device.
   *
   * @param OsUpdateStatus $osUpdateStatus
   */
  public function setOsUpdateStatus(OsUpdateStatus $osUpdateStatus)
  {
    $this->osUpdateStatus = $osUpdateStatus;
  }
  /**
   * @return OsUpdateStatus
   */
  public function getOsUpdateStatus()
  {
    return $this->osUpdateStatus;
  }
  /**
   * The Chrome device's operating system version.
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
   * Output only. Device policy compliance status of the OS version.
   *
   * Accepted values: complianceUnspecified, compliant, pending, notCompliant
   *
   * @param self::OS_VERSION_COMPLIANCE_* $osVersionCompliance
   */
  public function setOsVersionCompliance($osVersionCompliance)
  {
    $this->osVersionCompliance = $osVersionCompliance;
  }
  /**
   * @return self::OS_VERSION_COMPLIANCE_*
   */
  public function getOsVersionCompliance()
  {
    return $this->osVersionCompliance;
  }
  /**
   * The Chrome device's platform version.
   *
   * @param string $platformVersion
   */
  public function setPlatformVersion($platformVersion)
  {
    $this->platformVersion = $platformVersion;
  }
  /**
   * @return string
   */
  public function getPlatformVersion()
  {
    return $this->platformVersion;
  }
  /**
   * A list of recent device users, in descending order, by last login time.
   *
   * @param ChromeOsDeviceRecentUsers[] $recentUsers
   */
  public function setRecentUsers($recentUsers)
  {
    $this->recentUsers = $recentUsers;
  }
  /**
   * @return ChromeOsDeviceRecentUsers[]
   */
  public function getRecentUsers()
  {
    return $this->recentUsers;
  }
  /**
   * A list of screenshot files to download. Type is always "SCREENSHOT_FILE".
   * (Read-only)
   *
   * @param ChromeOsDeviceScreenshotFiles[] $screenshotFiles
   */
  public function setScreenshotFiles($screenshotFiles)
  {
    $this->screenshotFiles = $screenshotFiles;
  }
  /**
   * @return ChromeOsDeviceScreenshotFiles[]
   */
  public function getScreenshotFiles()
  {
    return $this->screenshotFiles;
  }
  /**
   * The Chrome device serial number entered when the device was enabled. This
   * value is the same as the Admin console's *Serial Number* in the *Chrome OS
   * Devices* tab.
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
   * The status of the device.
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
   * Final date the device will be supported (Read-only)
   *
   * @param string $supportEndDate
   */
  public function setSupportEndDate($supportEndDate)
  {
    $this->supportEndDate = $supportEndDate;
  }
  /**
   * @return string
   */
  public function getSupportEndDate()
  {
    return $this->supportEndDate;
  }
  /**
   * Reports of amounts of available RAM memory (Read-only)
   *
   * @param ChromeOsDeviceSystemRamFreeReports[] $systemRamFreeReports
   */
  public function setSystemRamFreeReports($systemRamFreeReports)
  {
    $this->systemRamFreeReports = $systemRamFreeReports;
  }
  /**
   * @return ChromeOsDeviceSystemRamFreeReports[]
   */
  public function getSystemRamFreeReports()
  {
    return $this->systemRamFreeReports;
  }
  /**
   * Total RAM on the device [in bytes] (Read-only)
   *
   * @param string $systemRamTotal
   */
  public function setSystemRamTotal($systemRamTotal)
  {
    $this->systemRamTotal = $systemRamTotal;
  }
  /**
   * @return string
   */
  public function getSystemRamTotal()
  {
    return $this->systemRamTotal;
  }
  /**
   * Trusted Platform Module (TPM) (Read-only)
   *
   * @param ChromeOsDeviceTpmVersionInfo $tpmVersionInfo
   */
  public function setTpmVersionInfo(ChromeOsDeviceTpmVersionInfo $tpmVersionInfo)
  {
    $this->tpmVersionInfo = $tpmVersionInfo;
  }
  /**
   * @return ChromeOsDeviceTpmVersionInfo
   */
  public function getTpmVersionInfo()
  {
    return $this->tpmVersionInfo;
  }
  /**
   * Determines if the device will auto renew its support after the support end
   * date. This is a read-only property.
   *
   * @param bool $willAutoRenew
   */
  public function setWillAutoRenew($willAutoRenew)
  {
    $this->willAutoRenew = $willAutoRenew;
  }
  /**
   * @return bool
   */
  public function getWillAutoRenew()
  {
    return $this->willAutoRenew;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDevice::class, 'Google_Service_Directory_ChromeOsDevice');
