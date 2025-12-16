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

class AdvancedSecurityOverrides extends \Google\Collection
{
  /**
   * Unspecified. Defaults to COMMON_CRITERIA_MODE_DISABLED.
   */
  public const COMMON_CRITERIA_MODE_COMMON_CRITERIA_MODE_UNSPECIFIED = 'COMMON_CRITERIA_MODE_UNSPECIFIED';
  /**
   * Default. Disables Common Criteria Mode.
   */
  public const COMMON_CRITERIA_MODE_COMMON_CRITERIA_MODE_DISABLED = 'COMMON_CRITERIA_MODE_DISABLED';
  /**
   * Enables Common Criteria Mode.
   */
  public const COMMON_CRITERIA_MODE_COMMON_CRITERIA_MODE_ENABLED = 'COMMON_CRITERIA_MODE_ENABLED';
  /**
   * Unspecified. Defaults to CONTENT_PROTECTION_DISABLED.
   */
  public const CONTENT_PROTECTION_POLICY_CONTENT_PROTECTION_POLICY_UNSPECIFIED = 'CONTENT_PROTECTION_POLICY_UNSPECIFIED';
  /**
   * Content protection is disabled and the user cannot change this.
   */
  public const CONTENT_PROTECTION_POLICY_CONTENT_PROTECTION_DISABLED = 'CONTENT_PROTECTION_DISABLED';
  /**
   * Content protection is enabled and the user cannot change this.Supported on
   * Android 15 and above. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 15.
   */
  public const CONTENT_PROTECTION_POLICY_CONTENT_PROTECTION_ENFORCED = 'CONTENT_PROTECTION_ENFORCED';
  /**
   * Content protection is not controlled by the policy. The user is allowed to
   * choose the behavior of content protection.Supported on Android 15 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 15.
   */
  public const CONTENT_PROTECTION_POLICY_CONTENT_PROTECTION_USER_CHOICE = 'CONTENT_PROTECTION_USER_CHOICE';
  /**
   * Unspecified. Defaults to DEVELOPER_SETTINGS_DISABLED.
   */
  public const DEVELOPER_SETTINGS_DEVELOPER_SETTINGS_UNSPECIFIED = 'DEVELOPER_SETTINGS_UNSPECIFIED';
  /**
   * Default. Disables all developer settings and prevents the user from
   * accessing them.
   */
  public const DEVELOPER_SETTINGS_DEVELOPER_SETTINGS_DISABLED = 'DEVELOPER_SETTINGS_DISABLED';
  /**
   * Allows all developer settings. The user can access and optionally configure
   * the settings.
   */
  public const DEVELOPER_SETTINGS_DEVELOPER_SETTINGS_ALLOWED = 'DEVELOPER_SETTINGS_ALLOWED';
  /**
   * Unspecified. Defaults to VERIFY_APPS_ENFORCED.
   */
  public const GOOGLE_PLAY_PROTECT_VERIFY_APPS_GOOGLE_PLAY_PROTECT_VERIFY_APPS_UNSPECIFIED = 'GOOGLE_PLAY_PROTECT_VERIFY_APPS_UNSPECIFIED';
  /**
   * Default. Force-enables app verification.
   */
  public const GOOGLE_PLAY_PROTECT_VERIFY_APPS_VERIFY_APPS_ENFORCED = 'VERIFY_APPS_ENFORCED';
  /**
   * Allows the user to choose whether to enable app verification.
   */
  public const GOOGLE_PLAY_PROTECT_VERIFY_APPS_VERIFY_APPS_USER_CHOICE = 'VERIFY_APPS_USER_CHOICE';
  /**
   * Unspecified. Defaults to MTE_USER_CHOICE.
   */
  public const MTE_POLICY_MTE_POLICY_UNSPECIFIED = 'MTE_POLICY_UNSPECIFIED';
  /**
   * The user can choose to enable or disable MTE on the device if the device
   * supports this.
   */
  public const MTE_POLICY_MTE_USER_CHOICE = 'MTE_USER_CHOICE';
  /**
   * MTE is enabled on the device and the user is not allowed to change this
   * setting. This can be set on fully managed devices and work profiles on
   * company-owned devices. A NonComplianceDetail with MANAGEMENT_MODE is
   * reported for other management modes. A NonComplianceDetail with
   * DEVICE_INCOMPATIBLE is reported if the device does not support
   * MTE.Supported on Android 14 and above. A NonComplianceDetail with API_LEVEL
   * is reported if the Android version is less than 14.
   */
  public const MTE_POLICY_MTE_ENFORCED = 'MTE_ENFORCED';
  /**
   * MTE is disabled on the device and the user is not allowed to change this
   * setting. This applies only on fully managed devices. In other cases, a
   * NonComplianceDetail with MANAGEMENT_MODE is reported. A NonComplianceDetail
   * with DEVICE_INCOMPATIBLE is reported if the device does not support
   * MTE.Supported on Android 14 and above. A NonComplianceDetail with API_LEVEL
   * is reported if the Android version is less than 14.
   */
  public const MTE_POLICY_MTE_DISABLED = 'MTE_DISABLED';
  /**
   * Unspecified. Defaults to DISALLOW_INSTALL.
   */
  public const UNTRUSTED_APPS_POLICY_UNTRUSTED_APPS_POLICY_UNSPECIFIED = 'UNTRUSTED_APPS_POLICY_UNSPECIFIED';
  /**
   * Default. Disallow untrusted app installs on entire device.
   */
  public const UNTRUSTED_APPS_POLICY_DISALLOW_INSTALL = 'DISALLOW_INSTALL';
  /**
   * For devices with work profiles, allow untrusted app installs in the
   * device's personal profile only.
   */
  public const UNTRUSTED_APPS_POLICY_ALLOW_INSTALL_IN_PERSONAL_PROFILE_ONLY = 'ALLOW_INSTALL_IN_PERSONAL_PROFILE_ONLY';
  /**
   * Allow untrusted app installs on entire device.
   */
  public const UNTRUSTED_APPS_POLICY_ALLOW_INSTALL_DEVICE_WIDE = 'ALLOW_INSTALL_DEVICE_WIDE';
  protected $collection_key = 'personalAppsThatCanReadWorkNotifications';
  /**
   * Controls Common Criteria Mode—security standards defined in the Common
   * Criteria for Information Technology Security Evaluation
   * (https://www.commoncriteriaportal.org/) (CC). Enabling Common Criteria Mode
   * increases certain security components on a device, see CommonCriteriaMode
   * for details.Warning: Common Criteria Mode enforces a strict security model
   * typically only required for IT products used in national security systems
   * and other highly sensitive organizations. Standard device use may be
   * affected. Only enabled if required. If Common Criteria Mode is turned off
   * after being enabled previously, all user-configured Wi-Fi networks may be
   * lost and any enterprise-configured Wi-Fi networks that require user input
   * may need to be reconfigured.
   *
   * @var string
   */
  public $commonCriteriaMode;
  /**
   * Optional. Controls whether content protection, which scans for deceptive
   * apps, is enabled. This is supported on Android 15 and above.
   *
   * @var string
   */
  public $contentProtectionPolicy;
  /**
   * Controls access to developer settings: developer options and safe boot.
   * Replaces safeBootDisabled (deprecated) and debuggingFeaturesAllowed
   * (deprecated). On personally-owned devices with a work profile, setting this
   * policy will not disable safe boot. In this case, a NonComplianceDetail with
   * MANAGEMENT_MODE is reported.
   *
   * @var string
   */
  public $developerSettings;
  /**
   * Whether Google Play Protect verification
   * (https://support.google.com/accounts/answer/2812853) is enforced. Replaces
   * ensureVerifyAppsEnabled (deprecated).
   *
   * @var string
   */
  public $googlePlayProtectVerifyApps;
  /**
   * Optional. Controls Memory Tagging Extension (MTE)
   * (https://source.android.com/docs/security/test/memory-safety/arm-mte) on
   * the device. The device needs to be rebooted to apply changes to the MTE
   * policy. On Android 15 and above, a NonComplianceDetail with PENDING is
   * reported if the policy change is pending a device reboot.
   *
   * @var string
   */
  public $mtePolicy;
  /**
   * Personal apps that can read work profile notifications using a
   * NotificationListenerService (https://developer.android.com/reference/androi
   * d/service/notification/NotificationListenerService). By default, no
   * personal apps (aside from system apps) can read work notifications. Each
   * value in the list must be a package name.
   *
   * @var string[]
   */
  public $personalAppsThatCanReadWorkNotifications;
  /**
   * The policy for untrusted apps (apps from unknown sources) enforced on the
   * device. Replaces install_unknown_sources_allowed (deprecated).
   *
   * @var string
   */
  public $untrustedAppsPolicy;

