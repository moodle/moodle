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

class ApplicationPolicy extends \Google\Collection
{
  /**
   * Unspecified. Defaults to VPN_LOCKDOWN_ENFORCED.
   */
  public const ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_UNSPECIFIED = 'ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_UNSPECIFIED';
  /**
   * The app respects the always-on VPN lockdown setting.
   */
  public const ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_VPN_LOCKDOWN_ENFORCED = 'VPN_LOCKDOWN_ENFORCED';
  /**
   * The app is exempt from the always-on VPN lockdown setting.
   */
  public const ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_VPN_LOCKDOWN_EXEMPTION = 'VPN_LOCKDOWN_EXEMPTION';
  /**
   * Unspecified. Defaults to AUTO_UPDATE_DEFAULT.
   */
  public const AUTO_UPDATE_MODE_AUTO_UPDATE_MODE_UNSPECIFIED = 'AUTO_UPDATE_MODE_UNSPECIFIED';
  /**
   * The default update mode.The app is automatically updated with low priority
   * to minimize the impact on the user.The app is updated when all of the
   * following constraints are met: The device is not actively used. The device
   * is connected to an unmetered network. The device is charging. The app to be
   * updated is not running in the foreground.The device is notified about a new
   * update within 24 hours after it is published by the developer, after which
   * the app is updated the next time the constraints above are met.
   */
  public const AUTO_UPDATE_MODE_AUTO_UPDATE_DEFAULT = 'AUTO_UPDATE_DEFAULT';
  /**
   * The app is not automatically updated for a maximum of 90 days after the app
   * becomes out of date.90 days after the app becomes out of date, the latest
   * available version is installed automatically with low priority (see
   * AUTO_UPDATE_DEFAULT). After the app is updated it is not automatically
   * updated again until 90 days after it becomes out of date again.The user can
   * still manually update the app from the Play Store at any time.
   */
  public const AUTO_UPDATE_MODE_AUTO_UPDATE_POSTPONED = 'AUTO_UPDATE_POSTPONED';
  /**
   * The app is updated as soon as possible. No constraints are applied.The
   * device is notified as soon as possible about a new update after it becomes
   * available.*NOTE:* Updates to apps with larger deployments across Android's
   * ecosystem can take up to 24h.
   */
  public const AUTO_UPDATE_MODE_AUTO_UPDATE_HIGH_PRIORITY = 'AUTO_UPDATE_HIGH_PRIORITY';
  /**
   * Unspecified. Defaults to CONNECTED_WORK_AND_PERSONAL_APPS_DISALLOWED.
   */
  public const CONNECTED_WORK_AND_PERSONAL_APP_CONNECTED_WORK_AND_PERSONAL_APP_UNSPECIFIED = 'CONNECTED_WORK_AND_PERSONAL_APP_UNSPECIFIED';
  /**
   * Default. Prevents the app from communicating cross-profile.
   */
  public const CONNECTED_WORK_AND_PERSONAL_APP_CONNECTED_WORK_AND_PERSONAL_APP_DISALLOWED = 'CONNECTED_WORK_AND_PERSONAL_APP_DISALLOWED';
  /**
   * Allows the app to communicate across profiles after receiving user consent.
   */
  public const CONNECTED_WORK_AND_PERSONAL_APP_CONNECTED_WORK_AND_PERSONAL_APP_ALLOWED = 'CONNECTED_WORK_AND_PERSONAL_APP_ALLOWED';
  /**
   * Unspecified. The behaviour is governed by credentialProviderPolicyDefault.
   */
  public const CREDENTIAL_PROVIDER_POLICY_CREDENTIAL_PROVIDER_POLICY_UNSPECIFIED = 'CREDENTIAL_PROVIDER_POLICY_UNSPECIFIED';
  /**
   * App is allowed to act as a credential provider.
   */
  public const CREDENTIAL_PROVIDER_POLICY_CREDENTIAL_PROVIDER_ALLOWED = 'CREDENTIAL_PROVIDER_ALLOWED';
  /**
   * Policy not specified. If no policy is specified for a permission at any
   * level, then the PROMPT behavior is used by default.
   */
  public const DEFAULT_PERMISSION_POLICY_PERMISSION_POLICY_UNSPECIFIED = 'PERMISSION_POLICY_UNSPECIFIED';
  /**
   * Prompt the user to grant a permission.
   */
  public const DEFAULT_PERMISSION_POLICY_PROMPT = 'PROMPT';
  /**
   * Automatically grant a permission.On Android 12 and above, READ_SMS (https:/
   * /developer.android.com/reference/android/Manifest.permission#READ_SMS) and
   * following sensor-related permissions can only be granted on fully managed
   * devices: ACCESS_FINE_LOCATION (https://developer.android.com/reference/andr
   * oid/Manifest.permission#ACCESS_FINE_LOCATION) ACCESS_BACKGROUND_LOCATION (h
   * ttps://developer.android.com/reference/android/Manifest.permission#ACCESS_B
   * ACKGROUND_LOCATION) ACCESS_COARSE_LOCATION (https://developer.android.com/r
   * eference/android/Manifest.permission#ACCESS_COARSE_LOCATION) CAMERA (https:
   * //developer.android.com/reference/android/Manifest.permission#CAMERA)
   * RECORD_AUDIO (https://developer.android.com/reference/android/Manifest.perm
   * ission#RECORD_AUDIO) ACTIVITY_RECOGNITION (https://developer.android.com/re
   * ference/android/Manifest.permission#ACTIVITY_RECOGNITION) BODY_SENSORS (htt
   * ps://developer.android.com/reference/android/Manifest.permission#BODY_SENSO
   * RS)
   */
  public const DEFAULT_PERMISSION_POLICY_GRANT = 'GRANT';
  /**
   * Automatically deny a permission.
   */
  public const DEFAULT_PERMISSION_POLICY_DENY = 'DENY';
  /**
   * Unspecified. Defaults to AVAILABLE.
   */
  public const INSTALL_TYPE_INSTALL_TYPE_UNSPECIFIED = 'INSTALL_TYPE_UNSPECIFIED';
  /**
   * The app is automatically installed and can be removed by the user.
   */
  public const INSTALL_TYPE_PREINSTALLED = 'PREINSTALLED';
  /**
   * The app is automatically installed regardless of a set maintenance window
   * and can't be removed by the user.
   */
  public const INSTALL_TYPE_FORCE_INSTALLED = 'FORCE_INSTALLED';
  /**
   * The app is blocked and can't be installed. If the app was installed under a
   * previous policy, it will be uninstalled. This also blocks its instant app
   * functionality.
   */
  public const INSTALL_TYPE_BLOCKED = 'BLOCKED';
  /**
   * The app is available to install.
   */
  public const INSTALL_TYPE_AVAILABLE = 'AVAILABLE';
  /**
   * The app is automatically installed and can't be removed by the user and
   * will prevent setup from completion until installation is complete.
   */
  public const INSTALL_TYPE_REQUIRED_FOR_SETUP = 'REQUIRED_FOR_SETUP';
  /**
   * The app is automatically installed in kiosk mode: it's set as the preferred
   * home intent and whitelisted for lock task mode. Device setup won't complete
   * until the app is installed. After installation, users won't be able to
   * remove the app. You can only set this installType for one app per policy.
   * When this is present in the policy, status bar will be automatically
   * disabled.If there is any app with KIOSK role, then this install type cannot
   * be set for any app.
   *
   * @deprecated
   */
  public const INSTALL_TYPE_KIOSK = 'KIOSK';
  /**
   * The app can only be installed and updated via AMAPI SDK command
   * (https://developers.google.com/android/management/extensibility-sdk-
   * integration).Note: This only affects fully managed devices. Play related
   * fields minimumVersionCode, accessibleTrackIds, autoUpdateMode,
   * installConstraint and installPriority cannot be set for the app. The app
   * isn't available in the Play Store. The app installed on the device has
   * applicationSource set to CUSTOM. When the current installType is CUSTOM,
   * the signing key certificate fingerprint of the existing custom app on the
   * device must match one of the entries in ApplicationPolicy.signingKeyCerts .
   * Otherwise, a NonComplianceDetail with APP_SIGNING_CERT_MISMATCH is
   * reported. Changing the installType from CUSTOM to another value must match
   * the playstore version of the application signing key certificate
   * fingerprint. Otherwise a NonComplianceDetail with APP_SIGNING_CERT_MISMATCH
   * is reported. Changing the installType to CUSTOM uninstalls the existing app
   * if its signing key certificate fingerprint of the installed app doesn't
   * match the one from the ApplicationPolicy.signingKeyCerts . Removing the app
   * from applications doesn't uninstall the existing app if it conforms to
   * playStoreMode. See also customAppConfig. This is different from the Google
   * Play Custom App Publishing
   * (https://developers.google.com/android/work/play/custom-app-api/get-
   * started) feature.
   */
  public const INSTALL_TYPE_CUSTOM = 'CUSTOM';
  /**
   * Whether this value is valid and what it means depends on where it is used,
   * and this is documented on the relevant fields.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_UNSPECIFIED = 'PREFERENTIAL_NETWORK_ID_UNSPECIFIED';
  /**
   * Application does not use any preferential network.
   */
  public const PREFERENTIAL_NETWORK_ID_NO_PREFERENTIAL_NETWORK = 'NO_PREFERENTIAL_NETWORK';
  /**
   * Preferential network identifier 1.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_ONE = 'PREFERENTIAL_NETWORK_ID_ONE';
  /**
   * Preferential network identifier 2.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_TWO = 'PREFERENTIAL_NETWORK_ID_TWO';
  /**
   * Preferential network identifier 3.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_THREE = 'PREFERENTIAL_NETWORK_ID_THREE';
  /**
   * Preferential network identifier 4.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FOUR = 'PREFERENTIAL_NETWORK_ID_FOUR';
  /**
   * Preferential network identifier 5.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FIVE = 'PREFERENTIAL_NETWORK_ID_FIVE';
  /**
   * Uses the default behaviour of the app to determine if user control is
   * allowed or disallowed. User control is allowed by default for most apps but
   * disallowed for following types of apps: extension apps (see extensionConfig
   * for more details) kiosk apps (see KIOSK install type for more details) apps
   * with roles set to a nonempty list other critical system apps
   */
  public const USER_CONTROL_SETTINGS_USER_CONTROL_SETTINGS_UNSPECIFIED = 'USER_CONTROL_SETTINGS_UNSPECIFIED';
  /**
   * User control is allowed for the app. Kiosk apps can use this to allow user
   * control. For extension apps (see extensionConfig for more details), user
   * control is disallowed even if this value is set.For apps with roles set to
   * a nonempty list (except roles containing only KIOSK role), this value
   * cannot be set.For kiosk apps (see KIOSK install type and KIOSK role type
   * for more details), this value can be used to allow user control.
   */
  public const USER_CONTROL_SETTINGS_USER_CONTROL_ALLOWED = 'USER_CONTROL_ALLOWED';
  /**
   * User control is disallowed for the app. This is supported on Android 11 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 11.
   */
  public const USER_CONTROL_SETTINGS_USER_CONTROL_DISALLOWED = 'USER_CONTROL_DISALLOWED';
  /**
   * Unspecified. Defaults to work_profile_widgets_default
   */
  public const WORK_PROFILE_WIDGETS_WORK_PROFILE_WIDGETS_UNSPECIFIED = 'WORK_PROFILE_WIDGETS_UNSPECIFIED';
  /**
   * Work profile widgets are allowed. This means the application will be able
   * to add widgets to the home screen.
   */
  public const WORK_PROFILE_WIDGETS_WORK_PROFILE_WIDGETS_ALLOWED = 'WORK_PROFILE_WIDGETS_ALLOWED';
  /**
   * Work profile widgets are disallowed. This means the application will not be
   * able to add widgets to the home screen.
   */
  public const WORK_PROFILE_WIDGETS_WORK_PROFILE_WIDGETS_DISALLOWED = 'WORK_PROFILE_WIDGETS_DISALLOWED';
  protected $collection_key = 'signingKeyCerts';
  /**
   * List of the app’s track IDs that a device belonging to the enterprise can
   * access. If the list contains multiple track IDs, devices receive the latest
   * version among all accessible tracks. If the list contains no track IDs,
   * devices only have access to the app’s production track. More details about
   * each track are available in AppTrackInfo.
   *
   * @var string[]
   */
  public $accessibleTrackIds;
  /**
   * Specifies whether the app is allowed networking when the VPN is not
   * connected and alwaysOnVpnPackage.lockdownEnabled is enabled. If set to
   * VPN_LOCKDOWN_ENFORCED, the app is not allowed networking, and if set to
   * VPN_LOCKDOWN_EXEMPTION, the app is allowed networking. Only supported on
   * devices running Android 10 and above. If this is not supported by the
   * device, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to API_LEVEL and a fieldPath. If this is not
   * applicable to the app, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to UNSUPPORTED and a fieldPath. The fieldPath is
   * set to applications[i].alwaysOnVpnLockdownExemption, where i is the index
   * of the package in the applications policy.
   *
   * @var string
   */
  public $alwaysOnVpnLockdownExemption;
  /**
   * Controls the auto-update mode for the app.
   *
   * @var string
   */
  public $autoUpdateMode;
  /**
   * Controls whether the app can communicate with itself across a device’s work
   * and personal profiles, subject to user consent.
   *
   * @var string
   */
  public $connectedWorkAndPersonalApp;
  /**
   * Optional. Whether the app is allowed to act as a credential provider on
   * Android 14 and above.
   *
   * @var string
   */
  public $credentialProviderPolicy;
  protected $customAppConfigType = CustomAppConfig::class;
  protected $customAppConfigDataType = '';
  /**
   * The default policy for all permissions requested by the app. If specified,
   * this overrides the policy-level default_permission_policy which applies to
   * all apps. It does not override the permission_grants which applies to all
   * apps.
   *
   * @var string
   */
  public $defaultPermissionPolicy;
  /**
   * The scopes delegated to the app from Android Device Policy. These provide
   * additional privileges for the applications they are applied to.
   *
   * @var string[]
   */
  public $delegatedScopes;
  /**
   * Whether the app is disabled. When disabled, the app data is still
   * preserved.
   *
   * @var bool
   */
  public $disabled;
  protected $extensionConfigType = ExtensionConfig::class;
  protected $extensionConfigDataType = '';
  protected $installConstraintType = InstallConstraint::class;
  protected $installConstraintDataType = 'array';
  /**
   * Optional. Amongst apps with installType set to: FORCE_INSTALLED
   * PREINSTALLEDthis controls the relative priority of installation. A value of
   * 0 (default) means this app has no priority over other apps. For values
   * between 1 and 10,000, a lower value means a higher priority. Values outside
   * of the range 0 to 10,000 inclusive are rejected.
   *
   * @var int
   */
  public $installPriority;
  /**
   * The type of installation to perform.
   *
   * @var string
   */
  public $installType;
  /**
   * Whether the app is allowed to lock itself in full-screen mode. DEPRECATED.
   * Use InstallType KIOSK or kioskCustomLauncherEnabled to configure a
   * dedicated device.
   *
   * @deprecated
   * @var bool
   */
  public $lockTaskAllowed;
  /**
   * Managed configuration applied to the app. The format for the configuration
   * is dictated by the ManagedProperty values supported by the app. Each field
   * name in the managed configuration must match the key field of the
   * ManagedProperty. The field value must be compatible with the type of the
   * ManagedProperty: *type* *JSON value* BOOL true or false STRING string
   * INTEGER number CHOICE string MULTISELECT array of strings HIDDEN string
   * BUNDLE_ARRAY array of objects
   *
   * @var array[]
   */
  public $managedConfiguration;
  protected $managedConfigurationTemplateType = ManagedConfigurationTemplate::class;
  protected $managedConfigurationTemplateDataType = '';
  /**
   * The minimum version of the app that runs on the device. If set, the device
   * attempts to update the app to at least this version code. If the app is not
   * up-to-date, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to APP_NOT_UPDATED. The app must already be
   * published to Google Play with a version code greater than or equal to this
   * value. At most 20 apps may specify a minimum version code per policy.
   *
   * @var int
   */
  public $minimumVersionCode;
  /**
   * The package name of the app. For example, com.google.android.youtube for
   * the YouTube app.
   *
   * @var string
   */
  public $packageName;
  protected $permissionGrantsType = PermissionGrant::class;
  protected $permissionGrantsDataType = 'array';
  /**
   * Optional. ID of the preferential network the application uses. There must
   * be a configuration for the specified network ID in
   * preferentialNetworkServiceConfigs. If set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED, the application will use the default
   * network ID specified in defaultPreferentialNetworkId. See the documentation
   * of defaultPreferentialNetworkId for the list of apps excluded from this
   * defaulting. This applies on both work profiles and fully managed devices on
   * Android 13 and above.
   *
   * @var string
   */
  public $preferentialNetworkId;
  protected $rolesType = Role::class;
  protected $rolesDataType = 'array';
  protected $signingKeyCertsType = ApplicationSigningKeyCert::class;
  protected $signingKeyCertsDataType = 'array';
  /**
   * Optional. Specifies whether user control is permitted for the app. User
   * control includes user actions like force-stopping and clearing app data.
   * Certain types of apps have special treatment, see
   * USER_CONTROL_SETTINGS_UNSPECIFIED and USER_CONTROL_ALLOWED for more
   * details.
   *
   * @var string
   */
  public $userControlSettings;
  /**
   * Specifies whether the app installed in the work profile is allowed to add
   * widgets to the home screen.
   *
   * @var string
   */
  public $workProfileWidgets;

