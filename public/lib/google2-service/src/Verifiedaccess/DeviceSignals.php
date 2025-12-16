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

namespace Google\Service\Verifiedaccess;

class DeviceSignals extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const DISK_ENCRYPTION_DISK_ENCRYPTION_UNSPECIFIED = 'DISK_ENCRYPTION_UNSPECIFIED';
  /**
   * Chrome could not evaluate the encryption state.
   */
  public const DISK_ENCRYPTION_DISK_ENCRYPTION_UNKNOWN = 'DISK_ENCRYPTION_UNKNOWN';
  /**
   * The main disk is not encrypted.
   */
  public const DISK_ENCRYPTION_DISK_ENCRYPTION_DISABLED = 'DISK_ENCRYPTION_DISABLED';
  /**
   * The main disk is encrypted.
   */
  public const DISK_ENCRYPTION_DISK_ENCRYPTION_ENCRYPTED = 'DISK_ENCRYPTION_ENCRYPTED';
  /**
   * UNSPECIFIED.
   */
  public const OPERATING_SYSTEM_OPERATING_SYSTEM_UNSPECIFIED = 'OPERATING_SYSTEM_UNSPECIFIED';
  /**
   * ChromeOS.
   */
  public const OPERATING_SYSTEM_CHROME_OS = 'CHROME_OS';
  /**
   * ChromiumOS.
   */
  public const OPERATING_SYSTEM_CHROMIUM_OS = 'CHROMIUM_OS';
  /**
   * Windows.
   */
  public const OPERATING_SYSTEM_WINDOWS = 'WINDOWS';
  /**
   * Mac Os X.
   */
  public const OPERATING_SYSTEM_MAC_OS_X = 'MAC_OS_X';
  /**
   * Linux
   */
  public const OPERATING_SYSTEM_LINUX = 'LINUX';
  /**
   * Unspecified.
   */
  public const OS_FIREWALL_OS_FIREWALL_UNSPECIFIED = 'OS_FIREWALL_UNSPECIFIED';
  /**
   * Chrome could not evaluate the OS firewall state.
   */
  public const OS_FIREWALL_OS_FIREWALL_UNKNOWN = 'OS_FIREWALL_UNKNOWN';
  /**
   * The OS firewall is disabled.
   */
  public const OS_FIREWALL_OS_FIREWALL_DISABLED = 'OS_FIREWALL_DISABLED';
  /**
   * The OS firewall is enabled.
   */
  public const OS_FIREWALL_OS_FIREWALL_ENABLED = 'OS_FIREWALL_ENABLED';
  /**
   * Unspecified.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PASSWORD_PROTECTION_WARNING_TRIGGER_UNSPECIFIED = 'PASSWORD_PROTECTION_WARNING_TRIGGER_UNSPECIFIED';
  /**
   * The policy is not set.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_POLICY_UNSET = 'POLICY_UNSET';
  /**
   * No password protection warning will be shown.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PASSWORD_PROTECTION_OFF = 'PASSWORD_PROTECTION_OFF';
  /**
   * Password protection warning is shown if a protected password is re-used.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PASSWORD_REUSE = 'PASSWORD_REUSE';
  /**
   * Password protection warning is shown if a protected password is re-used on
   * a known phishing website.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PHISHING_REUSE = 'PHISHING_REUSE';
  /**
   * Unspecified.
   */
  public const REALTIME_URL_CHECK_MODE_REALTIME_URL_CHECK_MODE_UNSPECIFIED = 'REALTIME_URL_CHECK_MODE_UNSPECIFIED';
  /**
   * Disabled. Consumer Safe Browsing checks are applied.
   */
  public const REALTIME_URL_CHECK_MODE_REALTIME_URL_CHECK_MODE_DISABLED = 'REALTIME_URL_CHECK_MODE_DISABLED';
  /**
   * Realtime check for main frame URLs is enabled.
   */
  public const REALTIME_URL_CHECK_MODE_REALTIME_URL_CHECK_MODE_ENABLED_MAIN_FRAME = 'REALTIME_URL_CHECK_MODE_ENABLED_MAIN_FRAME';
  /**
   * Unspecified.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_SAFE_BROWSING_PROTECTION_LEVEL_UNSPECIFIED = 'SAFE_BROWSING_PROTECTION_LEVEL_UNSPECIFIED';
  /**
   * Safe Browsing is disabled.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_INACTIVE = 'INACTIVE';
  /**
   * Safe Browsing is active in the standard mode.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_STANDARD = 'STANDARD';
  /**
   * Safe Browsing is active in the enhanced mode.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_ENHANCED = 'ENHANCED';
  /**
   * Unspecified.
   */
  public const SCREEN_LOCK_SECURED_SCREEN_LOCK_SECURED_UNSPECIFIED = 'SCREEN_LOCK_SECURED_UNSPECIFIED';
  /**
   * Chrome could not evaluate the state of the Screen Lock mechanism.
   */
  public const SCREEN_LOCK_SECURED_SCREEN_LOCK_SECURED_UNKNOWN = 'SCREEN_LOCK_SECURED_UNKNOWN';
  /**
   * The Screen Lock is not password-protected.
   */
  public const SCREEN_LOCK_SECURED_SCREEN_LOCK_SECURED_DISABLED = 'SCREEN_LOCK_SECURED_DISABLED';
  /**
   * The Screen Lock is password-protected.
   */
  public const SCREEN_LOCK_SECURED_SCREEN_LOCK_SECURED_ENABLED = 'SCREEN_LOCK_SECURED_ENABLED';
  /**
   * Unspecified.
   */
  public const SECURE_BOOT_MODE_SECURE_BOOT_MODE_UNSPECIFIED = 'SECURE_BOOT_MODE_UNSPECIFIED';
  /**
   * Chrome was unable to determine the Secure Boot mode.
   */
  public const SECURE_BOOT_MODE_SECURE_BOOT_MODE_UNKNOWN = 'SECURE_BOOT_MODE_UNKNOWN';
  /**
   * Secure Boot was disabled on the startup software.
   */
  public const SECURE_BOOT_MODE_SECURE_BOOT_MODE_DISABLED = 'SECURE_BOOT_MODE_DISABLED';
  /**
   * Secure Boot was enabled on the startup software.
   */
  public const SECURE_BOOT_MODE_SECURE_BOOT_MODE_ENABLED = 'SECURE_BOOT_MODE_ENABLED';
  /**
   * Unspecified.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * When navigating to an URL inside a browser.
   */
  public const TRIGGER_TRIGGER_BROWSER_NAVIGATION = 'TRIGGER_BROWSER_NAVIGATION';
  /**
   * When signing into an account on the ChromeOS login screen.
   */
  public const TRIGGER_TRIGGER_LOGIN_SCREEN = 'TRIGGER_LOGIN_SCREEN';
  protected $collection_key = 'systemDnsServers';
  /**
   * Output only. Value of the AllowScreenLock policy on the device. See
   * https://chromeenterprise.google/policies/?policy=AllowScreenLock for more
   * details. Available on ChromeOS only.
   *
   * @var bool
   */
  public $allowScreenLock;
  protected $antivirusType = Antivirus::class;
  protected $antivirusDataType = '';
  /**
   * Output only. Current version of the Chrome browser which generated this set
   * of signals. Example value: "107.0.5286.0".
   *
   * @var string
   */
  public $browserVersion;
  /**
   * Output only. Whether Chrome's built-in DNS client is used. The OS DNS
   * client is otherwise used. This value may be controlled by an enterprise
   * policy: https://chromeenterprise.google/policies/#BuiltInDnsClientEnabled.
   *
   * @var bool
   */
  public $builtInDnsClientEnabled;
  /**
   * Output only. Whether access to the Chrome Remote Desktop application is
   * blocked via a policy.
   *
   * @var bool
   */
  public $chromeRemoteDesktopAppBlocked;
  protected $crowdStrikeAgentType = CrowdStrikeAgent::class;
  protected $crowdStrikeAgentDataType = '';
  /**
   * Output only. Affiliation IDs of the organizations that are affiliated with
   * the organization that is currently managing the device. When the sets of
   * device and profile affiliation IDs overlap, it means that the organizations
   * managing the device and user are affiliated. To learn more about user
   * affiliation, visit
   * https://support.google.com/chrome/a/answer/12801245?ref_topic=9027936.
   *
   * @var string[]
   */
  public $deviceAffiliationIds;
  /**
   * Output only. Enrollment domain of the customer which is currently managing
   * the device.
   *
   * @var string
   */
  public $deviceEnrollmentDomain;
  /**
   * Output only. The name of the device's manufacturer.
   *
   * @var string
   */
  public $deviceManufacturer;
  /**
   * Output only. The name of the device's model.
   *
   * @var string
   */
  public $deviceModel;
  /**
   * Output only. The encryption state of the disk. On ChromeOS, the main disk
   * is always ENCRYPTED.
   *
   * @var string
   */
  public $diskEncryption;
  /**
   * Output only. The display name of the device, as defined by the user.
   *
   * @var string
   */
  public $displayName;
  /**
   * Hostname of the device.
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. International Mobile Equipment Identity (IMEI) of the device.
   * Available on ChromeOS only.
   *
   * @var string[]
   */
  public $imei;
  /**
   * Output only. MAC addresses of the device.
   *
   * @var string[]
   */
  public $macAddresses;
  /**
   * Output only. Mobile Equipment Identifier (MEID) of the device. Available on
   * ChromeOS only.
   *
   * @var string[]
   */
  public $meid;
  /**
   * Output only. The type of the Operating System currently running on the
   * device.
   *
   * @var string
   */
  public $operatingSystem;
  /**
   * Output only. The state of the OS level firewall. On ChromeOS, the value
   * will always be ENABLED on regular devices and UNKNOWN on devices in
   * developer mode. Support for MacOS 15 (Sequoia) and later has been
   * introduced in Chrome M131.
   *
   * @var string
   */
  public $osFirewall;
  /**
   * Output only. The current version of the Operating System. On Windows and
   * linux, the value will also include the security patch information.
   *
   * @var string
   */
  public $osVersion;
  /**
   * Output only. Whether the Password Protection Warning feature is enabled or
   * not. Password protection alerts users when they reuse their protected
   * password on potentially suspicious sites. This setting is controlled by an
   * enterprise policy:
   * https://chromeenterprise.google/policies/#PasswordProtectionWarningTrigger.
   * Note that the policy unset does not have the same effects as having the
   * policy explicitly set to `PASSWORD_PROTECTION_OFF`.
   *
   * @var string
   */
  public $passwordProtectionWarningTrigger;
  /**
   * Output only. Affiliation IDs of the organizations that are affiliated with
   * the organization that is currently managing the Chrome Profile’s user or
   * ChromeOS user.
   *
   * @var string[]
   */
  public $profileAffiliationIds;
  /**
   * Output only. Enrollment domain of the customer which is currently managing
   * the profile.
   *
   * @var string
   */
  public $profileEnrollmentDomain;
  /**
   * Output only. Whether Enterprise-grade (i.e. custom) unsafe URL scanning is
   * enabled or not. This setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/#EnterpriseRealTimeUrlCheckMode
   *
   * @var string
   */
  public $realtimeUrlCheckMode;
  /**
   * Output only. Safe Browsing Protection Level. That setting may be controlled
   * by an enterprise policy:
   * https://chromeenterprise.google/policies/#SafeBrowsingProtectionLevel.
   *
   * @var string
   */
  public $safeBrowsingProtectionLevel;
  /**
   * Output only. The state of the Screen Lock password protection. On ChromeOS,
   * this value will always be ENABLED as there is not way to disable requiring
   * a password or pin when unlocking the device.
   *
   * @var string
   */
  public $screenLockSecured;
  /**
   * Output only. Whether the device's startup software has its Secure Boot
   * feature enabled. Available on Windows only.
   *
   * @var string
   */
  public $secureBootMode;
  /**
   * Output only. The serial number of the device. On Windows, this represents
   * the BIOS's serial number. Not available on most Linux distributions.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. Whether the Site Isolation (a.k.a Site Per Process) setting is
   * enabled. That setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/#SitePerProcess
   *
   * @var bool
   */
  public $siteIsolationEnabled;
  /**
   * List of the addesses of all OS level DNS servers configured in the device's
   * network settings.
   *
   * @var string[]
   */
  public $systemDnsServers;
  /**
   * Output only. Deprecated. The corresponding policy is now deprecated.
   * Whether Chrome is blocking third-party software injection or not. This
   * setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/?policy=ThirdPartyBlockingEnabled.
   * Available on Windows only.
   *
   * @deprecated
   * @var bool
   */
  public $thirdPartyBlockingEnabled;
  /**
   * Output only. The trigger which generated this set of signals.
   *
   * @var string
   */
  public $trigger;
  /**
   * Output only. Windows domain that the current machine has joined. Available
   * on Windows only.
   *
   * @var string
   */
  public $windowsMachineDomain;
  /**
   * Output only. Windows domain for the current OS user. Available on Windows
   * only.
   *
   * @var string
   */
  public $windowsUserDomain;

  /**
   * Output only. Value of the AllowScreenLock policy on the device. See
   * https://chromeenterprise.google/policies/?policy=AllowScreenLock for more
   * details. Available on ChromeOS only.
   *
   * @param bool $allowScreenLock
   */
  public function setAllowScreenLock($allowScreenLock)
  {
    $this->allowScreenLock = $allowScreenLock;
  }
  /**
   * @return bool
   */
  public function getAllowScreenLock()
  {
    return $this->allowScreenLock;
  }
  /**
   * Output only. Information about Antivirus software on the device. Available
   * on Windows only.
   *
   * @param Antivirus $antivirus
   */
  public function setAntivirus(Antivirus $antivirus)
  {
    $this->antivirus = $antivirus;
  }
  /**
   * @return Antivirus
   */
  public function getAntivirus()
  {
    return $this->antivirus;
  }
  /**
   * Output only. Current version of the Chrome browser which generated this set
   * of signals. Example value: "107.0.5286.0".
   *
   * @param string $browserVersion
   */
  public function setBrowserVersion($browserVersion)
  {
    $this->browserVersion = $browserVersion;
  }
  /**
   * @return string
   */
  public function getBrowserVersion()
  {
    return $this->browserVersion;
  }
  /**
   * Output only. Whether Chrome's built-in DNS client is used. The OS DNS
   * client is otherwise used. This value may be controlled by an enterprise
   * policy: https://chromeenterprise.google/policies/#BuiltInDnsClientEnabled.
   *
   * @param bool $builtInDnsClientEnabled
   */
  public function setBuiltInDnsClientEnabled($builtInDnsClientEnabled)
  {
    $this->builtInDnsClientEnabled = $builtInDnsClientEnabled;
  }
  /**
   * @return bool
   */
  public function getBuiltInDnsClientEnabled()
  {
    return $this->builtInDnsClientEnabled;
  }
  /**
   * Output only. Whether access to the Chrome Remote Desktop application is
   * blocked via a policy.
   *
   * @param bool $chromeRemoteDesktopAppBlocked
   */
  public function setChromeRemoteDesktopAppBlocked($chromeRemoteDesktopAppBlocked)
  {
    $this->chromeRemoteDesktopAppBlocked = $chromeRemoteDesktopAppBlocked;
  }
  /**
   * @return bool
   */
  public function getChromeRemoteDesktopAppBlocked()
  {
    return $this->chromeRemoteDesktopAppBlocked;
  }
  /**
   * Output only. Crowdstrike agent properties installed on the device, if any.
   * Available on Windows and MacOS only.
   *
   * @param CrowdStrikeAgent $crowdStrikeAgent
   */
  public function setCrowdStrikeAgent(CrowdStrikeAgent $crowdStrikeAgent)
  {
    $this->crowdStrikeAgent = $crowdStrikeAgent;
  }
  /**
   * @return CrowdStrikeAgent
   */
  public function getCrowdStrikeAgent()
  {
    return $this->crowdStrikeAgent;
  }
  /**
   * Output only. Affiliation IDs of the organizations that are affiliated with
   * the organization that is currently managing the device. When the sets of
   * device and profile affiliation IDs overlap, it means that the organizations
   * managing the device and user are affiliated. To learn more about user
   * affiliation, visit
   * https://support.google.com/chrome/a/answer/12801245?ref_topic=9027936.
   *
   * @param string[] $deviceAffiliationIds
   */
  public function setDeviceAffiliationIds($deviceAffiliationIds)
  {
    $this->deviceAffiliationIds = $deviceAffiliationIds;
  }
  /**
   * @return string[]
   */
  public function getDeviceAffiliationIds()
  {
    return $this->deviceAffiliationIds;
  }
  /**
   * Output only. Enrollment domain of the customer which is currently managing
   * the device.
   *
   * @param string $deviceEnrollmentDomain
   */
  public function setDeviceEnrollmentDomain($deviceEnrollmentDomain)
  {
    $this->deviceEnrollmentDomain = $deviceEnrollmentDomain;
  }
  /**
   * @return string
   */
  public function getDeviceEnrollmentDomain()
  {
    return $this->deviceEnrollmentDomain;
  }
  /**
   * Output only. The name of the device's manufacturer.
   *
   * @param string $deviceManufacturer
   */
  public function setDeviceManufacturer($deviceManufacturer)
  {
    $this->deviceManufacturer = $deviceManufacturer;
  }
  /**
   * @return string
   */
  public function getDeviceManufacturer()
  {
    return $this->deviceManufacturer;
  }
  /**
   * Output only. The name of the device's model.
   *
   * @param string $deviceModel
   */
  public function setDeviceModel($deviceModel)
  {
    $this->deviceModel = $deviceModel;
  }
  /**
   * @return string
   */
  public function getDeviceModel()
  {
    return $this->deviceModel;
  }
  /**
   * Output only. The encryption state of the disk. On ChromeOS, the main disk
   * is always ENCRYPTED.
   *
   * Accepted values: DISK_ENCRYPTION_UNSPECIFIED, DISK_ENCRYPTION_UNKNOWN,
   * DISK_ENCRYPTION_DISABLED, DISK_ENCRYPTION_ENCRYPTED
   *
   * @param self::DISK_ENCRYPTION_* $diskEncryption
   */
  public function setDiskEncryption($diskEncryption)
  {
    $this->diskEncryption = $diskEncryption;
  }
  /**
   * @return self::DISK_ENCRYPTION_*
   */
  public function getDiskEncryption()
  {
    return $this->diskEncryption;
  }
  /**
   * Output only. The display name of the device, as defined by the user.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Hostname of the device.
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
   * Output only. International Mobile Equipment Identity (IMEI) of the device.
   * Available on ChromeOS only.
   *
   * @param string[] $imei
   */
  public function setImei($imei)
  {
    $this->imei = $imei;
  }
  /**
   * @return string[]
   */
  public function getImei()
  {
    return $this->imei;
  }
  /**
   * Output only. MAC addresses of the device.
   *
   * @param string[] $macAddresses
   */
  public function setMacAddresses($macAddresses)
  {
    $this->macAddresses = $macAddresses;
  }
  /**
   * @return string[]
   */
  public function getMacAddresses()
  {
    return $this->macAddresses;
  }
  /**
   * Output only. Mobile Equipment Identifier (MEID) of the device. Available on
   * ChromeOS only.
   *
   * @param string[] $meid
   */
  public function setMeid($meid)
  {
    $this->meid = $meid;
  }
  /**
   * @return string[]
   */
  public function getMeid()
  {
    return $this->meid;
  }
  /**
   * Output only. The type of the Operating System currently running on the
   * device.
   *
   * Accepted values: OPERATING_SYSTEM_UNSPECIFIED, CHROME_OS, CHROMIUM_OS,
   * WINDOWS, MAC_OS_X, LINUX
   *
   * @param self::OPERATING_SYSTEM_* $operatingSystem
   */
  public function setOperatingSystem($operatingSystem)
  {
    $this->operatingSystem = $operatingSystem;
  }
  /**
   * @return self::OPERATING_SYSTEM_*
   */
  public function getOperatingSystem()
  {
    return $this->operatingSystem;
  }
  /**
   * Output only. The state of the OS level firewall. On ChromeOS, the value
   * will always be ENABLED on regular devices and UNKNOWN on devices in
   * developer mode. Support for MacOS 15 (Sequoia) and later has been
   * introduced in Chrome M131.
   *
   * Accepted values: OS_FIREWALL_UNSPECIFIED, OS_FIREWALL_UNKNOWN,
   * OS_FIREWALL_DISABLED, OS_FIREWALL_ENABLED
   *
   * @param self::OS_FIREWALL_* $osFirewall
   */
  public function setOsFirewall($osFirewall)
  {
    $this->osFirewall = $osFirewall;
  }
  /**
   * @return self::OS_FIREWALL_*
   */
  public function getOsFirewall()
  {
    return $this->osFirewall;
  }
  /**
   * Output only. The current version of the Operating System. On Windows and
   * linux, the value will also include the security patch information.
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
   * Output only. Whether the Password Protection Warning feature is enabled or
   * not. Password protection alerts users when they reuse their protected
   * password on potentially suspicious sites. This setting is controlled by an
   * enterprise policy:
   * https://chromeenterprise.google/policies/#PasswordProtectionWarningTrigger.
   * Note that the policy unset does not have the same effects as having the
   * policy explicitly set to `PASSWORD_PROTECTION_OFF`.
   *
   * Accepted values: PASSWORD_PROTECTION_WARNING_TRIGGER_UNSPECIFIED,
   * POLICY_UNSET, PASSWORD_PROTECTION_OFF, PASSWORD_REUSE, PHISHING_REUSE
   *
   * @param self::PASSWORD_PROTECTION_WARNING_TRIGGER_* $passwordProtectionWarningTrigger
   */
  public function setPasswordProtectionWarningTrigger($passwordProtectionWarningTrigger)
  {
    $this->passwordProtectionWarningTrigger = $passwordProtectionWarningTrigger;
  }
  /**
   * @return self::PASSWORD_PROTECTION_WARNING_TRIGGER_*
   */
  public function getPasswordProtectionWarningTrigger()
  {
    return $this->passwordProtectionWarningTrigger;
  }
  /**
   * Output only. Affiliation IDs of the organizations that are affiliated with
   * the organization that is currently managing the Chrome Profile’s user or
   * ChromeOS user.
   *
   * @param string[] $profileAffiliationIds
   */
  public function setProfileAffiliationIds($profileAffiliationIds)
  {
    $this->profileAffiliationIds = $profileAffiliationIds;
  }
  /**
   * @return string[]
   */
  public function getProfileAffiliationIds()
  {
    return $this->profileAffiliationIds;
  }
  /**
   * Output only. Enrollment domain of the customer which is currently managing
   * the profile.
   *
   * @param string $profileEnrollmentDomain
   */
  public function setProfileEnrollmentDomain($profileEnrollmentDomain)
  {
    $this->profileEnrollmentDomain = $profileEnrollmentDomain;
  }
  /**
   * @return string
   */
  public function getProfileEnrollmentDomain()
  {
    return $this->profileEnrollmentDomain;
  }
  /**
   * Output only. Whether Enterprise-grade (i.e. custom) unsafe URL scanning is
   * enabled or not. This setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/#EnterpriseRealTimeUrlCheckMode
   *
   * Accepted values: REALTIME_URL_CHECK_MODE_UNSPECIFIED,
   * REALTIME_URL_CHECK_MODE_DISABLED,
   * REALTIME_URL_CHECK_MODE_ENABLED_MAIN_FRAME
   *
   * @param self::REALTIME_URL_CHECK_MODE_* $realtimeUrlCheckMode
   */
  public function setRealtimeUrlCheckMode($realtimeUrlCheckMode)
  {
    $this->realtimeUrlCheckMode = $realtimeUrlCheckMode;
  }
  /**
   * @return self::REALTIME_URL_CHECK_MODE_*
   */
  public function getRealtimeUrlCheckMode()
  {
    return $this->realtimeUrlCheckMode;
  }
  /**
   * Output only. Safe Browsing Protection Level. That setting may be controlled
   * by an enterprise policy:
   * https://chromeenterprise.google/policies/#SafeBrowsingProtectionLevel.
   *
   * Accepted values: SAFE_BROWSING_PROTECTION_LEVEL_UNSPECIFIED, INACTIVE,
   * STANDARD, ENHANCED
   *
   * @param self::SAFE_BROWSING_PROTECTION_LEVEL_* $safeBrowsingProtectionLevel
   */
  public function setSafeBrowsingProtectionLevel($safeBrowsingProtectionLevel)
  {
    $this->safeBrowsingProtectionLevel = $safeBrowsingProtectionLevel;
  }
  /**
   * @return self::SAFE_BROWSING_PROTECTION_LEVEL_*
   */
  public function getSafeBrowsingProtectionLevel()
  {
    return $this->safeBrowsingProtectionLevel;
  }
  /**
   * Output only. The state of the Screen Lock password protection. On ChromeOS,
   * this value will always be ENABLED as there is not way to disable requiring
   * a password or pin when unlocking the device.
   *
   * Accepted values: SCREEN_LOCK_SECURED_UNSPECIFIED,
   * SCREEN_LOCK_SECURED_UNKNOWN, SCREEN_LOCK_SECURED_DISABLED,
   * SCREEN_LOCK_SECURED_ENABLED
   *
   * @param self::SCREEN_LOCK_SECURED_* $screenLockSecured
   */
  public function setScreenLockSecured($screenLockSecured)
  {
    $this->screenLockSecured = $screenLockSecured;
  }
  /**
   * @return self::SCREEN_LOCK_SECURED_*
   */
  public function getScreenLockSecured()
  {
    return $this->screenLockSecured;
  }
  /**
   * Output only. Whether the device's startup software has its Secure Boot
   * feature enabled. Available on Windows only.
   *
   * Accepted values: SECURE_BOOT_MODE_UNSPECIFIED, SECURE_BOOT_MODE_UNKNOWN,
   * SECURE_BOOT_MODE_DISABLED, SECURE_BOOT_MODE_ENABLED
   *
   * @param self::SECURE_BOOT_MODE_* $secureBootMode
   */
  public function setSecureBootMode($secureBootMode)
  {
    $this->secureBootMode = $secureBootMode;
  }
  /**
   * @return self::SECURE_BOOT_MODE_*
   */
  public function getSecureBootMode()
  {
    return $this->secureBootMode;
  }
  /**
   * Output only. The serial number of the device. On Windows, this represents
   * the BIOS's serial number. Not available on most Linux distributions.
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
   * Output only. Whether the Site Isolation (a.k.a Site Per Process) setting is
   * enabled. That setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/#SitePerProcess
   *
   * @param bool $siteIsolationEnabled
   */
  public function setSiteIsolationEnabled($siteIsolationEnabled)
  {
    $this->siteIsolationEnabled = $siteIsolationEnabled;
  }
  /**
   * @return bool
   */
  public function getSiteIsolationEnabled()
  {
    return $this->siteIsolationEnabled;
  }
  /**
   * List of the addesses of all OS level DNS servers configured in the device's
   * network settings.
   *
   * @param string[] $systemDnsServers
   */
  public function setSystemDnsServers($systemDnsServers)
  {
    $this->systemDnsServers = $systemDnsServers;
  }
  /**
   * @return string[]
   */
  public function getSystemDnsServers()
  {
    return $this->systemDnsServers;
  }
  /**
   * Output only. Deprecated. The corresponding policy is now deprecated.
   * Whether Chrome is blocking third-party software injection or not. This
   * setting may be controlled by an enterprise policy:
   * https://chromeenterprise.google/policies/?policy=ThirdPartyBlockingEnabled.
   * Available on Windows only.
   *
   * @deprecated
   * @param bool $thirdPartyBlockingEnabled
   */
  public function setThirdPartyBlockingEnabled($thirdPartyBlockingEnabled)
  {
    $this->thirdPartyBlockingEnabled = $thirdPartyBlockingEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getThirdPartyBlockingEnabled()
  {
    return $this->thirdPartyBlockingEnabled;
  }
  /**
   * Output only. The trigger which generated this set of signals.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, TRIGGER_BROWSER_NAVIGATION,
   * TRIGGER_LOGIN_SCREEN
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * Output only. Windows domain that the current machine has joined. Available
   * on Windows only.
   *
   * @param string $windowsMachineDomain
   */
  public function setWindowsMachineDomain($windowsMachineDomain)
  {
    $this->windowsMachineDomain = $windowsMachineDomain;
  }
  /**
   * @return string
   */
  public function getWindowsMachineDomain()
  {
    return $this->windowsMachineDomain;
  }
  /**
   * Output only. Windows domain for the current OS user. Available on Windows
   * only.
   *
   * @param string $windowsUserDomain
   */
  public function setWindowsUserDomain($windowsUserDomain)
  {
    $this->windowsUserDomain = $windowsUserDomain;
  }
  /**
   * @return string
   */
  public function getWindowsUserDomain()
  {
    return $this->windowsUserDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceSignals::class, 'Google_Service_Verifiedaccess_DeviceSignals');