  /**
   * Controls Common Criteria Mode—security standards defined in the Common
   * Criteria for Information Technology Security Evaluation
   * (https://www.commoncriteriaportal.org/) (CC). Enabling Common Criteria Mode
   * increases certain security components on a device, see CommonCriteriaMode
   * for details.Warning: Common Criteria Mode enforces a strict security model
   * typically only required for IT products used in national security systems
   * and other highly sensitive organizations. Standard device use may be
   * affected. Only enabled if required. If Common Criteria Mode is turned off
   * after being enabled previously, all user-configured Wi-Fi networks may be
   * lost and any enterprise-configured Wi-Fi networks that require user input
   * may need to be reconfigured.
   *
   * Accepted values: COMMON_CRITERIA_MODE_UNSPECIFIED,
   * COMMON_CRITERIA_MODE_DISABLED, COMMON_CRITERIA_MODE_ENABLED
   *
   * @param self::COMMON_CRITERIA_MODE_* $commonCriteriaMode
   */
  public function setCommonCriteriaMode($commonCriteriaMode)
  {
    $this->commonCriteriaMode = $commonCriteriaMode;
  }
  /**
   * @return self::COMMON_CRITERIA_MODE_*
   */
  public function getCommonCriteriaMode()
  {
    return $this->commonCriteriaMode;
  }
  /**
   * Optional. Controls whether content protection, which scans for deceptive
   * apps, is enabled. This is supported on Android 15 and above.
   *
   * Accepted values: CONTENT_PROTECTION_POLICY_UNSPECIFIED,
   * CONTENT_PROTECTION_DISABLED, CONTENT_PROTECTION_ENFORCED,
   * CONTENT_PROTECTION_USER_CHOICE
   *
   * @param self::CONTENT_PROTECTION_POLICY_* $contentProtectionPolicy
   */
  public function setContentProtectionPolicy($contentProtectionPolicy)
  {
    $this->contentProtectionPolicy = $contentProtectionPolicy;
  }
  /**
   * @return self::CONTENT_PROTECTION_POLICY_*
   */
  public function getContentProtectionPolicy()
  {
    return $this->contentProtectionPolicy;
  }
  /**
   * Controls access to developer settings: developer options and safe boot.
   * Replaces safeBootDisabled (deprecated) and debuggingFeaturesAllowed
   * (deprecated). On personally-owned devices with a work profile, setting this
   * policy will not disable safe boot. In this case, a NonComplianceDetail with
   * MANAGEMENT_MODE is reported.
   *
   * Accepted values: DEVELOPER_SETTINGS_UNSPECIFIED,
   * DEVELOPER_SETTINGS_DISABLED, DEVELOPER_SETTINGS_ALLOWED
   *
   * @param self::DEVELOPER_SETTINGS_* $developerSettings
   */
  public function setDeveloperSettings($developerSettings)
  {
    $this->developerSettings = $developerSettings;
  }
  /**
   * @return self::DEVELOPER_SETTINGS_*
   */
  public function getDeveloperSettings()
  {
    return $this->developerSettings;
  }
  /**
   * Whether Google Play Protect verification
   * (https://support.google.com/accounts/answer/2812853) is enforced. Replaces
   * ensureVerifyAppsEnabled (deprecated).
   *
   * Accepted values: GOOGLE_PLAY_PROTECT_VERIFY_APPS_UNSPECIFIED,
   * VERIFY_APPS_ENFORCED, VERIFY_APPS_USER_CHOICE
   *
   * @param self::GOOGLE_PLAY_PROTECT_VERIFY_APPS_* $googlePlayProtectVerifyApps
   */
  public function setGooglePlayProtectVerifyApps($googlePlayProtectVerifyApps)
  {
    $this->googlePlayProtectVerifyApps = $googlePlayProtectVerifyApps;
  }
  /**
   * @return self::GOOGLE_PLAY_PROTECT_VERIFY_APPS_*
   */
  public function getGooglePlayProtectVerifyApps()
  {
    return $this->googlePlayProtectVerifyApps;
  }
  /**
   * Optional. Controls Memory Tagging Extension (MTE)
   * (https://source.android.com/docs/security/test/memory-safety/arm-mte) on
   * the device. The device needs to be rebooted to apply changes to the MTE
   * policy. On Android 15 and above, a NonComplianceDetail with PENDING is
   * reported if the policy change is pending a device reboot.
   *
   * Accepted values: MTE_POLICY_UNSPECIFIED, MTE_USER_CHOICE, MTE_ENFORCED,
   * MTE_DISABLED
   *
   * @param self::MTE_POLICY_* $mtePolicy
   */
  public function setMtePolicy($mtePolicy)
  {
    $this->mtePolicy = $mtePolicy;
  }
  /**
   * @return self::MTE_POLICY_*
   */
  public function getMtePolicy()
  {
    return $this->mtePolicy;
  }
  /**
   * Personal apps that can read work profile notifications using a
   * NotificationListenerService (https://developer.android.com/reference/androi
   * d/service/notification/NotificationListenerService). By default, no
   * personal apps (aside from system apps) can read work notifications. Each
   * value in the list must be a package name.
   *
   * @param string[] $personalAppsThatCanReadWorkNotifications
   */
  public function setPersonalAppsThatCanReadWorkNotifications($personalAppsThatCanReadWorkNotifications)
  {
    $this->personalAppsThatCanReadWorkNotifications = $personalAppsThatCanReadWorkNotifications;
  }
  /**
   * @return string[]
   */
  public function getPersonalAppsThatCanReadWorkNotifications()
  {
    return $this->personalAppsThatCanReadWorkNotifications;
  }
  /**
   * The policy for untrusted apps (apps from unknown sources) enforced on the
   * device. Replaces install_unknown_sources_allowed (deprecated).
   *
   * Accepted values: UNTRUSTED_APPS_POLICY_UNSPECIFIED, DISALLOW_INSTALL,
   * ALLOW_INSTALL_IN_PERSONAL_PROFILE_ONLY, ALLOW_INSTALL_DEVICE_WIDE
   *
   * @param self::UNTRUSTED_APPS_POLICY_* $untrustedAppsPolicy
   */
  public function setUntrustedAppsPolicy($untrustedAppsPolicy)
  {
    $this->untrustedAppsPolicy = $untrustedAppsPolicy;
  }
  /**
   * @return self::UNTRUSTED_APPS_POLICY_*
   */
  public function getUntrustedAppsPolicy()
  {
    return $this->untrustedAppsPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvancedSecurityOverrides::class, 'Google_Service_AndroidManagement_AdvancedSecurityOverrides');