  /**
   * List of the app’s track IDs that a device belonging to the enterprise can
   * access. If the list contains multiple track IDs, devices receive the latest
   * version among all accessible tracks. If the list contains no track IDs,
   * devices only have access to the app’s production track. More details about
   * each track are available in AppTrackInfo.
   *
   * @param string[] $accessibleTrackIds
   */
  public function setAccessibleTrackIds($accessibleTrackIds)
  {
    $this->accessibleTrackIds = $accessibleTrackIds;
  }
  /**
   * @return string[]
   */
  public function getAccessibleTrackIds()
  {
    return $this->accessibleTrackIds;
  }
  /**
   * Specifies whether the app is allowed networking when the VPN is not
   * connected and alwaysOnVpnPackage.lockdownEnabled is enabled. If set to
   * VPN_LOCKDOWN_ENFORCED, the app is not allowed networking, and if set to
   * VPN_LOCKDOWN_EXEMPTION, the app is allowed networking. Only supported on
   * devices running Android 10 and above. If this is not supported by the
   * device, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to API_LEVEL and a fieldPath. If this is not
   * applicable to the app, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to UNSUPPORTED and a fieldPath. The fieldPath is
   * set to applications[i].alwaysOnVpnLockdownExemption, where i is the index
   * of the package in the applications policy.
   *
   * Accepted values: ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_UNSPECIFIED,
   * VPN_LOCKDOWN_ENFORCED, VPN_LOCKDOWN_EXEMPTION
   *
   * @param self::ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_* $alwaysOnVpnLockdownExemption
   */
  public function setAlwaysOnVpnLockdownExemption($alwaysOnVpnLockdownExemption)
  {
    $this->alwaysOnVpnLockdownExemption = $alwaysOnVpnLockdownExemption;
  }
  /**
   * @return self::ALWAYS_ON_VPN_LOCKDOWN_EXEMPTION_*
   */
  public function getAlwaysOnVpnLockdownExemption()
  {
    return $this->alwaysOnVpnLockdownExemption;
  }
  /**
   * Controls the auto-update mode for the app.
   *
   * Accepted values: AUTO_UPDATE_MODE_UNSPECIFIED, AUTO_UPDATE_DEFAULT,
   * AUTO_UPDATE_POSTPONED, AUTO_UPDATE_HIGH_PRIORITY
   *
   * @param self::AUTO_UPDATE_MODE_* $autoUpdateMode
   */
  public function setAutoUpdateMode($autoUpdateMode)
  {
    $this->autoUpdateMode = $autoUpdateMode;
  }
  /**
   * @return self::AUTO_UPDATE_MODE_*
   */
  public function getAutoUpdateMode()
  {
    return $this->autoUpdateMode;
  }
  /**
   * Controls whether the app can communicate with itself across a device’s work
   * and personal profiles, subject to user consent.
   *
   * Accepted values: CONNECTED_WORK_AND_PERSONAL_APP_UNSPECIFIED,
   * CONNECTED_WORK_AND_PERSONAL_APP_DISALLOWED,
   * CONNECTED_WORK_AND_PERSONAL_APP_ALLOWED
   *
   * @param self::CONNECTED_WORK_AND_PERSONAL_APP_* $connectedWorkAndPersonalApp
   */
  public function setConnectedWorkAndPersonalApp($connectedWorkAndPersonalApp)
  {
    $this->connectedWorkAndPersonalApp = $connectedWorkAndPersonalApp;
  }
  /**
   * @return self::CONNECTED_WORK_AND_PERSONAL_APP_*
   */
  public function getConnectedWorkAndPersonalApp()
  {
    return $this->connectedWorkAndPersonalApp;
  }
  /**
   * Optional. Whether the app is allowed to act as a credential provider on
   * Android 14 and above.
   *
   * Accepted values: CREDENTIAL_PROVIDER_POLICY_UNSPECIFIED,
   * CREDENTIAL_PROVIDER_ALLOWED
   *
   * @param self::CREDENTIAL_PROVIDER_POLICY_* $credentialProviderPolicy
   */
  public function setCredentialProviderPolicy($credentialProviderPolicy)
  {
    $this->credentialProviderPolicy = $credentialProviderPolicy;
  }
  /**
   * @return self::CREDENTIAL_PROVIDER_POLICY_*
   */
  public function getCredentialProviderPolicy()
  {
    return $this->credentialProviderPolicy;
  }
  /**
   * Optional. Configuration for this custom app.install_type must be set to
   * CUSTOM for this to be set.
   *
   * @param CustomAppConfig $customAppConfig
   */
  public function setCustomAppConfig(CustomAppConfig $customAppConfig)
  {
    $this->customAppConfig = $customAppConfig;
  }
  /**
   * @return CustomAppConfig
   */
  public function getCustomAppConfig()
  {
    return $this->customAppConfig;
  }
  /**
   * The default policy for all permissions requested by the app. If specified,
   * this overrides the policy-level default_permission_policy which applies to
   * all apps. It does not override the permission_grants which applies to all
   * apps.
   *
   * Accepted values: PERMISSION_POLICY_UNSPECIFIED, PROMPT, GRANT, DENY
   *
   * @param self::DEFAULT_PERMISSION_POLICY_* $defaultPermissionPolicy
   */
  public function setDefaultPermissionPolicy($defaultPermissionPolicy)
  {
    $this->defaultPermissionPolicy = $defaultPermissionPolicy;
  }
  /**
   * @return self::DEFAULT_PERMISSION_POLICY_*
   */
  public function getDefaultPermissionPolicy()
  {
    return $this->defaultPermissionPolicy;
  }
  /**
   * The scopes delegated to the app from Android Device Policy. These provide
   * additional privileges for the applications they are applied to.
   *
   * @param string[] $delegatedScopes
   */
  public function setDelegatedScopes($delegatedScopes)
  {
    $this->delegatedScopes = $delegatedScopes;
  }
  /**
   * @return string[]
   */
  public function getDelegatedScopes()
  {
    return $this->delegatedScopes;
  }
  /**
   * Whether the app is disabled. When disabled, the app data is still
   * preserved.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Configuration to enable this app as an extension app, with the capability
   * of interacting with Android Device Policy offline.This field can be set for
   * at most one app. If there is any app with COMPANION_APP role, this field
   * cannot be set.The signing key certificate fingerprint of the app on the
   * device must match one of the entries in ApplicationPolicy.signingKeyCerts
   * or ExtensionConfig.signingKeyFingerprintsSha256 (deprecated) or the signing
   * key certificate fingerprints obtained from Play Store for the app to be
   * able to communicate with Android Device Policy. If the app is not on Play
   * Store and if ApplicationPolicy.signingKeyCerts and
   * ExtensionConfig.signingKeyFingerprintsSha256 (deprecated) are not set, a
   * NonComplianceDetail with INVALID_VALUE is reported.
   *
   * @deprecated
   * @param ExtensionConfig $extensionConfig
   */
  public function setExtensionConfig(ExtensionConfig $extensionConfig)
  {
    $this->extensionConfig = $extensionConfig;
  }
  /**
   * @deprecated
   * @return ExtensionConfig
   */
  public function getExtensionConfig()
  {
    return $this->extensionConfig;
  }
  /**
   * Optional. The constraints for installing the app. You can specify a maximum
   * of one InstallConstraint. Multiple constraints are rejected.
   *
   * @param InstallConstraint[] $installConstraint
   */
  public function setInstallConstraint($installConstraint)
  {
    $this->installConstraint = $installConstraint;
  }
  /**
   * @return InstallConstraint[]
   */
  public function getInstallConstraint()
  {
    return $this->installConstraint;
  }
  /**
   * Optional. Amongst apps with installType set to: FORCE_INSTALLED
   * PREINSTALLEDthis controls the relative priority of installation. A value of
   * 0 (default) means this app has no priority over other apps. For values
   * between 1 and 10,000, a lower value means a higher priority. Values outside
   * of the range 0 to 10,000 inclusive are rejected.
   *
   * @param int $installPriority
   */
  public function setInstallPriority($installPriority)
  {
    $this->installPriority = $installPriority;
  }
  /**
   * @return int
   */
  public function getInstallPriority()
  {
    return $this->installPriority;
  }
  /**
   * The type of installation to perform.
   *
   * Accepted values: INSTALL_TYPE_UNSPECIFIED, PREINSTALLED, FORCE_INSTALLED,
   * BLOCKED, AVAILABLE, REQUIRED_FOR_SETUP, KIOSK, CUSTOM
   *
   * @param self::INSTALL_TYPE_* $installType
   */
  public function setInstallType($installType)
  {
    $this->installType = $installType;
  }
  /**
   * @return self::INSTALL_TYPE_*
   */
  public function getInstallType()
  {
    return $this->installType;
  }
  /**
   * Whether the app is allowed to lock itself in full-screen mode. DEPRECATED.
   * Use InstallType KIOSK or kioskCustomLauncherEnabled to configure a
   * dedicated device.
   *
   * @deprecated
   * @param bool $lockTaskAllowed
   */
  public function setLockTaskAllowed($lockTaskAllowed)
  {
    $this->lockTaskAllowed = $lockTaskAllowed;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getLockTaskAllowed()
  {
    return $this->lockTaskAllowed;
  }
  /**
   * Managed configuration applied to the app. The format for the configuration
   * is dictated by the ManagedProperty values supported by the app. Each field
   * name in the managed configuration must match the key field of the
   * ManagedProperty. The field value must be compatible with the type of the
   * ManagedProperty: *type* *JSON value* BOOL true or false STRING string
   * INTEGER number CHOICE string MULTISELECT array of strings HIDDEN string
   * BUNDLE_ARRAY array of objects
   *
   * @param array[] $managedConfiguration
   */
  public function setManagedConfiguration($managedConfiguration)
  {
    $this->managedConfiguration = $managedConfiguration;
  }
  /**
   * @return array[]
   */
  public function getManagedConfiguration()
  {
    return $this->managedConfiguration;
  }
  /**
   * The managed configurations template for the app, saved from the managed
   * configurations iframe. This field is ignored if managed_configuration is
   * set.
   *
   * @param ManagedConfigurationTemplate $managedConfigurationTemplate
   */
  public function setManagedConfigurationTemplate(ManagedConfigurationTemplate $managedConfigurationTemplate)
  {
    $this->managedConfigurationTemplate = $managedConfigurationTemplate;
  }
  /**
   * @return ManagedConfigurationTemplate
   */
  public function getManagedConfigurationTemplate()
  {
    return $this->managedConfigurationTemplate;
  }
  /**
   * The minimum version of the app that runs on the device. If set, the device
   * attempts to update the app to at least this version code. If the app is not
   * up-to-date, the device will contain a NonComplianceDetail with
   * non_compliance_reason set to APP_NOT_UPDATED. The app must already be
   * published to Google Play with a version code greater than or equal to this
   * value. At most 20 apps may specify a minimum version code per policy.
   *
   * @param int $minimumVersionCode
   */
  public function setMinimumVersionCode($minimumVersionCode)
  {
    $this->minimumVersionCode = $minimumVersionCode;
  }
  /**
   * @return int
   */
  public function getMinimumVersionCode()
  {
    return $this->minimumVersionCode;
  }
  /**
   * The package name of the app. For example, com.google.android.youtube for
   * the YouTube app.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Explicit permission grants or denials for the app. These values override
   * the default_permission_policy and permission_grants which apply to all
   * apps.
   *
   * @param PermissionGrant[] $permissionGrants
   */
  public function setPermissionGrants($permissionGrants)
  {
    $this->permissionGrants = $permissionGrants;
  }
  /**
   * @return PermissionGrant[]
   */
  public function getPermissionGrants()
  {
    return $this->permissionGrants;
  }
  /**
   * Optional. ID of the preferential network the application uses. There must
   * be a configuration for the specified network ID in
   * preferentialNetworkServiceConfigs. If set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED, the application will use the default
   * network ID specified in defaultPreferentialNetworkId. See the documentation
   * of defaultPreferentialNetworkId for the list of apps excluded from this
   * defaulting. This applies on both work profiles and fully managed devices on
   * Android 13 and above.
   *
   * Accepted values: PREFERENTIAL_NETWORK_ID_UNSPECIFIED,
   * NO_PREFERENTIAL_NETWORK, PREFERENTIAL_NETWORK_ID_ONE,
   * PREFERENTIAL_NETWORK_ID_TWO, PREFERENTIAL_NETWORK_ID_THREE,
   * PREFERENTIAL_NETWORK_ID_FOUR, PREFERENTIAL_NETWORK_ID_FIVE
   *
   * @param self::PREFERENTIAL_NETWORK_ID_* $preferentialNetworkId
   */
  public function setPreferentialNetworkId($preferentialNetworkId)
  {
    $this->preferentialNetworkId = $preferentialNetworkId;
  }
  /**
   * @return self::PREFERENTIAL_NETWORK_ID_*
   */
  public function getPreferentialNetworkId()
  {
    return $this->preferentialNetworkId;
  }
  /**
   * Optional. Roles the app has.Apps having certain roles can be exempted from
   * power and background execution restrictions, suspension and hibernation on
   * Android 14 and above. The user control can also be disallowed for apps with
   * certain roles on Android 11 and above. Refer to the documentation of each
   * RoleType for more details.The app is notified about the roles that are set
   * for it if the app has a notification receiver service with . The app is
   * notified whenever its roles are updated or after the app is installed when
   * it has nonempty list of roles. The app can use this notification to
   * bootstrap itself after the installation. See Integrate with the AMAPI SDK
   * (https://developers.google.com/android/management/sdk-integration) and
   * Manage app roles (https://developers.google.com/android/management/app-
   * roles) guides for more details on the requirements for the service.For the
   * exemptions to be applied and the app to be notified about the roles, the
   * signing key certificate fingerprint of the app on the device must match one
   * of the signing key certificate fingerprints obtained from Play Store or one
   * of the entries in ApplicationPolicy.signingKeyCerts. Otherwise, a
   * NonComplianceDetail with APP_SIGNING_CERT_MISMATCH is reported.There must
   * not be duplicate roles with the same roleType. Multiple apps cannot hold a
   * role with the same roleType. A role with type ROLE_TYPE_UNSPECIFIED is not
   * allowed.
   *
   * @param Role[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return Role[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
  /**
   * Optional. Signing key certificates of the app.This field is required in the
   * following cases: The app has installType set to CUSTOM (i.e. a custom app).
   * The app has roles set to a nonempty list and the app does not exist on the
   * Play Store. The app has extensionConfig set (i.e. an extension app) but
   * ExtensionConfig.signingKeyFingerprintsSha256 (deprecated) is not set and
   * the app does not exist on the Play Store.If this field is not set for a
   * custom app, the policy is rejected. If it is not set when required for a
   * non-custom app, a NonComplianceDetail with INVALID_VALUE is reported.For
   * other cases, this field is optional and the signing key certificates
   * obtained from Play Store are used.See following policy settings to see how
   * this field is used: choosePrivateKeyRules
   * ApplicationPolicy.InstallType.CUSTOM ApplicationPolicy.extensionConfig
   * ApplicationPolicy.roles
   *
   * @param ApplicationSigningKeyCert[] $signingKeyCerts
   */
  public function setSigningKeyCerts($signingKeyCerts)
  {
    $this->signingKeyCerts = $signingKeyCerts;
  }
  /**
   * @return ApplicationSigningKeyCert[]
   */
  public function getSigningKeyCerts()
  {
    return $this->signingKeyCerts;
  }
  /**
   * Optional. Specifies whether user control is permitted for the app. User
   * control includes user actions like force-stopping and clearing app data.
   * Certain types of apps have special treatment, see
   * USER_CONTROL_SETTINGS_UNSPECIFIED and USER_CONTROL_ALLOWED for more
   * details.
   *
   * Accepted values: USER_CONTROL_SETTINGS_UNSPECIFIED, USER_CONTROL_ALLOWED,
   * USER_CONTROL_DISALLOWED
   *
   * @param self::USER_CONTROL_SETTINGS_* $userControlSettings
   */
  public function setUserControlSettings($userControlSettings)
  {
    $this->userControlSettings = $userControlSettings;
  }
  /**
   * @return self::USER_CONTROL_SETTINGS_*
   */
  public function getUserControlSettings()
  {
    return $this->userControlSettings;
  }
  /**
   * Specifies whether the app installed in the work profile is allowed to add
   * widgets to the home screen.
   *
   * Accepted values: WORK_PROFILE_WIDGETS_UNSPECIFIED,
   * WORK_PROFILE_WIDGETS_ALLOWED, WORK_PROFILE_WIDGETS_DISALLOWED
   *
   * @param self::WORK_PROFILE_WIDGETS_* $workProfileWidgets
   */
  public function setWorkProfileWidgets($workProfileWidgets)
  {
    $this->workProfileWidgets = $workProfileWidgets;
  }
  /**
   * @return self::WORK_PROFILE_WIDGETS_*
   */
  public function getWorkProfileWidgets()
  {
    return $this->workProfileWidgets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationPolicy::class, 'Google_Service_AndroidManagement_ApplicationPolicy');
